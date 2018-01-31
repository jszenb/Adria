<?php
	// Volumétrie totale de tous les fonds, quel que soit le critère de recherche
	// Valeurs ramenées par le contrôleur
	$totalMl = 0;
	$totalGo = 0;
	
	// Volumétrie totale des fonds ramenés par le contrôleur dans cette page
	// Valeurs calculées en cours d'affichage
	$totalMlEnCours = 0;
	$totalGoEnCours = 0;	
	
	// Récupération des volumétries totales à partir de ce que le contrôleur a renvoyé. 
	// Les autres valeurs vont être calculées pendant l'affichage
	if (!empty($volumetrie)) {
		foreach ($volumetrie as $uneVolumetrie){
			$totalMl = number_format($uneVolumetrie['sommeMl'], 0);
			$totalGo = number_format($uneVolumetrie['sommeGo'], 0);
			// Il n'y a qu'une ligne dans le résultat : c'est un count
			break;
		}
	}
?>