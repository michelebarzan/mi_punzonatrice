<?php
	include "Session.php";
	include "connessione.php";
	
	$pageName="Gestione parametri";
?>
<html>
	<head>
		<link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
		<link rel="stylesheet" href="js_libraries/spinners/spinner.css" />
		<script src="js_libraries/spinners/spinner.js"></script>
		<script src="editableTableParametri/editableTable.js"></script>
		<link rel="stylesheet" href="editableTableParametri/editableTable.css" />
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
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
			<b>Oasis Group</b>  |  Via Favola 19 33070 San Giovanni PN  |  Tel. +39 0434654752
		</div>
	</body>
</html>

