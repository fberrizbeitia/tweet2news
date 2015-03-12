<?php
include_once("concept.php");

class person extends concept{
	
	var $bio;         		// xsd:string short bio of the person
	var $decpiction;  		// xsd:string descriptive picture of the person
	var $givenName;			// xsd:string Given name. In the U.S., the first name of an individual. Also used if person has only one name
	var $additionalName;	// xsd:string
	var $familyName; 		// xsd:string
	var $address; 			// PostalAddress
	var $honorificPrefix;	// xsd:string
	var $honorificSuffix; 	// xsd:string

}

?>