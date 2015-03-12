<?php

function obtainWikipediaPageIDs($BG_candidates){
	$WP_PageID = array();
	$cont = 0;
	for($i = 0; $i < count($BG_candidates); $i++){
		$url = "http://en.wikipedia.org/w/api.php?action=query&format=json&prop=pageprops&ppprop=disambiguation&redirects&titles=".urlencode($BG_candidates[$i])."&continue=";

		$json = file_get_contents($url);
		$decode = json_decode($json, true);
		
		if(count($decode) > 1){
			$key = array_keys($decode["query"]["pages"]);
			$pageId = $key[0]+0;
		}else{
			$pageId = 0;
			}
		
		
		
		if($pageId > 0){

			$url = "<http://en.wikipedia.org/wiki/".str_replace(" ","_",$decode["query"]["pages"][$pageId]["title"]).">";
			$WP_PageID[$cont] = array($BG_candidates[$i],$url,$decode["query"]["pages"][$pageId]["title"]);
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



?>