<?php
/*
Recibe un query a traves de get y recobra los últimos 1000 tuits en ingles de ese término 
y lo almacena en la tabla
*/

require_once("conexion.php");
require_once("../classes/tuit.php");
require_once("../classes/termino.php");
require_once("../classes/classifier.php");
require_once("twitteroauth-master/twitteroauth/twitteroauth.php");
require_once("twitteroauth-master/config.php");



if(true){
	$oauth_token = '462908421-ldlyIPjaXNaifkJ6n3yh1aUV73c5oQnTTQfmNkvp';
	$oauth_token_secret = 'wcVsV9SRZq5N2EMkWB5VQT0gK55z5Leq4PiwU7mN5Jw';
	$oath = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $oauth_token, $oauth_token_secret);
	
	
	
	$tuit = new Tuit();
	$termino = new Termino();
	
	$termino->obtenerPorNombre($_GET["query"]);
	if($termino->total == 0){
		$termino->crear($_GET["query"],1);
	}
	
	$objClassifier = new Classifier();
	$objClassifier->setTotals();
	
	$desde = "";
	for($x = 1; $x <= 10; $x++){
			echo($x);
			$texto = urlencode('"'.$_GET["query"].'"');
			$query= "https://api.twitter.com/1.1/search/tweets.json?q=$texto&lang=en&count=100&".$desde."show_user=true&include_entities=true&with_twitter_user_id=trues";
			
			//$json = file_get_contents($query, true); //getting the file content
			$json = $oath->get($query);
			//echo($json);


			$decode = json_decode($json, true); //getting the file content as array
			$count = count($decode["statuses"]);	
			
			for($i=0;$i<$count;$i++){
				//obtener los valores de para ser insertados;
				$tuit->crear($decode["statuses"][$i]["id"]);		
				$desde="since_id=".$decode["statuses"][$i]["id"];
				$tuit->texto = str_replace("'"," ",$decode["statuses"][$i]["text"]);
				$tuit->emisor = $decode["statuses"][$i]["user"]["id"];
				$tuit->imagen = $decode["statuses"][$i]["user"]["profile_image_url"];
				//codificar la fecha y hora a formato mysql
				
				$fecha = $decode["statuses"][$i]["created_at"];
				
				$pos = strpos($fecha,"+");
				$fecha_1 = substr($fecha,4,$pos-5);
				$anio = substr($fecha,$pos+5);
				$fecha = $fecha_1.$anio;

				//$parced = date_parse_from_format("d M Y H:i:s", $fecha);

				$parced = date_parse($fecha);

	
				
				if($parced["minute"] < 10){
					$parced["minute"] = "0".$parced["minute"];
					}
				$fecha_mysql = $parced["year"]."-".$parced["month"]."-".$parced["day"]." ".$parced["hour"].":".$parced["minute"].":".$parced["second"];
				$tuit->creado = $fecha_mysql;
				
				
				$tuit->url = "";
				if(isset($decode["statuses"][$i]["entities"]["urls"][0]["url"])){
					$tuit->url = $decode["statuses"][$i]["entities"]["urls"][0]["url"];
				}
				$tuit->menciones = "";
				if(isset($decode["statuses"][$i]["entities"]["user_mentions"])){
					for($j = 0; $j < count($decode["statuses"][$i]["entities"]["user_mentions"]);$j++){
						$tuit->menciones .= $decode["statuses"][$i]["entities"]["user_mentions"][$j]["id"].",";
					}
				}
				
				$tuit->hashtags = "";
				if(isset($decode["statuses"][$i]["entities"]["hashtags"])){
					for($j = 0; $j < count($decode["statuses"][$i]["entities"]["hashtags"]);$j++){
						$tuit->hashtags .= $decode["statuses"][$i]["entities"]["hashtags"][$j]["text"].",";
					}
				}
				
				$tuit->lugar = "";
				if(isset($decode["statuses"][$i]["user"]["location"])){
					$tuit->lugar = $decode["statuses"][$i]["user"]["location"];
					}
					
				$tuit->idTermino = $termino->idTermino;
				
				if($objClassifier->classify($tuit->texto)){ // only save docu-tweets
					$tuit->guardar();
				}
			} //for($i=0;$i<$count;$i++){
	}//for($x = 1; $x <= 15; $x++){
}//if(isset($_GET["idTermino"])){

closeConnection($conn);
//header("Location: ../index.php?err=1");

?>