<?php 
/********************************************************************************************
** Fichier : recherche.ctp
** Vue gérant l'écran de recherche
** Contrôleur : Fonds
** La particularité de cet écran réside dans la construction en JS (jquery) de la liste des
** critères dynamiquement selon les choix de l'utilisateur. 
********************************************************************************************/
?>

<?php require_once WWW_ROOT . DS . 'php/construireValeursCritere.php'; ?>
<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>
<?php $listeCritere = [
						0  => '',
						1  => 'Nom du fonds', 
						5  => 'Producteur',
						6  => 'Statut juridique',
						7  => 'Mode d\'entrée',
						8  => 'Document afférent',
						9  => 'Type de fonds',
						10 => 'Fonds couplé à une collection d\'imprimés',
						11 => 'Type de document',
						12 => 'Type de support',
						22 => 'Volumétrie ml',
						23 => 'Volumétrie Go',
						//13 => 'Support numérique',
						//14 => 'Support physique',
						15 => 'Accroissement',
						19 => 'Conditionnement',
						20 => 'Etat de traitement',
						16 => 'Instrument de recherche',
						26 => 'URL de l\'instrument de recherche',
						21 => 'Numérisation',
						17 => 'Discipline',
						18 => 'Aire culturelle',
						2  => 'Code entité documentaire', 
						3  => 'Code établissement', 
						4  => 'Nom du lieu de conservation',
						24 => 'Cote',
						25 => 'Dates extrêmes',
						27 => 'Fiche de fonds modifiée',
						28 => 'Fiche de fonds à jour',
                                                29 => 'Prise en charge du fonds',
                                                30 => 'Prestation',
                                                31 => 'Lieu de stockage cible',
                                                32 => 'Communicabilité du fonds'
					];
		$listeOperande = [
						0 => '',
						1 => '=',
						2 => 'commence par',
						3 => 'différent de',
						4 => 'supérieure ou égale à',
						5 => 'inférieure ou égale à',
						6 => 'compris entre (année)',
						7 => 'renseigné'
						
		];
		if ($typeUserEnSession == PROFIL_CA) {
			$listePerimetre = [
						0 => 'Tous les fonds', // On force par défaut la recherche à "tous les fonds"
						1 => 'Vos fonds',
						2 => 'Autres fonds'
			];		
		}
		else {
			$listePerimetre = [
						0 => 'Tous les fonds' // On force par défaut la recherche à "tous les fonds". L'utilisateur Consultation voit nécessairement tous les fonds
			];		
		}
		
		$listeLimite = [
						'' => 'Résultats par page',
						20 => '20',
						30 => '30',
						40 => '40',
						50 => '50',
						80 => '80',
						100 => '100'
		];			
		/*$listeTri = [
						0 => '',
						1 => 'Nom croissant',
						2 => 'Nom décroissant',
						3 => 'Volumétrie ml croissante',
						4 => 'Volumétrie ml décroissante',
						5 => 'Volumétrie Go croissante',
						6 => 'Volumétrie Go décroissante'
		];*/
?>
<script language="javascript">
function prepareSubmit() {
	 if (document.forms["f_search"].valeur.type != "text") {
		 document.forms["f_search"].hi_valeur.value = document.getElementById("valeur")[document.getElementById("valeur").selectedIndex].innerHTML;
	 }
	 else {
		 document.forms["f_search"].hi_valeur.value = document.forms["f_search"].valeur.value;
	 }

	 // Pour les volumétries, contrôle du format : 
	 if ( (document.forms["f_search"].critere.value == 22) || (document.forms["f_search"].critere.value == 23) ) {
		 //alert(isNaN(document.forms["f_search"].valeur.value));
		 if (isNaN(document.forms["f_search"].valeur.value)) {
			 alert('Veuillez saisir une valeur numérique\nRemarque : le caractère décimal est le point.');
			 return false;
		 }
	 }	 
	//alert(document.getElementById("valeur")[document.getElementById("valeur").selectedIndex].innerHTML);
	//alert(document.forms["f_search"].hi_valeur.value);	
	return true;
}
</script>

