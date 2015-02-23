// JavaScript Document
$(document).ready(function(e) {
    //menu action
	$("#import-tweets").click(
		function(){
		$("#cuerpo").load("import-tweets.php");
		}
	);
});