<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<?php
$cakeDescription = 'Fiche fonds';
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
		Campus Condorcet - Cartographie dynamique des archives
    </title>
    <?= $this->Html->meta('icon') ?>

    
    <?= $this->fetch('meta') ?>
    
    <?= $this->fetch('script') ?>
	
	<!-- style propre à cette impression -->
	<style>
	h4 {
		text-decoration: underline;
	}
	
	table{
		border: none;
		padding:5px 5px;		
	}
	
	th {

		font-size: 10;
		font-weight: bold;
		text-align: left;
		border: none;
	}

	td {
		text-align: left;	  
	}
	</style>
</head>
<body>

    <h3><?= h($title)?> : <?= h($fond->nom) ?></h3>
<!-- ******************************************* IDENTIFICATION DU FONDS ******************************************* -->
	<h4>Identification du fonds</h4>
    <table width="100%">
        <tr>
			<!-- Les dimensions width sont appliquées par défaut sur les autres lignes du tableau -->
            <th width="25%"><?= __('Nom :') ?></th>
            <td width="75%"><?= h($fond->nom) ?></td>
        </tr>
		<tr>
			<th><?= __('Dates extrêmes :') ?></th>
			<td><?php if (!$fond->ind_annee) { ?><?= h($fond->annee_deb) ?> -  <?= h($fond->annee_fin) ?><?php } else { ?> <?= h('A renseigner') ?> <?php } ?>	</td>
        </tr>		
        <tr>
            <th><?= __('Cote :') ?></th>
            <td><?= h($fond->cote) ?></td>
        </tr>
        <tr>
            <th><?= __('Entité documentaire : ') ?></th>
            <td><?= h($fond->entite_doc->nom) ?> (<?= h($fond->entite_doc->code) ?>)</td>
        </tr>		
		<tr>
			<th colspan=2 width="100%"><?= __('Lieu(x) de conservation : ') ?></th>
		</tr>	
		<tr>
			<td colspan=2 width="100%">
				<?php if (!empty($fond->lieu_conservations)): ?>
				<table cellpadding="0" cellspacing="0" >

					<?php foreach ($fond->lieu_conservations as $lieuConservations): ?>
					<tr>
						<td width="40%"><?= h($lieuConservations->nom) ?></td>
						<td width="25%"><?= h($lieuConservations->adresse_1) ?></td>
						<td width="10%"><?= h($lieuConservations->adresse_cp) ?></td>
						<td width="25%"><?= h($lieuConservations->adresse_ville) ?></td>
					</tr>
					<?php endforeach; ?>
				</table>
				<?php endif; ?>
			</td>
		</tr>	
	</table>
	
<!-- ******************************************* CONTEXTE ******************************************* -->
	<br>
	<h4>Contexte</h4>	
    <table width="100%">	
        <tr>
            <th width="25%"><?= __('Producteur : ') ?></th>
            <td width="75%"><?= h($fond->producteur) ?></td>
        </tr>
        <tr>
			<th colspan=2 align=left><?= __('Historique : ') ?></th>
		</tr>
		<?php if (!empty($fond->historique)): ?>
		<tr>
			<td colspan=2>
				<?= $this->Text->autoParagraph(h($fond->historique)); ?>
			</td>
		</tr>
		<?php endif; ?>
        <tr>
            <th><?= __('Statut juridique : ') ?></th>
            <td><?= $fond->has('type_stat_jurid') ? h($fond->type_stat_jurid->type) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Mode d\'entrée : ') ?></th>
            <td><?= $fond->has('type_entree') ? h($fond->type_entree->type) : '' ?></td>
        </tr>	
		<tr>
			<th colspan=2 width="100%"><?= __('Document(s) afférent(s) au mode d\'entrée : ') ?></th>
		</tr>	
		<tr>
			<td colspan=2 width="100%">	
				<?php if (!empty($fond->type_doc_afferents)): ?>
				<table cellpadding="0" cellspacing="0">
					<?php foreach ($fond->type_doc_afferents as $typeDocAfferents): ?>
					<tr>
						<td width="100%"><?= h($typeDocAfferents->type) ?></td>
					</tr>
					<?php endforeach; ?>
				</table>
				<?php endif; ?>
			</td>
		</tr>	
	</table>
	