<nav class="large-3 medium-4 columns" id="actions-sidebar">

	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
	
</nav>
<div class="fonds index large-9 medium-8 columns content">
    <h3><?= __('Interroger la base') ?></h3>
	<div class="recherche">
	 
		<?=	$this->Form->create(null, ['type' => 'get', 'url' => '/fonds/recherche', 'name' => 'f_search', 'onsubmit' => 'return prepareSubmit();']); ?>
		<?= $this->Form->input('hi_valeur', ['type' => 'hidden', 'label' => ''], ['class' => 'hi_valeur']) ?>
		<table class="recherche">
		<tr class="recherche">
			<td width="20%" class="recherche"><?= $this->Form->select('critere', $listeCritere , ['class' => 'critere'] ) ?></td>
			<td width="15%" class="recherche"><?= $this->Form->select('operande',[], ['class' => 'operande']) ?></td>	
			<td width="25%" class="recherche"><?= $this->Form->input('valeur', ['type' => 'text', 'label' => ''], ['class' => 'valeur']) ?></td>
			<td width="10%" class="recherche"><?= $this->Form->select('perimetre', $listePerimetre , ['class' => 'perimetre'] ) ?></td>
			<td width="15%" class="recherche"><?= $this->Form->select('limite', $listeLimite, ['class' => 'limite']) ?></td>
			<?php //<td width="15%" class="recherche"><?= $this->Form->select('tri', $listeTri, ['label' => 'Ordre de tri', 'class' => 'tri']) ?>
			<td width="15%" class="recherche"><?= $this->Form->button('Rechercher', ['type' => 'submit']) ?></td>
		</tr>	
		</table>
		<?= $this->Form->end(); ?>
	</div>
    
	<?php 
	/* Préparation au comptage des volumétries et à l'affichage des résultats de volumétrie */
	require_once WWW_ROOT . DS . 'php/prepareVolumetrie.php';
	
	?>
	
	<!-- ******************************* Fonds de l'utilisateur connecté ******************************* -->	
    
	<?php 				
	// A-t-on des résultats
	
	// A-t-on ce genre de fonds ?
	$fondsPresent = false;
	
	
	
	if ( $count > 0) {
		$fondsPresent = true ;
	}
	

	// Pour les totaux, il faut distinguer différentes variables :
	// - totalMlEnCours et totalGoEnCours correspondent à des totaux pour les lignes ramenées par la recherche ET en cours d'affichage (donc selon l'état du paginateur)
	// - totalMl et totalGo correspondant aux volumétries totales de tous les fonds non supprimés, indépendant du critère de recherche et de l'affichage (renseigné dans l'appel à prepareVolumetrie.php ci-dessus)
	// - totalMlRecherche et TotalGoRecherche correspondant à des totaux pour les lignes ramenée par la recherche ET INDEPENDAMMENT de l'affichage. 
	// --> Ainsi, TotalMlEnCours n'est potentiellement qu'une fraction de TotalMlRecherche qui lui-même n'est potentiellement qu'une fraction de TotalMl (et resp. avec les Go)
	//     Je dis "potentiellement", car selon les recherches, on pourrait avoir égalité entre les trois : tout dépend de la recherche faite et du paramétrage du paginateur. 
	
	// Calcul de la volumétrie totale retournée par la recherche et en cours d'affichage
	if ($fondsPresent) {
		require_once WWW_ROOT . DS . 'php/calculTotauxVolumetrie.php';
	}

	
	// Préparation du passage des paramètres pour gérer la pagination sans perdre la recherche en cours
	if ($fondsPresent) { 
		$url = [
            'critere' => $rappelCritere,
			'operande' => $rappelOperande,
			'valeur' => $rappelValeur,
			'hi_valeur' => $rappelHi_valeur,
			'perimetre' => $rappelPerimetre,
			'dateDeb' => $rappelDateDeb,
			'dateFin' => $rappelDateFin,
			'changepage' => '1'
        
		];
		
		if ($rappelCritere == 25) {
			$rappelHi_valeur = $rappelDateDeb .' et '. $rappelDateFin;
		}

	?>

	<h4>Résultat de la recherche</h4>
	<p>
		<?php 
			if (!empty($rappelCritere) ) { 
				echo ("<b>" . $listeCritere[$rappelCritere] . " " 
							. $listeOperande[$rappelOperande] . " \"" 
							. $rappelHi_valeur . "\" : " 
							. $count . " fonds trouvé(s) dans le périmètre \"" 
							. $listePerimetre[$rappelPerimetre] . "\" représentant " 
							. str_replace(',', '', $totalMlRecherche) . " ml (sur "
							. str_replace(',', '', $totalMl) . ") et "
							. str_replace(',', '', $totalGoRecherche) . " Go (sur "
							. str_replace(',', '', $totalGo) . ")</b>"); 
			}
		?> 
	</p>
	<?php 
	
	$typeTraitementEnCours = null;
	if ($fondsPresent) { ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>		
                <th width=40%><?= $this->Paginator->sort('nom', 'Nom', ['url' => $url, 'direction' => 'desc']) ?></th>
		<th width=10%><?= $this->Paginator->sort('Etablissements.code','Etablissement', ['url' => $url]) ?></th>
                <th width=10%><?= $this->Paginator->sort('EntiteDocs.code', 'Entité documentaire', ['url' => $url]) ?></th>
		<th width=13%><?= $this->Paginator->sort('TypeFonds.type', 'Type de fonds', ['url' => $url]) ?></th>
                <th width=6%><?= $this->Paginator->sort('nb_ml', 'Vol. ml', ['url' => $url]) ?></th>
                <th width=6%><?= $this->Paginator->sort('nb_go', 'Vol. Go', ['url' => $url]) ?></th>				
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php 
			foreach ($fonds as $fond): 
					if ( ($typeUserEnSession == PROFIL_CC) || ( (in_array($typeUserEnSession, [PROFIL_CA, PROFIL_CO])) && (!$fond->ind_suppr) ) ): ?>
					<tr>
                                               <?php $dateAffichee = '' ;
                                                     empty($fond->dt_der_modif) ? $dateAffichee = $fond->dt_creation->nice('Europe/Paris', 'fr-FR') : $dateAffichee = $fond->dt_der_modif->nice('Europe/Paris', 'fr-FR') ;
                                               ?>
                                                <td><?= h($fond->nom) ?><?= $fond->ind_maj ? '&nbsp;&#x2714; (' .  $dateAffichee . ')' : '' ?>

						<?php if ($fond->ind_suppr) {echo('<b>(supprimé)</b>');} ?>
						</td>
						<td><?= $fond->entite_doc->has('etablissement') ? $this->Html->link($fond->entite_doc->etablissement->code, ['controller' => 'Etablissements', 'action' => 'view', $fond->entite_doc->etablissement->id]) : '' ?></td>
						<td><?= $fond->has('entite_doc') ? $this->Html->link($fond->entite_doc->code, ['controller' => 'EntiteDocs', 'action' => 'view', $fond->entite_doc->id]) : '' ?></td>
						<td><?= $fond->has('type_fond') ? h($fond->type_fond->type) : '' ?></td>				
						<td><?= $fond->ind_nb_ml_inconnu ? h('inconnue') : $this->Number->format($fond->nb_ml) ?></td>
						<td><?= $fond->ind_nb_go_inconnu ? h('inconnue') : $this->Number->format($fond->nb_go) ?></td>							
						<td class="actions">
							<?= $this->Html->link(__('Consulter'), ['action' => 'view', $fond->id]) ?>
							<?php if ( ($typeUserEnSession == PROFIL_CC) || ( ($typeUserEnSession == PROFIL_CA) && ($idEntiteDocEnSession == $fond->entite_doc->id) ) ){ ?>
								<?= $this->Html->link(__('Modifier'), ['action' => 'edit', $fond->id]) ?>
							<?php } ?>
							<?php /* if ($typeUserEnSession == PROFIL_CC) { ?>
								<?= $this->Form->postLink(__('Supprimer'), ['action' => 'delete', $fond->id], ['confirm' => __('Voulez-vous vraiment supprimer le fonds {0} ?', $fond->nom)]) ?>
							<?php }*/ ?>
						</td>
					</tr>
					<?php 
					endif;
			endforeach; ?>
			<!-- Volumétrie totale
			<tr>
				<td/>
				<td/>
				<td/>
				<td class="right"><b>Total affiché :</b></td>
				<td><b><?php echo(number_format($totalMlEnCours, 0, '.', '') . " / " . str_replace(',','',$totalMl)) ?></b></td>
				<td><b><?php echo(number_format($totalGoEnCours, 0, '.', '') . " / " . str_replace(',','',$totalGo)) ?></b></td>
				<td/>
			</tr>  -->
        </tbody>
    </table>
	<br>
	<?php } 
	?>
	<div class="paginator">
        <ul class="pagination">
		<?= $this->Paginator->first('Début',['escape' => false] ) ?>
		<?= $this->Paginator->hasPrev() ? $this->Paginator->prev('< ' . __('précédent'), ['url' => $url] ) : '' ?>
		<?= $this->Paginator->numbers(['url' => $url]) ?>
		<?= $this->Paginator->hasNext () ?$this->Paginator->next(__('suivant') . ' >', ['url' => $url]) : '' ?>
		<?= $this->Paginator->last('Fin', ['escape' => false] ) ?>
        </ul>
        <p><?= $this->Paginator->counter() ?></p>
	</div>
	<?php } 
	else {
		if (!empty($rappelCritere)) { 
			echo("<h4>Résultat de la recherche</h4>");
			echo ("<p><b>" . $listeCritere[$rappelCritere] . " " . $listeOperande[$rappelOperande] . " \"" . $rappelHi_valeur . "\" : pas de fonds correspondant à ce critère.</b>");
		}
	}?>

