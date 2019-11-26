<?php
	include "Session.php";
	include "connessione.php";
	
	$pageName="Gestione anagrafiche";
?>
<html>
	<head>
		<link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
		<link rel="stylesheet" href="js_libraries/spinners/spinner.css" />
		<script src="js_libraries/spinners/spinner.js"></script>
		<script src="editableTable/editableTable.js"></script>
		<link rel="stylesheet" href="editableTable/editableTable.css" />
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
		<title><?php echo $pageName; ?></title>
		<link rel="stylesheet" href="css/main.css" />
		<link rel="stylesheet" href="css/gestioneAnagrafiche.css" />
		<script src="js/struttura.js"></script>
		<script src="js/gestioneAnagrafiche.js"></script>
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
			.absoluteActionBarControls
			{
				float:left;
				display:none;
			}
		</style>
	</head>
	<body>
		<?php include('struttura.php'); ?>
        <div class="funcionListContainer" style="top:70;">
			<div class="functionList">
				<button class="functionListButton" onclick="resetStyle(this);getTable('anagrafica_punzoni')">Anagrafica punzoni</button>
				<button class="functionListButton" onclick="resetStyle(this);getTable('anagrafica_multitools')">Anagrafica multitools</button>
				<button class="functionListButton" onclick="resetStyle(this);getTable('anagrafica_configurazioni')">Anagrafica configurazioni torretta</button>
				<button class="functionListButton" onclick="resetStyle(this);addOptionsConfigurazioni();">Configurazioni punzoni torretta</button>
			</div>
		</div>
		<div class="absoluteActionBarSommarioArchivi">
			<div id="editableTableControls" class="absoluteActionBarControls">
				<div class="absoluteActionBarSommarioArchiviElement">Righe: <span id="rowsNumEditableTable"></span></div>
				<button class="absoluteActionBarSommarioArchiviButton" onclick="scaricaExcel('containerSommarioArchivi')">Esporta <i style="margin-left:5px;color:green" class="far fa-file-excel"></i></button>
				<button class="absoluteActionBarSommarioArchiviButton" onclick="resetFilters();getTable(selectetTable)">Ripristina <i style="margin-left:5px" class="fal fa-filter"></i></button>
			</div>
			<div id="configurazionePunzoniControls" class="absoluteActionBarControls">
				<div class="absoluteActionBarSommarioArchiviElement">
					Configurazione: 
					<select id="selectConfigurazionePunzoni" onchange="getConfigurazionePunzoni()"></select>
				</div>
			</div>
		</div>
		<div id="containerSommarioArchivi"></div>
		<div id="footer">
			<b>Oasis Group</b>  |  Via Favola 19 33070 San Giovanni PN  |  Tel. +39 0434654752
		</div>

		<!--for dev only-->
		<!--<script>addOptionsConfigurazioni();</script>
		<div style="position:absolute;width:5px;background:red;left:700;top:0;bottom:0"></div>
		<div style="position:absolute;height:5px;background:blue;top:510;right:0;left:0"></div>-->
		<!--for dev only-->
		<script src="js_libraries/jquery/jquery-3.4.1.min.js"></script>
		<script src="js_libraries/jquery/jquery-ui.js"></script>
		<link rel="stylesheet" href="js_libraries/jquery/jquery-ui.css">
	</body>
</html>

