<?php
include_once("concept.php");

class organization extends concept{
	
	var $tickerSymbol;	//xsd:string
	var $address; 		//PostalAddress
	
	function __construct ($uri = null,$name = null, $description = null, $image = null, $url = null,$addInfoUri = null,$tickerSymbol = null, $address = null){
			parent::__construct($uri,$name,$description,$image,$url,$addInfoUri);
			$this->tickerSymbol = $tickerSymbol;
			$this->address = $address;
			
		}
		
	function getType(){
		return 'organization';
	}
	
	function save(EasyRdf_Resource $EasyRdfResource){
		
		if($this->name != null){
			$EasyRdfResource->add("rNews:name",$this->name);
		}
		
		if($this->description != null){
			$EasyRdfResource->add("rNews:description",$this->description);
		}
		
		if($this->url != null){
			$EasyRdfResource->add("rNews:url",$this->url);
		}
		
		if($this->image != null){
			$EasyRdfResource->add("rNews:image",$this->image);
		}
		
		if($this->tickerSymbol != null){
			$EasyRdfResource->add("rNews:tickerSymbol",$this->tickerSymbol);
		}
			
	}
}

?>