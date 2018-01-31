<?php
function construireValeursCritere ($listeValeurs, $libelle) {
	$chaineType = "";
	
	if (empty($libelle)){
		$libelle = "type";
	}

	foreach($listeValeurs as $uneValeur) {
		
		if ($chaineType == "") {

			$chaineType = "<option value =\"" .  $uneValeur['id'] . "\">" . addslashes($uneValeur[$libelle]) . "</option>";
		}
		else {
			$chaineType = $chaineType . "<option value =\"" . $uneValeur['id'] . "\">" . addslashes($uneValeur[$libelle]) . "</option>";
		}
	}
	
	return $chaineType;
}
?>
