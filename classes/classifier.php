<?php
/*
Implementation of the Naïve Bayes Classifier
*/

class Classifier {
	
	var $totalDocumentary;
	var $totalNonDocumentary;
		
	function generateTrainingSet($size){
	/*
	Description: Takes a SRS of size $size form sample and tag those as parte of the traing Set
	*/	
	
	$sql = "UPDATE sample SET trainingSet = 0";
	mysql_query($sql) or die("Classifier->generateTrainingSet_1: error en consulta".mysql_error()."SQL: ".$sql);
	
	$sizeNormalized = $size/100; 
	
	$sql = "UPDATE sample SET trainingSet = 1 WHERE RAND() < $sizeNormalized";
	mysql_query($sql) or die("Classifier->generateTrainingSet_1: error en consulta".mysql_error()."SQL: ".$sql);
	
	}
	
	function train(){
		//primero los no documentales
		$sql = "SELECT bow.idWord,COUNT(bow.idWord) as total from bow,sample WHERE bow.idTuit = sample.idTuit and sample.trainingSet  = 1 and sample.class = 0 GROUP BY idWord";
		$result = mysql_query($sql) or die("Classifier->train: error en consulta".mysql_error()."SQL: ".$sql);
		
		while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
    		$idWord = $row[0]; 
			$count = $row[1]; 
			$sql = "UPDATE dictionary SET n_nondoc = $count WHERE idWord = $idWord";
			mysql_query($sql) or die("Classifier->train_2: error en consulta".mysql_error()."SQL: ".$sql);
		}
		
		//luego los no documentales
		$sql = "SELECT bow.idWord,COUNT(bow.idWord) as total from bow,sample WHERE bow.idTuit = sample.idTuit and sample.trainingSet  = 1 and sample.class = 1 GROUP BY idWord";
		$result = mysql_query($sql) or die("Classifier->train_3: error en consulta".mysql_error()."SQL: ".$sql);
		
		while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
    		$idWord = $row[0]; 
			$count = $row[1]; 
			$sql = "UPDATE dictionary SET n_doc = $count WHERE idWord = $idWord";
			mysql_query($sql) or die("Classifier->train_4: error en consulta".mysql_error()."SQL: ".$sql);
		}
	
	}
	
	function setTotals(){
		$sql = "SELECT COUNT( idTuit ) AS total FROM  `sample` WHERE class = 0 AND trainingSet =1";
		$result = mysql_query($sql) or die("Classifier->setTotals: error en consulta".mysql_error()."SQL: ".$sql);
		$this->totalNonDocumentary = mysql_result($result,0,"total");
		
		$sql = "SELECT COUNT( idTuit ) AS total FROM  `sample` WHERE class = 1 AND trainingSet =1";
		$result = mysql_query($sql) or die("Classifier->setTotals: error en consulta".mysql_error()."SQL: ".$sql);
		$this->totalDocumentary = mysql_result($result,0,"total");
		
		
	}
	
	function classify($text){
		/*
		Description: Classify the given text as documentary or non documentary
		INPUT: a stemmed text
		OUTPUT: 1 if the text is docuemntary, 0 otherwise
		*/
		
		
		$probDoc = $this->totalDocumentary/($this->totalDocumentary + $this->totalNonDocumentary) ;
		$probNonDoc = $this->totalNonDocumentary/($this->totalDocumentary + $this->totalNonDocumentary) ;
		
		
		$words = explode(" ",$text);
		
		for($i = 0; $i < count($words); $i++){
			
			$sql = "SELECT n_doc, n_nondoc FROM dictionary WHERE word = '".$words[$i]."' ";
			$result = mysql_query($sql) or die("Classifier->classify_1: error en consulta".mysql_error()."SQL: ".$sql);
			
			if(mysql_num_rows($result) > 0){
				 
				$probNonDoc *= (mysql_result($result,0,"n_nondoc")+1)/($this->totalNonDocumentary +1);
				$probDoc *= (mysql_result($result,0,"n_doc")+1)/($this->totalDocumentary +1);
				
				
			}
		}
		
		
		$resta = $probDoc - $probNonDoc;
		
		$threshold = 0;
		
		if( ($probDoc - $probNonDoc) > $threshold){
			return 1;
		}else{
			return 0;	
		}
	
	}

}
