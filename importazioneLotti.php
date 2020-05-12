<?php
	include "Session.php";
	include "connessione.php";
	
	$pageName="Importazione schede lotti";
?>
<html>
	<head>
		<link href="css/fonts.css" rel="stylesheet">
		<link rel="stylesheet" href="js_libraries/spinners/spinner.css" />
		<script src="js_libraries/spinners/spinner.js"></script>
		<script src="js_libraries/sweetalert.js"></script>
		<title><?php echo $pageName; ?></title>
		<link rel="stylesheet" href="css/main.css" />
		<link rel="stylesheet" href="css/importazioneLotti.css" />
		<script src="js/struttura.js"></script>
		<script src="js/importazioneLotti.js"></script>
		<script src="js/main.js"></script>
		<script type="text/javascript" src="js_libraries/xlsx.full.min.js"></script>
		<script type="text/javascript" src="js_libraries/jszip.js"></script>
		<style>
			.swal2-title
			{
				font-family:'Montserrat',sans-serif;
				font-size:18px;
			}
			.swal2-content
			{
				font-family:'Montserrat',sans-serif;
				font-size:13px;
			}
			.swal2-confirm,.swal2-cancel
			{
				font-family:'Montserrat',sans-serif;
				font-size:13px;
			}
		</style>
	</head>
	<body onload="onloadactions()">
		<?php include('struttura.php'); ?>
        <div class="top-action-bar" id="stampaChecklistActionBar">
			<button class="action-bar-text-icon-button" id="bntImporta" style="margin-left:0px" onclick="importaFile(this)"><span>Importa</span><i class="fad fa-file-import"></i></button>
		</div>
        <div id="dropFileContainer" onclick="document.getElementById('inputScegliFile').click()" ondrop="getDrop(event)" ondragover="getDropHereStyle(event)" ondragleave="clearDropHereStyle(event)">
            <div id="dropFileMessageContainer">
                <i id="dropFileIcon" class="fal fa-file-import"></i>
                <span id="dropFileMessage">Trascina qui un file o clicca per selezionarlo</span>
            </div>
        </div>
        <input type="file" id="inputScegliFile" style="display:none" onchange="checkFile(this.files[0])">
		<div id="importazioneLottiContainer"></div>
		<div id="footer">
			<b>Marine&nbspInteriors&nbspS.p.A.</b>&nbsp&nbsp|&nbsp&nbspVia&nbspSegaluzza&nbsp33170&nbspPordenone&nbsp&nbsp|&nbsp&nbspPhone:&nbsp(+39)&nbsp0434612811&nbsp|&nbspPowered&nbspby&nbsp<a target="_blank" href="http://www.servizioglobale.it">Servizio Globale S.R.L.</a>
		</div>
	</body>
</html>