</div>
<?php echo $this->Html->script('jquery-2.1.4.min.js'); ?>
<script type="text/javascript">
$(function() {

	// On commence par repositionner le critère de recherche : c'est une liste fixe. On ne repositionne pas
	// encore les valeurs d'opérande et de valeur avant que la liste des valeurs soit reconstruite par le code qui suit.
	document.forms["f_search"].critere.value = '<?php echo ($rappelCritere); ?>';
	document.forms["f_search"].perimetre.value = '<?php echo ($rappelPerimetre); ?>' ;
	document.forms["f_search"].limite.value = '<?php echo ($rappelLimite); ?>' ;	
	
});
</script>
<script type="text/javascript">
$(function() { 
	// Construction dynamique des critères de recherche
	
	// Etablissement des correspondances des critères de recherche
    var selectValues = {
        "0": {
		// vide
        },
		// Nom du fonds
	"1": {
            "1": "=",
            "2": "Commence par"
        },
		// Code Entité documentaire
        "2": {
            "1": "=",
            "2": "Commence par"
        },
		// Code Etablissement
        "3": {
            "1": "=",
            "2": "Commence par"
        },
		// Nom du lieu de conservation
        "4": {
            "1": "=",
            "2": "Commence par"
        },	
		// Producteur
        "5": {
            "1": "=",
            "2": "Commence par"
        },
		// Statut juridique
        "6": {
            "1": "=",
            "3": "Différent de"
        },	
		// Mode d'entrée
        "7": {
            "1": "=",
            "3": "Différent de"
        },	
		// Documents afférents
        "8": {
            "1": "=",
           "3": "Différent de"
        },			
		// Type de fonds
        "9": {
            "1": "=",
            "3": "Différent de"
        },
		// Couplé à une collection d'imprimés
        "10": {
            "1": "="
        },
		// Type de document
        "11": {
            "1": "=",
            "3": "Différent de"
        },
		// Type de support
        "12": {
            "1": "=",
            "3": "Différent de"
        },		
		// Support numérique
        "13": {
            "1": "="
        },
		// Support physique
        "14": {
            "1": "="
        },
		// Accroissement
        "15": {
            "1": "=",
            "3": "Différent de"			
        },
		// Type d'instrument de recherche
        "16": {
            "1": "=",
            "3": "Différent de"			
        },
		// Thématique
        "17": {
            "1": "=",
            "3": "Différent de"			
        },
		// Aires culturelles
        "18": {
            "1": "=",
            "3": "Différent de"			
        },
		// Type de conditionnement
        "19": {
            "1": "=",
            "3": "Différent de"			
        },
		// Type de traitement
        "20": {
            "1": "=",
            "3": "Différent de"			
        },
		// Type de numérisation
        "21": {
            "1": "=",
            "3": "Différent de"			
        },
		// Volumétrie ml et Go
        "22": {
            "4": "Supérieure ou égale",
            "5": "Inférieure ou égale"
		},
         "23": {
            "4": "Supérieure ou égale",
            "5": "Inférieure ou égale"
		},
		// Cote
         "24": {
            "1": "=",
            "2": "Commence par"	,	
            "3": "Différent de"
		},
		// Dates
         "25": {
            "6": 'compris entre (année)'
		},
		// URL de l'instrument de recherche
         "26": {
            "7": "renseigné"
		},
		// Modification ?
         "27": {
            "0": ""		
		},
		// Fiche de fonds à jour ?
         "28": {
            "1": "="		
		},
		// Type de prise en charge
 	"29": {
            "1": "=",
            "3": "Différent de"
		},
		// Type de traitement envisagé
 	"30": {
            "1": "=",
            "3": "Différent de"
		},
		// Stockage cible
         "31": {
            "1": "=",
            "3": "Différent de"
		},
		// Communicabilité ?
         "32": {
            "1": "=",
            "3": "Différent de"
		}
    };

    var $critere = $('select.critere');
    var $operande = $('select.operande');
	// Adaptation de la liste des opérandes selon le critère :
    $critere.change(function() {
		

		$operande.empty().append(function() {
			var output = '';
			$.each(selectValues[$critere.val()], function(key, value) {		
				output += '<option value="' + key +'">' + value + '</option>'	
			});
			return output;	
		});
		
		// Pas d'opérande pour le 27e critère (fiche de fonds modifiée) : on masque. Sinon on affiche. 
		$critere.val() == '27' ? $operande.hide() : $operande.show() ;
		
		// Détermination du mode de saisie de la valeur associée au critère selon ce dernier,
		// et si besoin, remplissage d'une liste de valeurs possibles. 
		switch ($critere.val()) {
			case '0':
				var $clauseSelect = '<select name="valeur" class="valeur" id="valeur"></select>';
				$('#valeur').replaceWith($clauseSelect);	
				break;
			// Remplissage de la liste des valeurs possibles pour type de statut juridique.
			case '6':	
				var $typeStatJurids = '<?php echo construireValeursCritere($typeStatJurids, null) ;  ?>';
				var $clauseSelect = '<select name="valeur" class="valeur" id="valeur">' + $typeStatJurids + '</select>';
				$('#valeur').replaceWith($clauseSelect);	
				break;	
			// Remplissage de la liste des valeurs possibles pour mode d'entrée.
			case '7':	
				var $typeEntrees = '<?php echo construireValeursCritere($typeEntrees, null) ; ?>';
				var $clauseSelect = '<select name="valeur" class="valeur" id="valeur">' + $typeEntrees + '</select>';
				$('#valeur').replaceWith($clauseSelect);	
				break;	
			// Remplissage de la liste des valeurs possibles pour documents afférents.
			case '8':	
				var $typeDocAfferents = '<?php echo construireValeursCritere($typeDocAfferents, null) ; ?>';
				var $clauseSelect = '<select name="valeur" class="valeur" id="valeur">' + $typeDocAfferents + '</select>';
				$('#valeur').replaceWith($clauseSelect);	
				break;					
			// Remplissage de la liste des valeurs possibles pour type de fonds.
			case '9':	
				var $typeFonds = '<?php echo construireValeursCritere($typeFonds, null) ; ?>';
				var $clauseSelect = '<select name="valeur" class="valeur" id="valeur">' + $typeFonds + '</select>';
				$('#valeur').replaceWith($clauseSelect);	
				break;
			// Indicateur pour le couplage aux imprimés
			case '10':
				var $clauseSelect = '<select name="valeur" class="valeur" id="valeur"><option value="0">non</option><option value="1">oui</option></select>';
				$('#valeur').replaceWith($clauseSelect);
				break;
			// Remplissage de la liste des valeurs possibles pour type de documents
			case '11':
				var $typeDocs = '<?php echo construireValeursCritere($typeDocs, null) ; ?>';
				var $clauseSelect = '<select name="valeur" class="valeur" id="valeur">' + $typeDocs + '</select>';
				$('#valeur').replaceWith($clauseSelect);
				break;	
			// Remplissage de la liste des valeurs possibles pour type de support (inutilisé pour le moment)
			case '12':
				var $typeSupports = '<?php echo construireValeursCritere($typeSupports, null) ; ?>';
				var $clauseSelect = '<select name="valeur" class="valeur" id="valeur">' + $typeSupports + '</select>';
				$('#valeur').replaceWith($clauseSelect);
				break;
			// Indicateur pour le support numérique, le support physique et l'URL de l'instrument de recherche: même valeurs de critère
			case '13':
			case '14':
			case '26':
			case '28':			
				var $clauseSelect = '<select name="valeur" class="valeur" id="valeur"><option value="0">non</option><option value="1">oui</option></select>';
				$('#valeur').replaceWith($clauseSelect);	
				break;		
			// Remplissage de la liste des valeurs possibles pour type d'accroissement
			case '15':
				var $typeAccroissements = '<?php echo construireValeursCritere($typeAccroissements, null) ; ?>';
				var $clauseSelect = '<select name="valeur" class="valeur" id="valeur">' + $typeAccroissements + '</select>';
				$('#valeur').replaceWith($clauseSelect);
				break;
			// Remplissage de la liste des valeurs possibles pour type d'instrument de recherche
			case '16':
				var $typeInstrRechs = '<?php echo construireValeursCritere($typeInstrRechs, null) ; ?>';
				var $clauseSelect = '<select name="valeur" class="valeur" id="valeur">' + $typeInstrRechs + '</select>';
				$('#valeur').replaceWith($clauseSelect);
				break;	
			// Remplissage de la liste des valeurs possibles pour thématique
			case '17':
				var $thematiques = '<?php echo construireValeursCritere($thematiques, "intitule") ; ?>';
				var $clauseSelect = '<select name="valeur" class="valeur" id="valeur">' + $thematiques + '</select>';
				$('#valeur').replaceWith($clauseSelect);
				break;		
			// Remplissage de la liste des valeurs possibles pour aires culturelles
			case '18':
				var $airesCulturelles = '<?php echo construireValeursCritere($aireCulturelles, "intitule") ; ?>';
				var $clauseSelect = '<select name="valeur" class="valeur" id="valeur">' + $airesCulturelles + '</select>';
				$('#valeur').replaceWith($clauseSelect);
				break;
			// Remplissage de la liste des valeurs possibles pour type de conditionnement
			case '19':
				var $typeConditionnements = '<?php echo construireValeursCritere($typeConditionnements, null) ; ?>';
				var $clauseSelect = '<select name="valeur" class="valeur" id="valeur">' + $typeConditionnements + '</select>';
				$('#valeur').replaceWith($clauseSelect);
				break;
			// Remplissage de la liste des valeurs possibles pour type de traitement
			case '20':
				var $typeTraitements = '<?php echo construireValeursCritere($typeTraitements, null) ; ?>';
				var $clauseSelect = '<select name="valeur" class="valeur" id="valeur">' + $typeTraitements + '</select>';
				$('#valeur').replaceWith($clauseSelect);
				break;
			// Remplissage de la liste des valeurs possibles pour type de numérisation
			case '21':
				var $typeNumerisations = '<?php echo construireValeursCritere($typeNumerisations, null) ; ?>';
				var $clauseSelect = '<select name="valeur" class="valeur" id="valeur">' + $typeNumerisations + '</select>';
				$('#valeur').replaceWith($clauseSelect);
				break;		
			case '25':
				$('#valeur').replaceWith('<span id="valeur" name="valeur" ><input size=4 type="int" min=0 maxlength=4 id="dateDeb" name="dateDeb"> et <input type="int" min=0 id="dateFin" name="dateFin" maxlength=4 size=4></span>');	
				break;		
			case '27':	
				var $clauseSelect = '<select name="valeur" class="valeur" id="valeur"><option value="0">oui</option><option value="1">oui, dans le dernier mois</option><option value="2">oui, dans le dernier trimestre</option><option value="3">non</option></select>';
				$('#valeur').replaceWith($clauseSelect);	
				break;					
			// Remplissage de la liste des valeurs possibles pour type de prise en charges
			case '29':
				var $typePriseEnCharges = '<?php echo construireValeursCritere($typePriseEnCharges, null) ; ?>';
				var $clauseSelect = '<select name="valeur" class="valeur" id="valeur">' + $typePriseEnCharges + '</select>';
				$('#valeur').replaceWith($clauseSelect);
				break;		
			// Remplissage de la liste des valeurs possibles pour type de realisation de traitement envisagé
			case '30':
				var $typeRealisationTraitements = '<?php echo construireValeursCritere($typeRealisationTraitements, null) ; ?>';
				var $clauseSelect = '<select name="valeur" class="valeur" id="valeur">' + $typeRealisationTraitements + '</select>';
				$('#valeur').replaceWith($clauseSelect);
				break;		
			case '31':
				var $clauseSelect = '<select name="valeur" class="valeur" id="valeur"><option value="0"><?php echo addslashes(LIB_STOCKAGE_CIBLE[0]) ?></option><option value="1"><?php echo addslashes(LIB_STOCKAGE_CIBLE[1]) ?></option><option value="2"><?php echo addslashes(LIB_STOCKAGE_CIBLE[2]) ?></option><option value="3"><?php echo addslashes(LIB_STOCKAGE_CIBLE[3]) ?></option></select>';
				$('#valeur').replaceWith($clauseSelect);	
				break;					
			case '32':
				var $clauseSelect = '<select name="valeur" class="valeur" id="valeur"><option value="0"><?php echo addslashes(LIB_COMMUNICATION[0]) ?></option><option value="1"><?php echo addslashes(LIB_COMMUNICATION[1]) ?></option><option value="2"><?php echo addslashes(LIB_COMMUNICATION[2]) ?></option></select>';
				$('#valeur').replaceWith($clauseSelect);	
				break;					
			default:
				$('#valeur').replaceWith('<input type="text" id="valeur" name="valeur" class="valeur">');	
				break;
		}
    }).change();
});
</script>
<script type="text/javascript">
$(function() {

	// La liste des valeurs et des opérandes a été reconstruite : on repositionne donc 
	// les paramètres donnés à la recherche.
    
	document.forms["f_search"].operande.value = <?php echo ($rappelOperande); ?> ;
	if (document.forms["f_search"].critere.value != 25) {
		document.forms["f_search"].valeur.value = "<?php echo ($rappelValeur); ?>" ;
	}
	else {
		document.forms["f_search"].dateDeb.value = "<?php echo ($rappelDateDeb); ?>" ;	
		document.forms["f_search"].dateFin.value = "<?php echo ($rappelDateFin); ?>" ;	
	}
		
});
</script>

