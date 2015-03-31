<?php
require_once ("JSearchString.php");
require_once ("ClodGraph.php");
require_once ("rNews/newsItem.php");
require_once ("rNews/geocoordinate.php");
require_once ("tuit.php");
require_once ("twitter.php");

class ClodArticle {
	
	var $rNewsArticle = null;
	
	function getDBpediaURI($term){
		$format = 'json';
		$query = "SELECT ?uri ?label
		WHERE {
		?uri rdfs:label ?label .
		filter(?label='".$term."'@en)
		}";	
		
		$searchUrl = 'http://dbpedia.org/sparql?'
			  .'query='.urlencode($query)
			  .'&format='.$format;
	
	   return $searchUrl;
	
	}
			
	function request($url){
	 
	   // is curl installed?
	   if (!function_exists('curl_init')){ 
		  die('CURL is not installed!');
	   }
	   
	   // get curl handle
	   $ch= curl_init();
	
	   // set request url
	   curl_setopt($ch, 
		  CURLOPT_URL, 
		  $url);
	
	   // return response, don't print/echo
	   curl_setopt($ch, 
		  CURLOPT_RETURNTRANSFER, 
		  true);
	 
	   /*
	   Here you find more options for curl:
	   http://www.php.net/curl_setopt
	   */    
	
	   $response = curl_exec($ch);
	   
	   curl_close($ch);
	   
	   return $response;
	}
	
	function isValid($term){
	
	}
	
	function create(Tuit $tuit){
		//gather the tweet and autor information
		
//		$this->rNewsArticle = new newsItem($headline,$provider,$dateCreated,$datePublished,$thumbnailUrl,$dateline);
		
		$this->rNewsArticle = new newsItem($tuit->texto,"Twitter",$tuit->creado,date("Y-m-d"),$tuit->media,$tuit->lugar,$tuit->idTuit);
		$objTwitter = new twitter();
		$query = "https://api.twitter.com/1.1/users/show.json?user_id=".$tuit->emisor;
		$json = $objTwitter->query($query);
		$decode = json_decode($json, true); //getting the file content as array
		
		//$this->rNewsArticle->Addcreator($name,$description, $image, $url, $addInfoUri,$givenName, $additionalName = null, $familyName = null, $address = null, $honorificPrefix = null, $honorificSuffix = null);
		$url = "http://twitter.com/".$decode["screen_name"];
		
		$this->rNewsArticle->Addcreator("@".$decode["screen_name"],$decode["description"], $decode["profile_image_url"], $url,$decode["url"],$decode["name"], $additionalName = null, $familyName = null, $address = null, $honorificPrefix = null, $honorificSuffix = null);
		
		 
	}
	
	function save(){
		$this->rNewsArticle->save();
	}
	
	function annotate($termArray,$tweet){
		
		foreach($termArray as $term ){
			
			$objSS = new jSearchString();
			
			if(!$objSS->isStopword($term[2])){
				
				var_dump($term[2]);			
				$requestURL = $this->getDBpediaURI($term[2]);
				//var_dump($requestURL);
				$responseArray = json_decode($this->request($requestURL),true); 
				// analize the json
				
				$graph = new ClodGraph(); 
				foreach($responseArray["results"]["bindings"] as $candidate){
					$uri = $candidate["uri"]["value"];
					if(stristr($uri,"http://dbpedia.org/resource/") !== false){
						// I'm only interested in the dbpedia/resourse uri for the moment
						if(stristr($uri,"Category:") == false){
						// not interested in categories
						// now lets see what's in the uri		
							
							$graph->load($uri);
							$graph->context = $tweet;
							$graph->term = $term[2];
							$graph->disambiguates();
							$type = $graph->getType();
							
							$name = $term[2];
							if(count($name) == 0){$name = array(array(null,null,array('value'=>null,'lang'=>null)));}
							
							$description= $graph->get(null,"http://dbpedia.org/ontology/abstract",null,"en");
							if(count($description) == 0){$description = array(array(null,null,array('value'=>null,'lang'=>null)));}
							$image = $graph->get(null,"http://xmlns.com/foaf/0.1/depiction",null,null);
							if(count($image)== 0){$image = array(array(null,null,array('value'=>null,'lang'=>null)));}
							
							$url = $graph->get(null,"http://xmlns.com/foaf/0.1/isPrimaryTopicOf",null,null);
							if(count($url)== 0){$url = array(array(null,null,array('value'=>null,'lang'=>null)));}
							
							if($type == 'Person'){
								
								
								$givenName = $graph->get(null,"http://xmlns.com/foaf/0.1/givenName",null,null);
								if(count($givenName)== 0){$givenName = array(array(null,null,array('value'=>null,'lang'=>null)));}
								$familyName = $graph->get(null,"http://xmlns.com/foaf/0.1/surname",null,null);
								if(count($familyName) == 0){$familyName = array(array(null,null,array('value'=>null,'lang'=>null)));}
								
								$this->rNewsArticle->addPerson('about', $graph->uri,$name,$description[0][2]['value'], $image[0][2]['value'], $url[0][2]['value'], $addInfoUri = null,$givenName[0][2]['value'], $additionalName = null, $familyName[0][2]['value'], $address = null, $honorificPrefix = null, $honorificSuffix = null);
								
							}elseif($type == 'Place'){
								$point = $graph->get(null,"http://www.georss.org/georss/point",null,null);
								$objGeo = null;
								if(count($point) > 0){
									$objGeo = new geoCoordinate($point[0][2]['value'], $line = null, $polygon = null, $box = null, $circle = null, $elevation = null);
								}
								
								$this->rNewsArticle->addPlace('about',$graph->uri, $name,$description[0][2]['value'], $image[0][2]['value'], $url[0][2]['value'], $addInfoUri = null,$address = null, $objGeo, $featureCode = null);
								
							}elseif($type == 'Organization'){
								$this->rNewsArticle->addOrganization('about',$graph->uri, $name,$description[0][2]['value'], $image[0][2]['value'], $url[0][2]['value'],$addInfoUri = null,$tickerSymbol = null, $address = null);
								
							}else{ // add the concept
								
								$this->rNewsArticle->addConcept('about',$graph->uri, $name,$description[0][2]['value'], $image[0][2]['value'], $url[0][2]['value'], $addInfoUri = null);
							}
							
							
						}//if(stristr($uri,"Category:") == false){
					}
				}	//foreach($responseArray["results"]["bindings"] as $candidate){	
			}//if(!jSearchString::isStopword($term[2])){
		}	//foreach($BG_wikipedia_list as $term ){
	}//function getSemantics($termArray){
	
	
	
}

?>