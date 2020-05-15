<?php
	include "Session.php";
	include "connessione.php";
	
	$pageName="Generazione programmi";
?>
<html>
	<head>
		<link href="css/fonts.css" rel="stylesheet">
		<link rel="stylesheet" href="js_libraries/spinners/spinner.css" />
		<script src="js_libraries/spinners/spinner.js"></script>
		<script src="js_libraries/html2canvas.min.js"></script>
		<script src="editableTable/editableTable.js"></script>
		<link rel="stylesheet" href="editableTable/editableTable.css" />
		<script src="js_libraries/sweetalert.js"></script>
		<script src="js_libraries/spinnersV2/spinners.js"></script>
		<link rel="stylesheet" href="js_libraries/spinnersV2/spinners.css" />
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
				font-weight:normal;
				color:black;
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
	<body onload="onLoadActions()">
		<?php include('struttura.php'); ?>
        <!--<div class="absoluteActionBar" id="actionBarGenerazioneProgrammi" style="top:100;display:none">
            <div class="absoluteActionBarSelectContainer">
                Configurazione: 
                <select id="selectConfigurazionePunzoni" style="max-width:150px" class="absoluteActionBarTransparentSelect" onchange="cleanContainerSviluppi()"></select>
            </div>-->
            <!--<div class="absoluteActionBarSelectContainer">
				<select id="selectGruppoSviluppi" style="width:165px" class="absoluteActionBarTransparentSelect" onchange="addCodiciGruppo(this,this.value)"></select>
            </div>-->
            <!--<button class="absoluteActionBarButton" onclick="apriPopupScegliGruppiSviluppi()">Gestisci gruppi sviluppi <i style="margin-left:5px" class="fad fa-cog"></i></button>-->
			<!--<div class="absoluteActionBarElement" style="display:flex;flex-direction:row;align-items:center;">
                <span>Inserisci sviluppi:</span> 
				<textarea id="inputInserisciSviluppo"></textarea>
				<i class="fal fa-plus-circle" id="iconInserisciSviluppi" onclick="addSviluppi(document.getElementById('inputInserisciSviluppo'))"></i>
            </div>
		</div>-->
		<div class="reusable-control-bar" id="actionBarGenerazioneProgrammi">
			<div class="rcb-select-container">
				<span>Configurazione:</span>
				<select style="margin-left:5px" id="selectConfigurazionePunzoni" onchange="cleanContainerSviluppi()"></select>
			</div>
			<div class="rcb-select-container">
				<select style="" id="selectGruppoSviluppi" style="" onchange="addCodiciGruppo(this,this.value)"></select>
			</div>
			<button class="rcb-button-text-icon" onclick="apriPopupScegliGruppiSviluppi()">
				<span>Gestisci gruppi sviluppi</span>
				<i class="fad fa-cog" style="margin-left:5px"></i>
			</button>
			<div class="rcb-input-icon-container">
				<span>Codici</span>
				<textarea title="Inserisci uno o più codici, separandoli con un a capo, oppure incolla una colonna copiata da Excel" id="inputInserisciSviluppo"></textarea>
				<i class="fal fa-plus" onclick="addSviluppi(document.getElementById('inputInserisciSviluppo').value.split('\n'));document.getElementById('inputInserisciSviluppo').value=''" id="iconSearchreportAcquisti"></i>
			</div>
			<!--<div class="rcb-select-container">
				<select id="selectSchedaLotto" style="" onchange="addCodiciSchedaLotto(this,this.value)"></select>
			</div>-->
			<button class="rcb-button-text-icon" id="btnScegliSchedeLotti" onclick="getPopupScegliSchedeLotti(this)">
				<span>Scegli schede lotti</span>
				<i class="fas fa-caret-down" style="margin-left:5px"></i>
			</button>
		</div>
		<div id="containerGenerazioneProgrammi">
			<div class="containerGenerazioneProgrammiTitle">
				<div class="absoluteActionBarElement" style="height:28px;line-height:28px;background-color:#4C91CB;box-sizing:border-box;padding-left:10px;padding-right:10px;margin-left:-5px;color:white">
					Elenco sviluppi 
				</div>
				<button class="absoluteActionBarButton" style="margin-top:4px;width:120px" id="buttonGeneraTuttiSviluppi" onclick="generaTuttiProgrammiSviluppo(false)" >Genera tutti <i class="fad fa-layer-plus" style="margin-left:5px"></i></button>
				<button class="absoluteActionBarButton" style="margin-top:4px;width:120px" id="buttonTrasferisci" onclick="trasferisciProgrammiSviluppo(this)" >Trasferisci <i class="fad fa-microscope" style="margin-left:5px"></i></button>
				<button class="absoluteActionBarButton" style="margin-top:4px;width:120px" onclick="scaricaTuttiProgrammiSviluppo(this,this.parentElement)" >Scarica nc <i class="far fa-download" style="margin-left:5px"></i></button>
				<button class="absoluteActionBarButton" style="margin-top:4px;width:120px" onclick="rimuoviTuttiProgrammiSviluppo()" >Rimovi tutti <i class="far fa-times" style="color:gray;margin-left:5px"></i></button>
				<button class="absoluteActionBarButton" style="margin-top:4px;width:120px" onclick="creaGruppoDaElencoSviluppi()" >Crea gruppo <i class="far fa-object-ungroup" style="color:gray;margin-left:5px"></i></button>
				<button class="absoluteActionBarButton" style="margin-top:4px;width:120px" onclick="getPopupImpostazioni()" >Impostazioni <i class="fad fa-sliders-v" style="margin-left:5px"></i></button>
				<button class="absoluteActionBarButton" style="width:180px;margin-top:4px;display:none" id="buttonTabellaSoluzioniConflittiNomiBreviSviluppi" onclick="getTableConflittiSviluppi()" style="margin-top:4px">Tabella soluzioni conflitti</button>
			</div>
			<div id="containerGenerazioneProgrammiContainerSviluppi"></div>
		</div>
		<div id="footer">
		<b>Marine&nbspInteriors&nbspS.p.A.</b>&nbsp&nbsp|&nbsp&nbspVia&nbspSegaluzza&nbsp33170&nbspPordenone&nbsp&nbsp|&nbsp&nbspPhone:&nbsp(+39)&nbsp0434612811&nbsp|&nbspPowered&nbspby&nbsp<a target="_blank" href="http://www.servizioglobale.it">Servizio Globale S.R.L.</a>
		</div>
		<script src="js_libraries/jquery/jquery-3.4.1.min.js"></script>
		<script src="js_libraries/jquery/jquery-ui.js"></script>
		<link rel="stylesheet" href="js_libraries/jquery/jquery-ui.css">
	</body>
</html>

