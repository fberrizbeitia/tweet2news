<?php
include_once ("JSearchString.php");
include_once ("PorterStemmer.php");
require_once ("EasyRdf.php");

class ClodGraph {
	
	var $uri;
	var $triplets;
	var $term;
	var $context;
	
	function getOutlinks(){
		return $this->getOutlinksFrom($this->triplets);
	}
	
	private function getOutlinksFrom($triplets){
		$sameAs = $this->getFrom($triplets,null,"http://www.w3.org/2002/07/owl#sameAs",null,null);
		$result = array();
		foreach($sameAs as $triplet){
			if(stristr($triplet[2]['value'],"dbpedia") === false){
				array_push($result,$triplet);			
			}
		}
		return $result;
	}
	
	
	function parse($decode){
		$this->triplets = $this->parseTo($decode);
	}
	
	private function parseTo($decode){
		$triplets = array();
		$keys = array_keys($decode);
		foreach ($keys as $key){	
			$subject = $key;
			$item = $decode[$key];
			$itemKeys = array_keys($item);
			foreach ($itemKeys as $itemK){
				$predicate = $itemK;
				$objKeys = array_keys($item[$itemK]);
				$objects = $item[$itemK];
				foreach ($objKeys as $objK){
					$object = $objects[$objK];
					$triplet = array($subject,$predicate,$object);
					array_push($triplets,$triplet);
				}
			}
		}
		
	return $triplets;
	}
	
	
	function load($Inputuri){
		$graph = new EasyRdf_Graph();
		$graph->load($Inputuri);
		$output = $graph->serialise('php');
		$this->parse ($output);
		$this->uri = $Inputuri;
	}
	
	private function match ($triplet,$subject = null, $predicate = null,$object = null, $lang = null){
		$result = true;

		if($subject !== null){
			if(strcasecmp($triplet[0],$subject) != 0){
				$result = false;
			}
		}
		
		if($predicate !== null and $result){
			if(strcasecmp($triplet[1],$predicate) != 0){
				$result = false;
			}
		}
		
		if($object !== null and $result){
			if(strcasecmp($triplet[2]['value'],$object) != 0){
				$result = false;
			}
		}
		
		if($lang !== null and $result){
			if(isset($triplet[2]['lang'])){
				if(strcasecmp($triplet[2]['lang'],$lang) != 0){
					$result = false;
				}
			}else{
				$result = false;
				}
		}
		
		return $result;
	}
	
	function get($subject = null, $predicate = null,$object = null, $lang = null){	
		return $this->getFrom($this->triplets,$subject,$predicate,$object,$lang);	
	}
	
	private function getFrom($triplets,$subject = null, $predicate = null,$object = null, $lang = null){	
		$result = array();
		
		for($i = 0; $i < count($triplets); $i++){
			$triplet = $this->triplets[$i];
			if($this->match($triplet,$subject,$predicate,$object,$lang)){
				array_push($result,$triplet);
			}	
		}
		
		return $result;
		
	}
	
	private function cleanAndStem($text){
		$result = array();
		$objSS = new jSearchString();
		$wordArray = explode(" ",$objSS->parseString($text)); 
		foreach ($wordArray as $word){
			array_push($result,PorterStemmer::Stem(trim($word)));
		}
		return $result;
		
	}
	
	private function getRelevance($text){
		//Remove the term from the context to get beter results.	
		$common = array_uintersect($this->cleanAndStem($this->context),$this->cleanAndStem($text),"strcasecmp");
		return (count($common));
		
	}
	
	function disambiguates(){
		//First check if it's a disambiguation page
		$resultA = $this->get(null,"http://dbpedia.org/ontology/abstract",null,'en');
		$resultD = $this->get(null,"http://dbpedia.org/ontology/wikiPageDisambiguates",null,null);

		if( (count($resultD) > 0) and (count($resultA) == 0) ){
			//echo("<br> -- vamos a desaukmbiguar -- <br>");
			// get the URI a see witch one fit best in the context and the reload the URI
			$best  = array("",-1);
			foreach ($resultD as $candidate){
				//$JSONuri = str_ireplace("resource","data",$candidate[2]['value']).".json";
				$JSONuri = str_ireplace("resource","data",$candidate[2]['value']);
				$this->load($JSONuri);
				$abstract = $this->get($candidate[2]['value'],"http://dbpedia.org/ontology/abstract",null,'en');
				$relevance = 0;
				if(count($abstract)> 0){
					$relevance = $this->getRelevance($abstract[0][2]['value']); 
				} //if(count($abstract)> 0){
				if($relevance > $best[1]){
					$best[0] = $JSONuri;
					$best[1] = $relevance;
				} //if($relevance > $best[1]){	
			} //foreach ($resultD as $candidate){
			//echo ("<br> --$best[0]--<br>");
			$this->load($best[0]);
		}//if( (count($resultD) > 0) and (count($resultA) == 0) ){	
	} // function disambiguates($context){
	
	private function isValidPredicate($triplet){
		$result = false;
		if(stristr($triplet[1],"http://") !== false ){
			$result = true;
		}
		
		return $result;
	}
	
	
	private function addTriplet($triplet){
		if($this->isValidPredicate($triplet)){
			if(count($this->get(null,$triplet[1],null,null))){
				array_push($this->triplets,$triplet);
			}
		}		
	}

	function enrich(){
		$outlinks = $this->getOutlinks();
		$graph = new EasyRdf_Graph();	
		foreach ($outlinks as $outlink){
			$uri=$outlink[2]['value'];
			var_dump($uri);
			try{
				@$graph->load($uri);
				$output = $graph->serialise('php');
				//add the new triplet
				$dereferencedTriplets = $this->parseTo($output);
				foreach($dereferencedTriplets as $triplet){
					$this->addTriplet($triplet);
				}			

			}catch (Exception $e) {
				echo("no se pudo cargar el uri por ".$e->getMessage()."<br>" );
			}
	
		}
	}
	
	function getType(){
		$type = "concept";
		
		$typeTriplets = $this->get(null,"http://www.w3.org/1999/02/22-rdf-syntax-ns#type",null,null);
		
		foreach($typeTriplets as $triplet){
			if(strcasecmp($triplet[2]['value'],"http://schema.org/Person") == 0){
				$type = "Person";
				return $type;
			}
			if(strcasecmp($triplet[2]['value'],"http://dbpedia.org/ontology/Place") == 0){
				$type = "Place";
				return $type;
			}
			
			if(strcasecmp($triplet[2]['value'],"http://schema.org/Organization") == 0){
				$type = "Organization";
				return $type;
			}
		}
		
		return $type;
	}

}

?>