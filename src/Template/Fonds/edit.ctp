<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>
<?php 
	// Ne pas retirer la ligne ci-dessous, nécessaire pour faire fonctionner l'aide !! 
	$urlAide = $this->Url->build(['controller' => 'Infobulles', 'action' => 'view']); 
?>

<?php require_once WWW_ROOT . DS . 'php/infobulle.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
	
</nav>

<div class="fonds form large-9 medium-8 columns content">
    <?= $this->Form->create($fond) ?>
    <fieldset>
        <legend><?= __('Modifier le fonds') ?></legend>
        <h4>Identification du fonds</h4>
		<?php
            echo $this->Form->input('nom');
            echo $this->Form->input('annee_deb',['id'=>'annee_deb', 'size'=>4, 'min'=>0, 'type' => 'int', 'label' => 'Date extrême (année de début)']);	
            echo '<p>'.$this->Form->input('annee_fin',['id'=>'annee_fin', 'size'=>4, 'min'=>0,  'type' => 'int', 'label' => 'Date extrême (année de fin)']);
			echo '<p><p>'.$this->Form->checkbox('ind_annee', ['id'=>'ind_annee', 'onChange' => 'onChangeCheckDate()', 'hiddenField' => true]).'Dates extrêmes à renseigner';				
            echo $this->Form->input('cote');
			if ($typeUserEnSession == PROFIL_CA) {
				// Il n'y a qu'une seule valeur possible : celle de l'entité documentaire en session (celle de l'user en cours). Je mets donc empty à false : la valeur par 
				// défaut est donc la seule possible et l'utilisateur ne peut pas la modifier ()puisqu'il n'y a qu'une valeur possible. )
				echo $this->Form->input('entite_doc_id', ['options' => $entiteDocs, 'label' => 'Entité documentaire',  'empty' => false]);
			}
			else {
				echo $this->Form->input('entite_doc_id', ['options' => $entiteDocs, 'label' => 'Entité documentaire', 'empty' => true]);	
			}
			echo $this->Form->input('lieu_conservations._ids', ['options' => $lieuConservations, 'label' => 'Lieu(x) de conservation', 'required' => true]);

		?>
		
        <h4>Contexte</h4>
		<?php
            echo $this->Form->input('producteur');	
			echo $this->Form->input('historique');
            echo $this->Form->input('type_stat_jurid_id', ['options' => $typeStatJurids, 'label' => 'Statut juridique', 'empty' => true]);
			ecrireLienInfobulle("TypeStatJurids", "Statut juridique");
            echo $this->Form->input('type_entree_id', ['options' => $typeEntrees, 'label' => 'Mode d\'entrée', 'empty' => true]);
			ecrireLienInfobulle("TypeEntrees", "Mode d'entrée");
			echo $this->Form->input('type_doc_afferents._ids', ['options' => $typeDocAfferents, 'label' => 'Document(s) afférent(s) au mode d\'entrée', 'required' => true]);	
			ecrireLienInfobulle("TypeDocAfferents", "Document(s) afférent(s) au mode d'entrée");			
		?>
		
		<h4>Contenu et volumétrie</h4>
		<?php
			echo $this->Form->input('type_fond_id', ['options' => $typeFonds, 'label' => 'Type de fonds', 'empty' => true]);
			ecrireLienInfobulle("TypeFonds", "Type de fonds");
			echo $this->Form->input('ind_bib', ['label' => 'Fonds couplé à une collection d\'imprimés']);
			echo $this->Form->input('url_collection', ['label' => 'URL de l\'inventaire de la collection']);				
            echo $this->Form->input('precision_bib', ['label' => 'Précisions sur cette collection']);
			
			echo $this->Form->input('type_docs._ids', ['options' => $typeDocs, 'label' => 'Type de documents', 'required' => true]);	
            ecrireLienInfobulle("TypeDocs", "Type de documents");
			echo $this->Form->input('type_supports._ids', ['options' => $typeSupports, 'label' => 'Type de supports', 'required' => true]);
			ecrireLienInfobulle("TypeSupports", "Type de supports");
			
			echo $this->Form->input('nb_ml', ['id'=>'nb_ml', 'size'=>5, 'min' => 0, 'type' => 'float', 'label' => 'Volumétrie en mètres linéaires']);
			echo '<p>'.$this->Form->checkbox('ind_nb_ml_inconnu', ['id'=>'ind_nb_ml_inconnu', 'onChange' => 'onChangeCheckNbMl();', 'hiddenField' => true]).' Volumétrie en mètres linéaires inconnue';	

			echo $this->Form->input('nb_go', ['id'=>'nb_go','size'=>5, 'min' => 0, 'type' => 'float', 'label' => 'Volumétrie en giga-octets']);		
			echo '<p>'.$this->Form->checkbox('ind_nb_go_inconnu', ['id'=>'ind_nb_go_inconnu','onChange' => 'onChangeCheckNbGo();',  'hiddenField' => true]).' Volumétrie en giga-octets inconnue';			
			
			echo('<br>');
            echo $this->Form->input('type_accroissement_id', ['options' => $typeAccroissements, 'label' => 'Accroissement', 'empty' => true]);
			ecrireLienInfobulle("TypeAccroissements", "Type d'accroissement");
			echo $this->Form->input('thematiques._ids', ['options' => $thematiques, 'label' => 'Discipline(s)']);		
			ecrireLienInfobulle("Thematiques", "Disciplines");
            echo $this->Form->input('aire_culturelles._ids', ['options' => $aireCulturelles, 'label' => 'Aire(s) culturelle(s)']);	
			ecrireLienInfobulle("AireCulturelles", "Aires culturelles");
		?>
		
		<h4>Traitement matériel et intellectuel</h4>
		<?php
            echo $this->Form->input('type_conditionnements._ids', ['options' => $typeConditionnements, 'label' => 'Type de conditionnement', 'required' => true]);
			ecrireLienInfobulle("TypeConditionnements", "Type de conditionnement");
            echo $this->Form->input('type_traitement_id', ['options' => $typeTraitements, 'label' => 'Etat de traitement', 'required' => true, 'empty' => true]);
            ecrireLienInfobulle("TypeTraitements", "Type de traitement");					
			echo $this->Form->input('type_instr_rech_id', ['options' => $typeInstrRechs, 'label' => 'Instrument de recherche', 'empty' => true]);	
			ecrireLienInfobulle("TypeInstrRechs", "Type d'instrument de recherche");
			echo $this->Form->input('url_instr_rech', ['label' => 'URL de l\'instrument de recherche']);						
            echo $this->Form->input('type_numerisation_id', ['options' => $typeNumerisations, 'label' => 'Etat de numérisation', 'empty' => true]);		
			ecrireLienInfobulle("TypeNumerisations", "Type de numérisation");
		?>
		<h4>Observations</h4>
		
		<?php
			echo $this->Form->input('observations', ['label' => '']);
			echo '<p>'.$this->Form->checkbox('ind_maj', ['id' => 'ind_maj', 'hiddenField' => true, 'label' => 'Fiche à jour']).' Fiche à jour</p>';
            //echo $this->Form->input('dt_creation');
            //echo $this->Form->input('dt_der_modif', ['empty' => true, 'default' => '']);
            //echo $this->Form->input('dt_suppr', ['empty' => true, 'default' => '']);
		?>
		
		<h4>Marché de traitement</h4>
		<?php
            echo $this->Form->input('type_prise_en_charge_id', ['options' => $typePriseEnCharges, 'label' => 'Prise en charge du fonds', 'onChange' => 'onChangePriseEnCharge()', 'required' => true, 'empty' => false]);
            ecrireLienInfobulle("TypePriseEnCharges", "Type de prise en charge");					
            echo $this->Form->input('type_realisation_traitement_id', ['options' => $typeRealisationTraitements, 'label' => 'Prestation', 'onChange' => 'onChangeRealisationTraitement()', 'required' => true, 'empty' => false]);
            ecrireLienInfobulle("TypeRealisationTraitements", "Type de réalisation de traitement");					
			echo $this->Form->input('site_intervention', ['label' => 'Site d\'intervention']);		
			$this->Form->templates(['dateWidget' => '{{day}}{{month}}{{year}}']);

			echo $this->Form->input('dt_deb_prestation', ['type' => 'date', 'label' => 'Dates envisagées / effectives (début)', 'minYear' => 2017, 'day' => ['id' => 'dt-deb-prestation-day'], 'month' => ['id' => 'dt-deb-prestation-month'], 'year' => ['id' => 'dt-deb-prestation-year'], 'empty' => true, 'monthNames' => false]);	
			echo $this->Form->input('dt_fin_prestation', ['type' => 'date', 'label' => 'Dates envisagées / effectives (fin)', 'minYear' => 2017, 'day' => ['id' => 'dt-fin-prestation-day'], 'month' => ['id' => 'dt-fin-prestation-month'], 'year' => ['id' => 'dt-fin-prestation-year'],'empty' => true, 'monthNames' => false]);	
			echo $this->Form->input('responsable_operation', ['label' => 'Responsable d\'opérations']);				
		
		?>	
		<h4>Orientation du fonds</h4>
		<label>Lieu de stockage cible</label>
		<table>
			<tr>
				<td width=1%>
				</td>
				<td>
					<?php
	 				echo $this->Form->radio('stockage', [LIB_STOCKAGE_CIBLE[0], LIB_STOCKAGE_CIBLE[1], LIB_STOCKAGE_CIBLE[2], LIB_STOCKAGE_CIBLE[3]]);
					?>
				</td>
			</tr>
		</table>
		<label>Le fonds est-il communicable ?</label>
		<table>
			<tr>
				<td width=1%>
				</td>
				<td>
					<?php
	 				echo $this->Form->radio('communication', [LIB_COMMUNICATION[0], LIB_COMMUNICATION[1], LIB_COMMUNICATION[2]]);
					?>
				</td>
			</tr>
		</table>
		<?php
		/********************************************** DONNEES POUR L'ADMINISTRATEUR *******************************************/
		if ($typeUserEnSession == PROFIL_CC) { 
		?>
		<h4>Implantation en magasin</h4>
		<table>
			<tr>
				<td colspan=5>
					<b>Adresse n°1</b>
				</td>
			</tr>		
			<tr>
				<td>
					Volume en mètres linéaires
				</td>
				<td>
					Magasin n°
				</td>
				<td>
					Epi(s)
				</td>
				<td>
					Travée(s)
				</td>
				<td>
					Tablette(s)
				</td>
			</tr>
			<tr valign="middle">
				<td>
					<?php echo $this->Form->input('adresses.0.id', ['type' => 'hidden']); ?>
					<?php echo $this->Form->input('adresses.0.num_seq', ['type' => 'hidden', 'value' => '0']); ?>
					<?php echo $this->Form->input('adresses.0.volume', ['size'=>5, 'min' => 0, 'type' => 'float',  'onChange' => 'onChangeAdresse(0)', 'label' => '']); ?>
				</td>
				<td>
					<?php echo $this->Form->input('adresses.0.magasin', ['options' => LISTE_MAGASINS, 'empty' => true, 'label' => '']); ?>
				</td>
				<td>
					<?php echo $this->Form->input('adresses.0.epi_deb', ['type' => 'int', 'size' => '2', 'label' => '']); ?> 
					à 
					<?php echo $this->Form->input('adresses.0.epi_fin', ['type' => 'int', 'size' => '2', 'label' => '']); ?>
				</td>
				<td>
					<?php echo $this->Form->input('adresses.0.travee_deb', ['type' => 'int', 'size' => '2', 'label' => '']); ?>
					à 
					<?php echo $this->Form->input('adresses.0.travee_fin', ['type' => 'int', 'size' => '2', 'label' => '']); ?>
				</td>
				<td>
					<?php echo $this->Form->input('adresses.0.tablette_deb', ['type' => 'int', 'size' => '2', 'label' => '']); ?> 
					à 
					<?php echo $this->Form->input('adresses.0.tablette_fin', ['type' => 'int', 'size' => '2', 'label' => '']); ?>				
				</td>
			</tr>	
			<tr>
				<td colspan=5>
					<b>Adresse n°2</b>
				</td>
			</tr>	
			<tr>
				<td>
					Volume en mètres linéaires
				</td>
				<td>
					Magasin n°
				</td>
				<td>
					Epi(s)
				</td>
				<td>
					Travée(s)
				</td>
				<td>
					Tablette(s)
				</td>
			</tr>			
			<tr>
				<td>
					<?php echo $this->Form->input('adresses.1.id', ['type' => 'hidden']); ?>
					<?php echo $this->Form->input('adresses.1.num_seq', ['type' => 'hidden', 'value' => '1']); ?>
					<?php echo $this->Form->input('adresses.1.volume', ['size'=>5, 'min' => 0, 'type' => 'float', 'onChange' => 'onChangeAdresse(1)', 'label' => '']); ?>
				</td>
				<td>
					<?php echo $this->Form->input('adresses.1.magasin', ['options' => LISTE_MAGASINS, 'empty' => true, 'label' => '']); ?>
				</td>
				<td>
					<?php echo $this->Form->input('adresses.1.epi_deb', ['type' => 'int', 'size' => '2', 'label' => '']); ?> 
					à 
					<?php echo $this->Form->input('adresses.1.epi_fin', ['type' => 'int', 'size' => '2', 'label' => '']); ?>
				</td>
				<td>
					<?php echo $this->Form->input('adresses.1.travee_deb', ['type' => 'int', 'size' => '2', 'label' => '']); ?>
					à 
					<?php echo $this->Form->input('adresses.1.travee_fin', ['type' => 'int', 'size' => '2', 'label' => '']); ?>
				</td>
				<td>
					<?php echo $this->Form->input('adresses.1.tablette_deb', ['type' => 'int', 'size' => '2', 'label' => '']); ?> 
					à 
					<?php echo $this->Form->input('adresses.1.tablette_fin', ['type' => 'int', 'size' => '2', 'label' => '']); ?>				
				</td>
			</tr>
			<tr>
				<td colspan=5>
					<b>Adresse n°3</b>
				</td>
			</tr>	
			<tr>
				<td>
					Volume en mètres linéaires
				</td>
				<td>
					Magasin n°
				</td>
				<td>
					Epi(s)
				</td>
				<td>
					Travée(s)
				</td>
				<td>
					Tablette(s)
				</td>
			</tr>			
			<tr>			
				<td>
					<?php echo $this->Form->input('adresses.2.id', ['type' => 'hidden']); ?>
					<?php echo $this->Form->input('adresses.2.num_seq', ['type' => 'hidden', 'value' => '2']); ?>
					<?php echo $this->Form->input('adresses.2.volume', ['size'=>5, 'min' => 0, 'type' => 'float', 'onChange' => 'onChangeAdresse(2)', 'label' => '']); ?>
				</td>
				<td>
					<?php echo $this->Form->input('adresses.2.magasin', ['options' => LISTE_MAGASINS, 'empty' => true, 'label' => '']); ?>
				</td>
				<td>
					<?php echo $this->Form->input('adresses.2.epi_deb', ['type' => 'int', 'size' => '2', 'label' => '']); ?> 
					à 
					<?php echo $this->Form->input('adresses.2.epi_fin', ['type' => 'int', 'size' => '2', 'label' => '']); ?>
				</td>
				<td>
					<?php echo $this->Form->input('adresses.2.travee_deb', ['type' => 'int', 'size' => '2', 'label' => '']); ?>
					à 
					<?php echo $this->Form->input('adresses.2.travee_fin', ['type' => 'int', 'size' => '2', 'label' => '']); ?>
				</td>
				<td>
					<?php echo $this->Form->input('adresses.2.tablette_deb', ['type' => 'int', 'size' => '2', 'label' => '']); ?> 
					à 
					<?php echo $this->Form->input('adresses.2.tablette_fin', ['type' => 'int', 'size' => '2', 'label' => '']); ?>				
				</td>
			</tr>				
		</table>
				
		<?php
			
            echo $this->Form->input('ind_suppr', ['label' => 'Fonds supprimé ?']);
            echo $this->Form->input('raison_suppression_id', ['options' => $raisonSuppressions, 'empty' => true]);
		}
        ?>
		
	
		
    </fieldset>
	
    <?= $this->Form->button(__('Enregistrer')) ?>
    
	<?= $this->Form->end() ?>

</div>
	<!-- ------------------ Gestion relations checkboxes / inputs ------------------ -->
	<script type="text/javascript">
		$priseEnChargeNon = '<?php echo $priseEnChargeNon->id ?>' ;
		$realisationTraitementAucun = '<?php echo $realisationTraitementAucun->id ?>' ;
	</script>
	<?php echo $this->Html->script('Fonds-add-edit.js'); ?>
	