<!-- ******************************************* CONTENU ET VOLUMETRIE ******************************************* -->
	
	<br>
	<h4>Contenu et volumétrie</h4>		
	<table width="100%">
        <tr>
            <th width="40%"><?= __('Type de fonds : ') ?></th>
            <td width="60%"><?= $fond->has('type_fond') ? h($fond->type_fond->type) : '' ?></td>			
        </tr>	
        <tr>
            <th><?= __('URL de l\'inventaire de la collection') ?></th>
            <td><?= $fond->has('url_collection') ? h($fond->url_collection) : '' ?></td>	
        </tr>			
        <tr>
            <th><?= __('Fonds couplé à une collection d\'imprimés ? ') ?></th>
            <td><?= $fond->ind_bib ? __('Oui') : __('Non'); ?></td>
        </tr>
		<?php if (!empty($fond->precision_bib)): ?>		
		<tr>
			<th colspan=2 width="100%"><?= __('Précisions sur cette collection : ') ?></th>
		</tr>

		<tr>
			<td colspan=2 width="100%">
				 <div align=left> 
				<?= $this->Text->autoParagraph(h($fond->precision_bib)); ?>
				 </div> 
			</td>
		</tr>
		<?php endif; ?>
		<tr>
			<th colspan=2 width="100%"><?= __('Type(s) de documents du fonds : ') ?></th>
		</tr>	
		<tr>
			<td colspan=2 width="100%">	
				<?php if (!empty($fond->type_docs)): ?>
				<table cellpadding="0" cellspacing="0">
					<?php  foreach ($fond->type_docs as $typeDocs): ?>
					<tr>
						<td width="100%"><?= h($typeDocs->type) ?></td>
					</tr>
					<?php endforeach; ?>
				</table>
				<?php endif; ?>
			</td>			
		</tr>	
		<tr>
			<th colspan=2 width="100%"><?= __('Type(s) de supports : ') ?></th>
		</tr>			
		<tr>
			<td colspan=2 width="100%">	
				<?php if (!empty($fond->type_supports)): ?>
				<table cellpadding="0" cellspacing="0">
					<?php  foreach ($fond->type_supports as $typeSupports): ?>
					<tr>
						<td width="100%"><?= h($typeSupports->type) ?></td>
					</tr>
					<?php endforeach; ?>
				</table>
				<?php endif; ?>
			</td>			
		</tr>			
		<tr>
            <th width="40%"><?= __('Volumétrie physique en mètres linéaires : ') ?></th>
            <td width="60%"><?php if (!$fond->ind_nb_ml_inconnu) { ?><?= $this->Number->format($fond->nb_ml) ?> <?php } else { ?> <?= h('inconnue') ?> <?php } ?></td>
        </tr>
        <tr>
            <th width="40%"><?= __('Volumétrie numérique en giga-octets : ') ?></th>
            <td width="60%"><?php if (!$fond->ind_nb_go_inconnu) { ?><?= $this->Number->format($fond->nb_go) ?> <?php } else { ?> <?= h('inconnue') ?> <?php } ?></td>			
        </tr>
        <tr>
            <th><?= __('Accroissement : ') ?></th>
            <td><?= $fond->has('type_accroissement') ? h($fond->type_accroissement->type) : '' ?></td>
        </tr>	
		<tr>
			<th colspan=2 width="100%"><?= __('Discipline(s) : ') ?></th>
		</tr>	
		<tr>
			<td colspan=2 width="100%">
				<?php if (!empty($fond->thematiques)): ?>
				<table cellpadding="0" cellspacing="0">
					<?php foreach ($fond->thematiques as $thematiques): ?>
					<tr>
						<td width="100%"><?= h($thematiques->intitule) ?></td>
					 </tr>
					<?php endforeach; ?>
				</table>
				<?php endif; ?> 
			</td>
		</tr>
		<tr>
			<th colspan=2 width="100%"><?= __('Aire(s) culturelle(s) : ') ?></th>
		</tr>	
		<tr>
			<td colspan=2 width="100%">
				<?php if (!empty($fond->aire_culturelles)): ?>
				<table cellpadding="0" cellspacing="0">
					<?php foreach ($fond->aire_culturelles as $aireCulturelles): ?>
					<tr>
						<td width="100%"><?= h($aireCulturelles->intitule) ?></td>
					</tr>
					<?php endforeach; ?>
				</table>
				<?php endif; ?>
			</td>
		</tr>	

	</table>

