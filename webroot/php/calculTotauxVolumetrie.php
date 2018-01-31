	<?php
	// Pour les totaux, il faut distinguer différentes variables :
	// - totalMlEnCours et totalGoEnCours correspondent à des totaux pour les lignes ramenées par la recherche ET en cours d'affichage (donc selon l'état du paginateur)
	// - totalMl et totalGo correspondant aux volumétries totales de tous les fonds non supprimés, indépendant du critère de recherche et de l'affichage (renseigné dans l'appel à prepareVolumetrie.php ci-dessus)
	// - totalMlRecherche et TotalGoRecherche correspondant à des totaux pour les lignes ramenée par la recherche ET INDEPENDAMMENT de l'affichage. 
	// --> Ainsi, TotalMlEnCours n'est potentiellement qu'une fraction de TotalMlRecherche qui lui-même n'est potentiellement qu'une fraction de TotalMl (et resp. avec les Go)
	//     Je dis "potentiellement", car selon les recherches, on pourrait avoir égalité entre les trois : tout dépend de la recherche faite et du paramétrage du paginateur. 
	
	// Calcul de la volumétrie totale retournée par la recherche et en cours d'affichage

		foreach ($fonds as $fond) {

			// Calcul de la volumétrie totale de l'utilisateur :
			$totalMlEnCours = $totalMlEnCours +  $this->Number->format($fond->nb_ml);
			$totalGoEnCours = $totalGoEnCours +  $this->Number->format($fond->nb_go);

		}

	
	// Récupération de la volumétrie totale retournée par la recherche dans son ensemble
	if (!empty($sumMl)) {
		foreach ($sumMl as $uneSumMl){

			$totalMlRecherche = number_format($uneSumMl['somme'], 0);
			// Il n'y a qu'une ligne dans le résultat : c'est une somme
			break;
		}
	}

	if (!empty($sumGo)) {
		foreach ($sumGo as $uneSumGo){
			$totalGoRecherche = number_format($uneSumGo['somme'], 0);
			// Il n'y a qu'une ligne dans le résultat : c'est une somme
			break;
		}
	}
	?>