<?php

function obtainWikipediaPageIDs($BG_candidates){
	$WP_PageID = array();
	$cont = 0;
	for($i = 0; $i < count($BG_candidates); $i++){
		$url = "http://en.wikipedia.org/w/api.php?action=query&format=json&prop=pageprops&ppprop=disambiguation&redirects&titles=".urlencode($BG_candidates[$i])."&continue=";
		//echo($url."<br>");
		$json = file_get_contents($url);
		$decode = json_decode($json, true);
		echo("--------------- JSON -----------------<br>");
		echo("--$BG_candidates[$i]--<br>");
		var_dump($decode);
		//$key = array_keys($decode["query"]["pages"]);
		$key = array_keys($decode["query"]["pages"]);
		
		$pageId = $key[0]+0;
		
		if($pageId > 0){
			//var_dump($decode);
			$title = "<http://en.wikipedia.org/wiki/".str_replace(" ","_",$decode["query"]["pages"][$pageId]["title"]).">";
			$WP_PageID[$cont] = array($BG_candidates[$i],$title);
			$cont++;
		}
	}
	
	return $WP_PageID;
}

function cleanUnigramCandidates($UG_candidates,$BG_wikipedia_list){
	$result = array();
	$cont = 0;
	$esta = false;
	for($i = 0; $i < count($UG_candidates);$i++){
		$word = $UG_candidates[$i];
		$esta = false;
		for($j =0; $j < count($BG_wikipedia_list) and !$esta;$j++){
			$bigram = $BG_wikipedia_list[$j][0];
			if(substr_count($bigram,$word) > 0){
				$esta = true;
			}
		}
		if(!$esta){
			$result[$cont] = $word;
			$cont++;
		}
	}//	for($i = 0; $i < count($UG_candidates);$i++){
	return $result;	
}

function getUrlDbpediaPage($pageURL)
{
   $format = 'json';

   // if is a thing obtain these parameters
    $query = "select ?label ?abstract ?thumbnail ?decpition 
		where{
		?resource <http://xmlns.com/foaf/0.1/isPrimaryTopicOf> $pageURL.
		?resource <http://www.w3.org/2000/01/rdf-schema#label> ?label.
		?resource <http://dbpedia.org/ontology/abstract> ?abstract.
		?resource <http://dbpedia.org/ontology/thumbnail> ?thumbnail.
		?resource <http://xmlns.com/foaf/0.1/depiction> ?decpition.
		FILTER(langMatches(lang(?abstract), 'EN')).
		FILTER(langMatches(lang(?label), 'EN')).
		}
	";
	//if is a person
	
	//if is a place   
   
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

function printArray($array, $spaces = "")
{
   $retValue = "";
   
   if(is_array($array))
   {  
      $spaces = $spaces
         ."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

      $retValue = $retValue."<br/>";

      foreach(array_keys($array) as $key)
      {
         $retValue = $retValue.$spaces
            ."<strong>".$key."</strong>"
            .printArray($array[$key], 
               $spaces);
      }     
      $spaces = substr($spaces, 0, -30);
   }
   else $retValue = 
      $retValue." - ".$array."<br/>";
   
   return $retValue;
}

function getSemantics($IdList){
	$results = array();
	$count = 0;
	for($i = 0; $i < count($IdList); $i++){
		//echo("URI: ".$IdList[$i][1]."<br>");
		$requestURL = getUrlDbpediaPage($IdList[$i][1]);
		//var_dump($i);
		//var_dump($requestURL);
		$responseArray = json_decode(request($requestURL),true); 
		//var_dump($responseArray["results"]["bindings"]);
		//echo("----------------------------------<br>");
		
		if(count($responseArray["results"]["bindings"]) > 0){
			$label= $responseArray["results"]["bindings"][0]["label"]["value"];
			$abstract = $responseArray["results"]["bindings"][0]["abstract"]["value"];
			$thumb = $responseArray["results"]["bindings"][0]["thumbnail"]["value"];
			$decpition = $responseArray["results"]["bindings"][0]["decpition"]["value"];
			$results[$count] = array($IdList[$i][0],$label,$thumb,$abstract,$decpition);
			$count++;
		}
	}
	return $results;
}

?>