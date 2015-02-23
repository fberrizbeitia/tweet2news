// JavaScript Document

$(document).ready(function(){
	
	// FORM ACTIONS
	$( "#submit_btn" ).click(function() {
	
		if ( $( "#query" ).val().length === 0 ) {
			alert("The query must contain al least 1 word");
			event.preventDefault();
		} else {
			// Run $.ajax() here
			$("#loader").show();
			var queryStr = $( "#query" ).val();
			$.ajax({
	 
				// The URL for the request
				url: "scripts/importarTuits.php",
			 
				// The data to send (will be converted to a query string)
				data: {
					query: queryStr
				},
			 
				// Whether this is a POST or GET request
				type: "GET",
			 
				// The type of data we expect back
				//dataType : "json",
			 
				// Code to run if the request succeeds;
				// the response is passed to the function
				success: function( data ) {
					$("#cuerpo").load("import-tweets.php");
					alert( "Finish Downloadng tweets"+data );
				},
			 
				// Code to run if the request fails; the raw request and
				// status codes are passed to the function
				error: function( xhr, status, errorThrown ) {
					alert( "Sorry, there was a problem!" );
					console.log( "Error: " + errorThrown );
					console.log( "Status: " + status );
					console.dir( xhr );
				},
			 
				// Code to run regardless of success or failure
				complete: function( xhr, status ) {
					$("#loader").hide();
				}
				}); //$.ajax({
			} // else
		}); //$( "#importQuery" ).submit(function( event ) {

});//$(document).ready(function(){
// JavaScript Document