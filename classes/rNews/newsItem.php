<?php

class newsItem{
	var $subjectURI;
	
	var $headline;			// xsd:string
	var $provider;			// PERSON | ORGANIZATION
	var $creator;			// PERSON | ORGANIZATION
	var $dateCreated;		// xsd:dateTime
	var $datePublished;		// xsd:dateTime
	var $about;				// CONCEPT
	var $mentions;			// CONCEPT
	var $inLanguage = "en";	// xsd:string // language
	var $identifier; 		// xsd:string
	var $dateline; 			// xsd:string // Location of the news
 
}

?>