<!-- ******************************************* TRAITEMENT MATÉRIEL ET INTELLECTUEL ******************************************* -->
	
	<br>
	<h4>Traitement matériel et intellectuel</h4>
	<table width="100%">
		<tr>
			<th colspan=2><?= __('Conditionnement(s) : ') ?></th>
		</tr>
		<tr>
			<td colspan=2 width="100%">
				<?php if (!empty($fond->type_conditionnements)): ?>
				<table cellpadding="0" cellspacing="0">
					<?php foreach ($fond->type_conditionnements as $typeConditionnements): ?>
					<tr>
						<td width="100%"><?= h($typeConditionnements->type) ?></td>
					</tr>
					<?php endforeach; ?>
				</table>
				<?php endif; ?>
			</td>
		</tr>		
        <tr>
            <th width="25%"><?= __('Traitement : ') ?></th>
            <td width="75%"><?= $fond->has('type_traitement') ? h($fond->type_traitement->type) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Instrument de recherche : ') ?></th>
            <td><?= $fond->has('type_instr_rech') ? h($fond->type_instr_rech->type) : '' ?></td>
        </tr>		
        <tr>
            <th><?= __('URL de l\'instrument de recherche') ?></th>
            <td><?= $fond->has('url_instr_rech') ? h($fond->url_instr_rech) : '' ?></td>	
        </tr>		
        <tr>
            <th><?= __('Numérisation : ') ?></th>
            <td><?= $fond->has('type_numerisation') ? h($fond->type_numerisation->type) : '' ?></td>
        </tr>	
	</table>

<!-- ******************************************* OBSERVATIONS ******************************************* -->
	
	<br>	
	<?php if (!empty($fond->observations)) { ?>
	<h4><?= __('Observations') ?></h4>
	<?= $this->Text->autoParagraph(h($fond->observations)); ?>
	<?php } ?>

	
<!-- ******************************************* DONNEES POUR L'ADMINISTRATEUR ******************************************* -->
	

<?php if ($typeUserEnSession == PROFIL_CC) { ?>	
	<br>
	<h4><?= __('Informations complémentaires pour l\'administrateur') ?></h4>
	<table width="100%">	
	    <tr>
            <th width="25%"><?= __('Identifiant du fonds en base') ?></th>
            <td width="75%"><?= $this->Number->format($fond->id) ?></td>
        </tr>	
        <tr>
            <th><?= __('Date de création') ?></th>			
            <td><?= $fond->has('dt_creation') ? h($fond->dt_creation->nice('Europe/Paris', 'fr-FR')) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Date de dernière modification') ?></th>
            <td><?= $fond->has('dt_der_modif') ? h($fond->dt_der_modif->nice('Europe/Paris', 'fr-FR')) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Le fonds est-il considéré comme supprimé ?') ?></th>
            <td><?= $fond->ind_suppr ? __('Oui') : __('Non'); ?></td>
         </tr>		
        <tr>
            <th><?= __('Raison de la suppression') ?></th>
            <td><?= $fond->has('raison_suppression') ? $this->Html->link($fond->raison_suppression->raison, ['controller' => 'RaisonSuppressions', 'action' => 'view', $fond->raison_suppression->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Date de la suppression') ?></th>
            <td><?= $fond->has('dt_suppr') ? h($fond->dt_suppr->nice('Europe/Paris', 'fr-FR')) : '' ?></td>
        </tr>
    </table>		
<?php } ?>
</body>
</html>
