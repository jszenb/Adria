<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">

	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
	
</nav>
<div class="fonds index large-9 medium-8 columns content">
	
<h3><?= __('Génération de rapports') ?></h3>
    <?= $this->Form->create(null, ['type' => 'get', 'url' => '', 'name' => 'f_rapport', 'onsubmit' => 'return false;']) ?>
    <fieldset>
        <legend><?= __('Choisissez le rapport à générer') ?></legend>

        <?php
			if ($typeUserEnSession == PROFIL_CC) {
				$options = [
					'ListeDetailleeFonds' => 'Liste détaillée des fonds rejoignant le GED',
					'VolumetrieParLieuxEtablissementsEntites' => 'Volumétrie par lieux de conservation, établissements et entités documentaires',
					'ListeFondsParEntiteDocsEtLieuxStockageCible' => 'Liste des fonds par entité documentaire et par lieu de stockage cible',
					'ListeFondsParSiteDepart' => 'Liste des fonds par site de départ',
					'ListeMagasin' => 'Implantation en magasins'
				];
			} else {
				$options = [
					'ListeDetailleeFonds' => 'Liste détaillée des fonds rejoignant le GED',
					'ListeFondsParEntiteDocsEtLieuxStockageCible' => 'Liste des fonds par entité documentaire et par lieu de stockage cible',
					'ListeFondsParSiteDepart' => 'Liste des fonds par site de départ'
				];
			}
			
			$colonneTri = [
				'Fonds.nom' => 'Nom du fonds',
				'Type_fonds.nom' => 'Type de fonds',
				'Fonds.nb_ml' => 'Métrage linéaire',
				'Fonds.nb_go' => 'Nb de Go'
			];
			
			$ordreTri = [
				'asc' => 'croissant',
				'desc' => 'décroissant'
			];
			?>
			<table class="recherche" width="100%">
				<tr class="recherche">
					<td class="recherche" width="50%" >
						<?php echo $this->Form->input('typerapport', ['options' => $options, 'label' => '', 'class' => 'recherche', 'empty' => true, 'onChange' => "rapportChange();"]);	?>
					</td>
					<td class="recherche" width="30%">
						<?php echo $this->Form->input('entitedoc', ['options' => $entiteDocs, 'label' => '', 'empty' => 'Choisissez une entité documentaire dans la liste ci-dessous', 'class' => 'recherche', 'onChange' => "entiteChange();"]); ?>
					</td>
					<td class="recherche" width="20%">
						<?php echo $this->Form->button('Générer', ['type' => 'button', 'id' => 'subButton', 'onclick'=>"javascript:$('#rapport').click();"]) ; ?>
					</td>
				</tr>
				<!--
				<tr class="recherche">
					<td class="recherche" width="70%">
						Ordre de tri (1er niveau) : 
						<?php echo $this->Form->input('colonneTri1', ['options' => $colonneTri, 'label' => '', 'class' => 'recherche']);	?>
					</td>
					<td class="recherche" width="30%">
						Sens du tri : 
						<?php echo $this->Form->input('ordreTri1', ['options' => $ordreTri, 'label' => '', 'class' => 'recherche', 'empty' => false]); ?>
					</td>
				</tr>	
				<tr class="recherche">
					<td class="recherche" width="70%">
						Ordre de tri (2e niveau) : 
						<?php echo $this->Form->input('colonneTri2', ['options' => $colonneTri, 'label' => '', 'class' => 'recherche']);	?>
					</td>
					<td class="recherche" width="30%">
						Sens du tri : 
						<?php echo $this->Form->input('ordreTri2', ['options' => $ordreTri, 'label' => '', 'class' => 'recherche', 'empty' => false]); ?>
					</td>
				</tr>
				-->
			</table>

    </fieldset>		
    <?= $this->Form->end() ?>	
	<?= $this->Html->link(__('Cliquez ici pour générer le rapport choisi'),  
						[	'action' => 'generatepdf', '?' => ['mode' => ''] ], 
						['id' => 'rapport', 
						 'title'=>'',
						 'onclick'=>'javascript:window.open(this.href,\'_blank\',\'toolbar=0,scrollbars=0,location=0,status=0,menubar=0,resizable=0,width=400,height=100\');return false;']) ?>

</div>
<?php echo $this->Html->script('jquery-2.1.4.min.js'); ?>
<script type="text/javascript">
$(document).ready(function() {
	var entDoc = '' ;

	// Ajout d'une possibilité de génération de graphique on le met après la première ligne
	$('#entitedoc :nth-child(1)').after("<option value='all'>Toutes entités</option>");
	$('#entitedoc').hide();
	
	$('#rapport').hide();
	$('#subButton').hide();
	
	if (entDoc != '') {
		$('#entitedoc option[value='+entDoc+']').prop('selected', true);
		$('#entitedoc').show();
	}
	
});
function rapportChange() {

	
	// Pour les graphiques 9 à 17, on peut sélectionner une entité documentaire :
	switch($('#typerapport').val()) {	
		case '':		
			$('#entitedoc option[value=""]').prop('selected', true);
			$('#entitedoc').hide();
			$('#entitedoc').css('border','2px solid');
			$('#entitedoc').css('border-color','red');	
			$('#rapport').hide();	
			$('#subButton').hide();			
			break;
		default:
			$('#entitedoc option[value=""]').prop('selected', true);
			$('#entitedoc').hide();
			urlTemp = $('#rapport').attr('href').split('?');
			$('#rapport').attr('href',urlTemp[0]+'?mode='+$('#typerapport').val() );
			$('#subButton').show();
	}

}

function entiteChange() {
	if ($('#entitedoc').val() != '') {
		$('#f_rapport').submit();
	}
}
</script> 	
	
