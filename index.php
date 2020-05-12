<?php
	include "Session.php";
	include "connessione.php";

	$pageName="Homepage";
?>
<html>
	<head>
		<link href="css/fonts.css" rel="stylesheet">
		<script type="text/javascript" src="js_libraries/jquery.min.js"></script>
		<title><?php echo $pageName; ?></title>
		<link rel="stylesheet" href="css/main.css" />
		<script src="js/struttura.js"></script>
		<script src="js/main.js"></script>
	</head>
	<body onload="getPercentualeSviluppiGenerabili('statisticheSwContainer','500px','300px','light2','10px')">
		<?php include('struttura.php'); ?>
		<div id="container">
			<div id="content">
				<div id="immagineLogo" class="immagineLogo" ></div>
				<div class="homepageLinkContainer">
					<div class="homepageLink" data-tooltip="Gestisci le tabelle e compila le anagrafiche" onclick="gotopath('gestioneAnagrafiche.php')">
						<i class="fal fa-database"></i>
						<div>Gestione anagrafiche</div>
					</div>
					<div class="homepageLink" data-tooltip="Genera i programmi plc partendo dai codici sviluppo" onclick="gotopath('generazioneProgrammi.php')">
						<i class="fad fa-layer-plus"></i>
						<div>Generazione programmi</div>
					</div>
					<div class="homepageLink" data-tooltip="Consulta e modifica la tabella parametri" onclick="gotopath('gestioneParametri.php')">
						<i class="fal fa-table"></i>
						<div>Gestione parametri</div>
					</div>
					<div class="homepageLink" data-tooltip="Importa lotti e schede" onclick="gotopath('importazioneLotti.php')">
						<i class="fad fa-file-import"></i>
						<div>Importazione schede lotti</div>
					</div>
				</div>
				<div id="statisticheSwContainer"></div>
			</div>
		</div>
		<div id="footer">
			<b>Marine&nbspInteriors&nbspS.p.A.</b>&nbsp&nbsp|&nbsp&nbspVia&nbspSegaluzza&nbsp33170&nbspPordenone&nbsp&nbsp|&nbsp&nbspPhone:&nbsp(+39)&nbsp0434612811&nbsp|&nbspPowered&nbspby&nbsp<a target="_blank" href="http://www.servizioglobale.it">Servizio Globale S.R.L.</a>
		</div>
	</body>
</html>
