<?php

class wordnet {

	function isEmpty($wordTypeRow){
		
		if($wordTypeRow[0][0] == "" and $wordTypeRow[1][0] == "" and $wordTypeRow[2][0] == "" and $wordTypeRow[3][0] == "")	{
		return true;
		}else{
			return false;
		}
	
	}
	
	function isNumeric($wordTypeRow){
			if($wordTypeRow[0][0] == ""){
				return false;	
			}else{
				return true;
				}
	}
	
	function isVerb($wordTypeRow){
			if($wordTypeRow[1][0] == ""){
				return false;	
			}else{
				return true;
				}
	}
	
	function isAdj($wordTypeRow){
			if($wordTypeRow[2][0] == ""){
				return false;	
			}else{
				return true;
				}
	}
	function isNoun($wordTypeRow){
			if($wordTypeRow[3][0] == ""){
				return false;	
			}else{
				return true;
				}
	}
	
	
	function determineWordType($word,$wn_conn){
		//array()
		$result = array();
		$result[0] = array("",""); // Numeric type
		$result[1] = array("",""); // is a verb
		$result[2] = array("",""); // is an adjective
		$result[3] = array("",""); // is a noun
		
		if(is_numeric($word)){
			$result[0][0] = "Numeric";
		}else{
			mysql_select_db("wordnet");
			$sql = "SELECT  lexdomainname,pos,definition FROM DICT INNER JOIN lexdomains USING(lexdomainid,pos) WHERE lemma = '$word'";
			//echo($sql."<br>");
			$result2 = mysql_query($sql,$wn_conn) or die( mysql_error( $db_conn ) );
			
			
			$is_verb = false;
			$verb_def = "";
			$verb_domain = "";
			
			$is_adj = false;
			$adj_def = "";
			$adj_domain = "";
			
			$is_noun = false;
			$noun_def = "";
			$noun_domain = "";
	
			
			while($row = mysql_fetch_assoc($result2)) {
				//echo($word." ".$row['lexdomainname']." ".$row['definition']."<br>");
				
				if($row['pos'] == 'v' and !$is_verb){
					$is_verb = true;
					$verb_domain = $row['lexdomainname'];
					$verb_def = $row['definition'];	
				} 
				
				if($row['pos'] == 'a' and !$is_adj){
					$is_adj = true;
					$adj_def = $row['definition'];
					$adj_domain = $row['lexdomainname'];	
				}
				if($row['pos'] == 'n' and !$is_noun){
					$is_noun = true;
					$noun_def = $row['definition'];
					$noun_domain = $row['lexdomainname'];
				}
				 
			}
			
			if($is_adj){
				$result[2][0] = $adj_domain;
				$result[2][1] = $adj_def;
				
			}
			if($is_verb){
				$result[1][0] = $verb_domain ;
				$result[1][1] = $verb_def;	
			}
			
			if($is_noun){
				$result[3][0] = $noun_domain;
				$result[3][1] = $noun_def;	
			}
		}
		
		return $result;
			
	}
	
	function isValid($wordTypeRow1,$wordTypeRow2){
		//valid bigrams are: empty - empty empty-(noun|adj) , (noun|adj)|empty , adj|noun , noun|noun
		
		$result = false;
		if($this->isEmpty($wordTypeRow1)){
			if($this->isNoun($wordTypeRow2) or $this->isAdj($wordTypeRow2) or $this->isEmpty($wordTypeRow2)){
				$result = true;
			}
		}
		
		if($this->isEmpty($wordTypeRow2)){
			if($this->isNoun($wordTypeRow1) or $this->isAdj($wordTypeRow1)){
				$result = true;
			}
		}
		
		if($this->isAdj($wordTypeRow1) and $this->isNoun($wordTypeRow2)){
			$result = true;
		}
		
		if($this->isNoun($wordTypeRow1) and $this->isNoun($wordTypeRow2)){
			$result = true;
		}
		
		return $result;
	
	}
	
	function getBigramCandidates($wordTypeArray){
		
		$bigramArray = array();
		$bigramCount = 0;
		for($i = 0; $i < (count($wordTypeArray)-1); $i++){
			$wordTypeRow1 = $wordTypeArray[$i];
			$wordTypeRow2 = $wordTypeArray[$i+1];
			if($this->isValid($wordTypeRow1[1],$wordTypeRow2[1])){
				$bigramArray[$bigramCount] = $wordTypeRow1[0]." ".$wordTypeRow2[0];
				$bigramCount++;
			}
		}
		return $bigramArray;		
	}
	
	function getUnigramCandidates($wordTypeArray){
		
		$UnigramArray = array();
		$UnigramCount = 0;
		for($i = 0; $i < count($wordTypeArray); $i++){
			if($this->isNoun($wordTypeArray[$i][1]) or $this->isEmpty($wordTypeArray[$i][1])){
				$UnigramArray[$UnigramCount] = $wordTypeArray[$i][0];
				$UnigramCount++; 
			}
		}
		return $UnigramArray;		
	}

}
?>
