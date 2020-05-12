<?php
	include "Session.php";
	include "connessione.php";
	
	$pageName="Gestione parametri";
?>
<html>
	<head>
		<link href="css/fonts.css" rel="stylesheet">
		<link rel="stylesheet" href="js_libraries/spinners/spinner.css" />
		<script src="js_libraries/spinners/spinner.js"></script>
		<script src="editableTableParametri/editableTable.js"></script>
		<link rel="stylesheet" href="editableTableParametri/editableTable.css" />
		<script src="js_libraries/sweetalert.js"></script>
		<title><?php echo $pageName; ?></title>
		<link rel="stylesheet" href="css/main.css" />
		<link rel="stylesheet" href="css/gestioneAnagrafiche.css" />
		<script src="js/struttura.js"></script>
		<script src="js/gestioneParametri.js"></script>
		<script src="js/main.js"></script>
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
	<body>
		<?php include('struttura.php'); ?>
		<div class="absoluteActionBarSommarioArchivi" style="top:100">
            <div class="absoluteActionBarSommarioArchiviElement">Righe: <span id="rowsNumEditableTable"></span></div>
            <!--<button class="absoluteActionBarSommarioArchiviButton" onclick="scaricaExcel('containerSommarioArchivi')">Esporta <i style="margin-left:5px;color:green" class="far fa-file-excel"></i></button>-->
            <button class="absoluteActionBarSommarioArchiviButton" onclick="resetFilters();getTable(selectetTable)">Ripristina <i style="margin-left:5px" class="fal fa-filter"></i></button>
		</div>
		<div id="containerSommarioArchivi"></div>
		<div id="footer">
			<b>Marine&nbspInteriors&nbspS.p.A.</b>&nbsp&nbsp|&nbsp&nbspVia&nbspSegaluzza&nbsp33170&nbspPordenone&nbsp&nbsp|&nbsp&nbspPhone:&nbsp(+39)&nbsp0434612811&nbsp|&nbspPowered&nbspby&nbsp<a target="_blank" href="http://www.servizioglobale.it">Servizio Globale S.R.L.</a>
		</div>
	</body>
</html>

