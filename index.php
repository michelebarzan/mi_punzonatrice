<?php
	include "Session.php";
	include "connessione.php";

	$pageName="Homepage";
?>
<html>
	<head>
		<link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Nunito|Raleway" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Quicksand:300" rel="stylesheet">
		<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
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
				</div>
				<div id="statisticheSwContainer"></div>
			</div>
		</div>
		<div id="footer">
			<b>Oasis Group</b>  |  Via Favola 19 33070 San Giovanni PN  |  Tel. +39 0434654752
		</div>
	</body>
</html>