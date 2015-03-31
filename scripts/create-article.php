<?php
require_once("conexion.php");
require_once("../classes/tuit.php");
require_once("../classes/wordnet.php");
require_once("../classes/wikipedia.php");
require_once("../classes/ClodArticle.php");

// for debugging

ini_set('xdebug.var_display_max_depth', 5);
ini_set('xdebug.var_display_max_children', 256);
ini_set('xdebug.var_display_max_data', 50000);
	

//-- get the tuit
$tuit = new Tuit();

if(isset($_GET["idTuit"])){
	$tuit->obtenerPorID($_GET["idTuit"]);	
}else{
	$tuit->obtenerPrimero();
}

$clean = explode(" ",trim($tuit->denoise())); 

var_dump($tuit->texto);

//get all posibles (2-gram and 1-gram) candidates to have a wikipedia page

	$wordTypes = array();
	$wordRow = array("","","");
	$wordTypeArray = array();
	$wordCont = 0;	
	
	$wordNet = new wordnet();
		
	for($i = 0; $i < count($clean); $i++){
		if(strlen($clean[$i]) > 2){
			$wordTest = $wordNet->determineWordType($clean[$i],$conn);
			if($wordNet->isEmpty($wordTest)){
				// ver si es por el plural o una conjugación 
				$testWord = substr($clean[$i],0,(strlen($clean[$i])-1));
				//echo($testWord."<br>");
				$wordTest = $wordNet->determineWordType($testWord,$conn);
			}
			$wordRow[0] = $clean[$i];
			$wordRow[1] = $wordTest[0][0]."|".$wordTest[1][0]."|".$wordTest[2][0]."|".$wordTest[3][0];
			$wordRow[2] = $wordTest[0][1]."|".$wordTest[1][1]."|".$wordTest[2][1]."|".$wordTest[3][1];
			$wordTypes[$wordCont] = $wordRow;
			$wordTypeArray[$wordCont] = array($clean[$i],$wordTest); 
			$wordCont++;
		}
	}	
	
	// Create the list of cadidates to be validated
	$BG_candidates = $wordNet->getBigramCandidates($wordTypeArray);
	$UG_candidates = $wordNet->getUnigramCandidates($wordTypeArray);

	//obtain the wikipedia pages ID's using the API and discard unsolved candidates
		
	//first we iterate throu the bigrams because they have more semantic value
	//var_dump($BG_candidates);
	$BG_wikipedia_list = obtainWikipediaPageIDs($BG_candidates);
	$UG_unkown_candidates = cleanUnigramCandidates($UG_candidates,$BG_wikipedia_list);
	//var_dump($UG_unkown_candidates);	
	$UG_wikipedia_list = obtainWikipediaPageIDs($UG_unkown_candidates);

	//var_dump($BG_wikipedia_list);
	//var_dump($UG_wikipedia_list);
		
	//with the IDs lets query dbpedia for the semantics
	
	$objArticle = new ClodArticle();
	$objArticle->create($tuit);
	
	$objArticle->annotate($BG_wikipedia_list,$tuit->texto);
	$objArticle->annotate($UG_wikipedia_list,$tuit->texto);
	
	//var_dump($objArticle->rNewsArticle);
	
	$objArticle->save();
	


closeConnection($conn);

?>