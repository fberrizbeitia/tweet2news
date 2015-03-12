<?php
include_once("dbObjeto.php");
include_once("JSearchString.php");

class Tuit extends dbObjeto{

	var $idTuit;
	var $menciones;
	var $hashtags;
	var $url;
	var $media;
	var $texto;
	var $emisor;
	var $creado;
	var $lugar;
	var $idTermino;
	var $especiales = array("'",'"',"/","\\");

	
	
	function actualizar(){
		if ($this->total > 0){
				$this->idTuit= mysql_result($this->lista,$this->indice,"idTuit");
				$this->menciones = mysql_result($this->lista,$this->indice,"menciones");
				$this->hashtags = mysql_result($this->lista,$this->indice,"hashtags");
				$this->url = mysql_result($this->lista,$this->indice,"url");
				$this->media = mysql_result($this->lista,$this->indice,"media");
				$this->texto = mysql_result($this->lista,$this->indice,"texto");
				$this->emisor = mysql_result($this->lista,$this->indice,"emisor");
				$this->creado = mysql_result($this->lista,$this->indice,"creado");
				$this->lugar = mysql_result($this->lista,$this->indice,"lugar");

				$this->idTermino = mysql_result($this->lista,$this->indice,"idTermino");
			}
	
	}
		
	function obtenerTodos(){
		$sql = "SELECT * FROM tuits";
		$result = mysql_query($sql) or die("tuit->obtenerTodos: error en consulta".mysql_error()."SQL: ".$sql);
		$this->lista = $result;
		$this->total = mysql_num_rows($result);
		$this->indice = 0;
		$this->actualizar();

	}
	
	function obtenerPorID($id){
		$sql = "SELECT * FROM  tuits WHERE idTuit = ".$id;
		$result = mysql_query($sql) or die("tuit->obtenerPorID: error en consulta".mysql_error()."SQL: ".$sql);
		$this->lista = $result;
		$this->total = mysql_num_rows($result);
		$this->indice = 0;
		$this->actualizar();
	}
		

	
	function obtenerPrimero(){
		$sql = "SELECT * FROM `tuits` WHERE idTuit NOT IN (SELECT idTuit FROM articles) ORDER BY Creado ASC LIMIT 1";
		$result = mysql_query($sql) or die("tuit->obtenerImpactoTotal: error en consulta".mysql_error()."SQL: ".$sql);
		$this->lista = $result;
		$this->total = mysql_num_rows($result);
		$this->indice = 0;
		$this->actualizar();
		}
	
	function obtenerMenciones($idusuario){
		$sql = "SELECT * FROM `tuits` WHERE menciones LIKE '%$idusuario%' and idTermino = $this->idTermino  ORDER BY impacto DESC limit 0,25";
		$result = mysql_query($sql) or die("tuit->obtenerMenciones: error en consulta".mysql_error()."SQL: ".$sql);
		$this->lista = $result;
		$this->total = mysql_num_rows($result);
		$this->indice = 0;
		$this->actualizar();
	}
	
	function obtenerTotalMenciones($idusuario){
		$sql = "SELECT COUNT(menciones) AS total FROM `tuits` WHERE menciones LIKE '%$idusuario%' and idTermino = $this->idTermino";
		$result = mysql_query($sql) or die("tuit->obtenerTotalMenciones: error en consulta".mysql_error()."SQL: ".$sql);
		return mysql_result($result,0,"total");
		}
	
	function guardar(){
		//si hay caracteres raros eliminarlo
		for($i = 0; $i < count($this->especiales); $i++){
			$this->texto = str_replace($this->especiales[$i],"", $this->texto);
			$this->lugar = str_replace($this->especiales[$i],"", $this->lugar);
		}
		
		$sql = "UPDATE tuits SET  menciones = '$this->menciones', hashtags = '$this->hashtags', url = '$this->url', texto = '$this->texto', media = '$this->media',emisor = '$this->emisor', creado = '$this->creado', lugar = '$this->lugar',  idTermino = $this->idTermino WHERE idTuit=$this->idTuit";
		mysql_query($sql) or die("tuit->guardar(): error en consulta".mysql_error()."SQL: ".$sql);
	}
	
		
	function crear($id){
		$sql = "SELECT * FROM  tuits WHERE idTuit = ".$id;
		$result = mysql_query($sql) or die("tuit->obtenerPorID: error en consulta".mysql_error()."SQL: ".$sql);	
		if(mysql_num_rows($result) == 0){
			$sql = "INSERT INTO tuits (idTuit) values ($id)";
			mysql_query($sql) or die("tuit->crear: error en consulta".mysql_error().".SQL: ".$sql);
		}
		$this->obtenerPorID($id);
	}
	
	function eliminar(){
		//eliminar el cargo de la tabla
		$sql = "DELETE FROM tuits WHERE idTuit=$this->idTuit";
		mysql_query($sql) or die("tuit->eliminar(): error en consulta".mysql_error()."SQL: ".$sql);
	}
	
	
	//------------------------------- DENOISE FUNTIONS
	
	private function removeMentions($tweet){
		$token = explode(" ",$tweet);
		$noMentions = "";
		for($i = 0; $i < count($token); $i++){
			$pos = strpos($token[$i], '@');
			$posURL = strpos($token[$i], 'htt');
			if($pos === false and $posURL === false){
				$noMentions .= $token[$i]." ";
			}
		}
		return $noMentions;
	}
	
	public function denoise(){
		$jSS = new jSearchString();
		$noMentions = $this->removeMentions($this->texto);
		//$noStopWords = $jSS->parseString( strtolower($noMentions));
		$noStopWords = $noMentions;
		return $noStopWords;
	}


}
?>