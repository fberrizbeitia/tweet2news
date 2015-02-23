<?php
include_once("dbObjeto.php");

class Termino extends dbObjeto{

var $idTermino;
var $texto;
var $idUsuario;

	function actualizar(){
		if ($this->total > 0){
				$this->idUsuario= mysql_result($this->lista,$this->indice,"idUsuario");
				$this->texto = mysql_result($this->lista,$this->indice,"texto");
				$this->idTermino = mysql_result($this->lista,$this->indice,"idTermino");
			}
		}
		
	function obtenerTodos(){
		$sql = "SELECT * FROM termino";
		$result = mysql_query($sql) or die("termino->obtenerTodos: error en consulta".mysql_error()."SQL: ".$sql);
		$this->lista = $result;
		$this->total = mysql_num_rows($result);
		$this->indice = 0;
		$this->actualizar();
		}
		
	function obtenerPorID($id){
		$sql = "SELECT * FROM termino WHERE idTermino = $id";
		$result = mysql_query($sql) or die("termino->obtenerPorID: error en consulta".mysql_error()."SQL: ".$sql);
		$this->lista = $result;
		$this->total = mysql_num_rows($result);
		$this->indice = 0;
		$this->actualizar();
		}
		
	function obtenerPorIdUsuario($id){
		$sql = "SELECT * FROM termino WHERE idUsuario = $id";
		$result = mysql_query($sql) or die("termino->obtenerPorIdUsuario: error en consulta".mysql_error()."SQL: ".$sql);
		$this->lista = $result;
		$this->total = mysql_num_rows($result);
		$this->indice = 0;
		$this->actualizar();
		}
		
	function obtenerPorNombre($nombre){
		$sql = "SELECT * FROM termino WHERE texto = '$nombre'";
		$result = mysql_query($sql) or die("termino->obtenerPorNombre: error en consulta".mysql_error()."SQL: ".$sql);
		$this->lista = $result;
		$this->total = mysql_num_rows($result);
		$this->indice = 0;
		$this->actualizar();
		}
		
	function guardar(){
		$sql = "UPDATE termino SET nombre = $this->nombre, idUsuario = $this->idUsuario WHERE idTermino = $this->idTermino";
		mysql_query($sql) or die("cliente->guardar(): error en consulta".mysql_error()."SQL: ".$sql);
	}
	
	function crear($texto, $idUsuario){
		$sql = "INSERT INTO termino (texto,idUsuario) values ('$texto',$idUsuario)";
		mysql_query($sql) or die("cliente->crear($texto, $idUsuario): error en consulta".mysql_error()."SQL: ".$sql);
		$this->obtenerPorID(mysql_insert_id());
		}
	
	function obtenerTotalTuits(){
		$sql = "SELECT COUNT( idTuit ) AS total FROM  `tuits` WHERE idTermino = $this->idTermino";
		$result = mysql_query($sql) or die("cliente->obtenerTotalTuits($texto, $idUsuario): error en consulta".mysql_error()."SQL: ".$sql);
		return(mysql_result($result,0,"total"));
		}
}

?>