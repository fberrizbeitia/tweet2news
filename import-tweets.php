<?php
require_once("scripts/conexion.php");
require_once("classes/termino.php");

?>
<link href="stiles.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/import-tweets.js"></script>

<div id="loader">
<img src="images/page-loader.gif" width="150" height="150" />
<br />
The tweets are being downloaded. Please wait. It may take several minutes to complete.
</div>

<div id="inner-cont">
  
        <div id="titulo">
        	<h1>Choose a theme to recover the latest Tweets.<br>
            	Only documentary tweets will be saved.
            </h1>
        </div>
        
        <div>
        	<form id="importQuery" action="">
                <input id="query" name="query" type="text" />
                <input id="submit_btn" name="submit_btn" type="button" value="Submit" />
            </form>
        </div>
 
  		<div id="lista">
        	<ul>
        	<?php
            $termino = new Termino();
			$termino->obtenerTodos();
			for($i = 0; $i < $termino->total; $i++){
				$termino->ir($i);
			?>
            	<li><?php echo($termino->texto.": ".$termino->obtenerTotalTuits())?> </li>
            <?php
			}
			?>
            </ul>
        </div>
</div>

