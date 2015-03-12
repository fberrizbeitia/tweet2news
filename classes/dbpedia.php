<?php
require_once ("JSearchString.php");
require_once ("dbPediaJSON.php");

class dbpedia {
	
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
	
	function getSemantics($termArray){
		
		foreach($termArray as $term ){
			
			$objSS = new jSearchString();
			
			if(!$objSS->isStopword($term[2])){
			
				$requestURL = $this->getDBpediaURI($term[2]);
				//var_dump($requestURL);
				$responseArray = json_decode($this->request($requestURL),true); 
				// analize the json
				
				$objDBJSON = new dbPediaJSON();
				
				foreach($responseArray["results"]["bindings"] as $candidate){
					$uri = $candidate["uri"]["value"];
					if(stristr($uri,"http://dbpedia.org/resource/") !== false){
						// I'm only interested in the dbpedia/resourse uri for the moment
						if(stristr($uri,"Category:") == false){
						// not interested in categories
						// now lets see what's in the uri		
							$JSONuri = str_ireplace("resource","data",$uri).".json";
							$objDBJSON->load($JSONuri);
							$type = $objDBJSON->get($uri,"http://www.w3.org/1999/02/22-rdf-syntax-ns#type",null,null);
							$abstract = $objDBJSON->get($uri,"http://dbpedia.org/ontology/abstract",null,"en");
							
	
						}
					}
				}	//foreach($responseArray["results"]["bindings"] as $candidate){	
			}//if(!jSearchString::isStopword($term[2])){
		}	//foreach($BG_wikipedia_list as $term ){
	}//function getSemantics($termArray){
	
	
	
}

?>