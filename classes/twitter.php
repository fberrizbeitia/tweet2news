<?php
require_once("twitteroauth-master/twitteroauth/twitteroauth.php");
require_once("twitteroauth-master/config.php");

class twitter{
	
	var $oauth_token = '462908421-ldlyIPjaXNaifkJ6n3yh1aUV73c5oQnTTQfmNkvp';
	var $oauth_token_secret  = 'wcVsV9SRZq5N2EMkWB5VQT0gK55z5Leq4PiwU7mN5Jw';
	
	function __construct(){
		$this->oath = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $this->oauth_token, $this->oauth_token_secret);
	}
	
	function query($query){
		return $this->oath->get($query);
	}
	
}

?>