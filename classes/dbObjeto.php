<?php

abstract class dbObjeto{
	
	public $indice = 0;
	public $total = 0;
	public $lista;
	
	abstract function actualizar();
	abstract function obtenerTodos();
	abstract function obtenerPorID($id);
	abstract function guardar();
	
	public function query($sql){
		$result = mysql_query($sql) 
		 or die("Funci�n DBobjeto->query: Could not query: " . mysql_error());
		 return mysql_fetch_array($result);	
	}
	
	public function ir($input_indice){
		if ($input_indice < $this->total && $input_indice >= 0){
			$this->indice = $input_indice;
		}
		$this->actualizar();		
	}
	
	function cambiaf_a_mysql($fecha) {
		ereg("([0-9]{1,2})-([0-9]{1,2})-([0-9]{2,4})", $fecha, $mifecha);
		$lafecha = $mifecha[3] . "-" . $mifecha[2] . "-" . $mifecha[1];
		if($lafecha == "--") {$lafecha = "00-00-0000";}
		return $lafecha;
	}

	function cambiaf_a_normal($fechaMysql) {
		$fecha = split("-",$fechaMysql); 
		$hora = split(":", $fecha[2]);
		$fecha_hora = split(" ", $hora[0]); 
		$fecha_convertida = $fecha_hora[0].'-'.$fecha[1].'-'.$fecha[0].' '.$fecha_hora[1].':'.$hora[1].':'.$hora[2];  
		return $fecha_convertida; 
	}
	
	function aHTML($valor){
	
		$valor = str_replace("�","&aacute;",$valor);
		$valor = str_replace("�","&eacute;",$valor);
		$valor = str_replace("�","&iacute;",$valor);
		$valor = str_replace("�","&oacute;",$valor);
		$valor = str_replace("�","&uacute;",$valor);
		$valor = str_replace("�","&Aacute;",$valor);
		$valor = str_replace("�","&Eacute;",$valor);
		$valor = str_replace("�","&Iacute;",$valor);
		$valor = str_replace("�","&Oacute;",$valor);
		$valor = str_replace("�","&Uacute;",$valor);
		$valor = str_replace("�","&ntilde;",$valor);
		$valor = str_replace("�","&Ntilde;",$valor);
		$valor = str_replace("�","&uuml;",$valor);
		$valor = str_replace("�","&Uuml;",$valor);
		$valor = str_replace("\n","<br />",$valor);
		$valor = str_replace(chr(34),"&quot;",$valor);
		$valor = str_replace("�","&ldquo;",$valor);
		$valor = str_replace("�","&rdquo;",$valor);
		$valor = str_replace("�","&hellip;",$valor);
		$valor = str_replace("�","&deg;",$valor);
		$valor = str_replace("�","&iexcl;",$valor);
		$valor = str_replace("�","&iquest;",$valor);
		$valor = str_replace("�","&ccedil;",$valor);
		$valor = str_replace("�","&atilde;",$valor);
		$valor = str_replace("�","&otilde;",$valor);
		$valor = str_replace("�","&ecirc;",$valor);
		$valor = str_replace("�","&acirc;",$valor);
		$valor = str_replace("�","&ocirc;",$valor);
		$valor = str_replace("�","&agrave;",$valor);
	
		return $valor;
	}
}

?>