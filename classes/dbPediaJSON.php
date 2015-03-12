<?php

class dbPediaJSON {
	
	var $uri;
	var $triplets;
	
	function parse($decode){
		$this->triplets = array();
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
					array_push($this->triplets,$triplet);
				}
			}
		}
	}
	
	function load($Inputuri){
		$json = file_get_contents($Inputuri);
		$this->parse (json_decode($json, true));
		$this->uri = $Inputuri;
	}
	
	private function match ($triplet,$subject = null, $predicate = null,$object = null, $lang = null){
		$result = true;
		/*
		echo($subject." , ".$predicate." . ".$object."<br>");
		echo($triplet[0]." , ".$triplet[1]." , ".$triplet[2]['value']."<br>");
		echo("----------------------------------- <br>");	
		*/
		if($subject !== null){
			if(strcasecmp($triplet[0],$subject) != 0){
				$result = false;
			}
		}
		
		if($predicate !== null){
			if(strcasecmp($triplet[1],$predicate) != 0){
				$result = false;
			}
		}
		
		if($object !== null){
			if(strcasecmp($triplet[2]['value'],$object) != 0){
				$result = false;
			}
		}
		
		if($lang !== null){
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
		$result = array();
		
		for($i = 0; $i < count($this->triplets); $i++){
			$triplet = $this->triplets[$i];
			if($this->match($triplet,$subject,$predicate,$object,$lang)){
				array_push($result,$triplet);
			}	
		}
		
		return $result;
		
	}
	
	


}

?>