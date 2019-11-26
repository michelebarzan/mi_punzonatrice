<?php
	include "Session.php";
	include "connessione.php";
	
	$pageName="Generazione programmi";
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
		<link rel="stylesheet" href="css/generazioneProgrammi.css" />
		<script src="js/struttura.js"></script>
		<script src="js/generazioneProgrammi.js"></script>
		<script src="js/main.js"></script>
		<script>var php_session_id="<?php echo session_id(); ?>";</script>
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
	<body onload="addOptionsConfigurazioni();addOptionsGruppiSviluppi();/*addOptionsListaSviluppi()*/">
		<?php include('struttura.php'); ?>
        <div class="absoluteActionBar" style="top:100">
            <div class="absoluteActionBarSelectContainer">
                Configurazione: 
                <select id="selectConfigurazionePunzoni" style="width:70px" class="absoluteActionBarTransparentSelect" onchange="cleanContainerSviluppi()"></select>
            </div>
            <div class="absoluteActionBarSelectContainer">
                Gruppo sviluppi: 
                <select id="selectGruppoSviluppi" style="width:70px" class="absoluteActionBarTransparentSelect" onchange="addCodiciGruppo(this.value)"></select>
            </div>
            <button class="absoluteActionBarButton" onclick="apriPopupNuovoGruppoSviluppi()">Crea nuovo gruppo sviluppi <i style="margin-left:5px" class="far fa-object-ungroup"></i></button>
            <div class="absoluteActionBarElement">
                Inserisci sviluppo: 
                <input type="text" id="inputInserisciSviluppo" maxlength="10">
            </div>
			<button class="absoluteActionBarButton" onclick="addSviluppo(document.getElementById('inputInserisciSviluppo'),document.getElementById('inputInserisciSviluppo').value)" style="margin-left:0px"><i class="fad fa-check-double"></i></button>
		</div>
		<div id="containerGenerazioneProgrammi">
			<div class="containerGenerazioneProgrammiTitle">
				<div class="absoluteActionBarElement" style="height:28px;line-height:28px;background-color:#4C91CB;box-sizing:border-box;padding-left:10px;padding-right:10px;margin-left:-5px;color:white">
					Elenco sviluppi 
				</div>
				<button class="absoluteActionBarButton" style="width:100px;margin-top:4px" id="buttonGeneraTuttiSviluppi" onclick="generaTuttiProgrammiSviluppo()" style="margin-top:4px">Genera tutti</button>
				<button class="absoluteActionBarButton" style="width:100px;margin-top:4px" onclick="scaricaTuttiProgrammiSviluppo(this,this.parentElement)" style="margin-top:4px">Scarica tutti</button>
				<button class="absoluteActionBarButton" style="width:170px;margin-top:4px">
					<label class="pure-material-checkbox">
						<input type="checkbox" checked id="checkboxControlloConflitti">
						<span>Controllo conflitti</span>
					</label>
				</button>
				<button class="absoluteActionBarButton" style="width:180px;margin-top:4px;display:none" id="buttonTabellaSoluzioniConflittiNomiBreviSviluppi" onclick="getTableConflittiSviluppi()" style="margin-top:4px">Tabella soluzioni conflitti</button>
			</div>
			<div id="containerGenerazioneProgrammiContainerSviluppi"></div>
		</div>
		<div id="footer">
			<b>Oasis Group</b>  |  Via Favola 19 33070 San Giovanni PN  |  Tel. +39 0434654752
		</div>
		<script src="js_libraries/jquery/jquery-3.4.1.min.js"></script>
		<script src="js_libraries/jquery/jquery-ui.js"></script>
		<link rel="stylesheet" href="js_libraries/jquery/jquery-ui.css">
	</body>
</html>

