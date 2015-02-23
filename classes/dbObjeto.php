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
		 or die("Función DBobjeto->query: Could not query: " . mysql_error());
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
	
		$valor = str_replace("á","&aacute;",$valor);
		$valor = str_replace("é","&eacute;",$valor);
		$valor = str_replace("í","&iacute;",$valor);
		$valor = str_replace("ó","&oacute;",$valor);
		$valor = str_replace("ú","&uacute;",$valor);
		$valor = str_replace("Á","&Aacute;",$valor);
		$valor = str_replace("É","&Eacute;",$valor);
		$valor = str_replace("Í","&Iacute;",$valor);
		$valor = str_replace("Ó","&Oacute;",$valor);
		$valor = str_replace("Ú","&Uacute;",$valor);
		$valor = str_replace("ñ","&ntilde;",$valor);
		$valor = str_replace("Ñ","&Ntilde;",$valor);
		$valor = str_replace("ü","&uuml;",$valor);
		$valor = str_replace("Ü","&Uuml;",$valor);
		$valor = str_replace("\n","<br />",$valor);
		$valor = str_replace(chr(34),"&quot;",$valor);
		$valor = str_replace("“","&ldquo;",$valor);
		$valor = str_replace("”","&rdquo;",$valor);
		$valor = str_replace("…","&hellip;",$valor);
		$valor = str_replace("°","&deg;",$valor);
		$valor = str_replace("¡","&iexcl;",$valor);
		$valor = str_replace("¿","&iquest;",$valor);
		$valor = str_replace("ç","&ccedil;",$valor);
		$valor = str_replace("ã","&atilde;",$valor);
		$valor = str_replace("õ","&otilde;",$valor);
		$valor = str_replace("ê","&ecirc;",$valor);
		$valor = str_replace("â","&acirc;",$valor);
		$valor = str_replace("ô","&ocirc;",$valor);
		$valor = str_replace("à","&agrave;",$valor);
	
		return $valor;
	}
}

?>