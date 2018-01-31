<?php

// Fichier : infobulle.php
// Description : création de code javascript pour la gestion d'informations générales sur les champs de saisie dans la page des fonds.
//               Le contrôleur renvoie la liste des tables de références avec les types et les descriptions.
//               Lorsqu'un utilisateur clique sur une bulle d'aide, il reçoit dans une pop-up la liste des types et la description.

function ecrireLienInfobulle($uneRubrique, $unTitre) {
	echo ("<div align='right'><a href=\"javascript:ouvreAide('".$uneRubrique."', '".addslashes($unTitre)."');\">Aide</a></div>");
}

?>
<script type="text/javascript">

function ouvreAide(maRubrique, monTitre) {
	monUrl = "<?php echo $urlAide ; ?>";
	monUrl = monUrl + '?rubrique=' + maRubrique + '&titre=' + monTitre;
	window.open(monUrl,'_blank','toolbar=0,titlebar=0,scrollbars=1,location=0,status=0,menubar=0,resizable=0,width=500,height=500');
}

</script>