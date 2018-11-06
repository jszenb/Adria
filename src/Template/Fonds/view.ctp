<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
	
</nav>
<div class="fonds view large-9 medium-8 columns content">
    <h3><?= h($fond->nom) ?><?= $fond->ind_maj ? '&nbsp;&#x2714;' : '' ?></h3>
<div class="right">
	<?= $this->Html->link(__('Télécharger'), ['action' => 'generatepdf', 
	                                          '?' => ['mode' => 'fiche', 'id' => $fond->id ]
											 ], 
											 ['title'=>'PDF généré','onclick'=>'javascript:window.open(this.href,\'_blank\',\'toolbar=0,scrollbars=0,location=0,status=0,menubar=0,resizable=0,width=400,height=100\');return false;']) ?>
</div>	
<!-- ******************************************* IDENTIFICATION DU FONDS ******************************************* -->
	<h4>Identification du fonds</h4>
    <table class="vertical-table">
        <tr>
            <th><?= __('Nom') ?></th>
            <td><?= h($fond->nom) ?></td>
        </tr>
		<tr>
			<th><?= __('Dates extrêmes') ?></th>
			<td><?php if (!$fond->ind_annee) { ?><?= h($fond->annee_deb) ?> -  <?= h($fond->annee_fin) ?><?php } else { ?> <?= h('A renseigner') ?> <?php } ?>	</td> 
        </tr>
		<tr>
            <th><?= __('Cote') ?></th>
            <td><?= h($fond->cote) ?></td>
        </tr>
        <tr>
            <th><?= __('Entité documentaire') ?></th>
            <td><?= $fond->has('entite_doc') ? $this->Html->link($fond->entite_doc->nom.' ('.$fond->entite_doc->code.')', ['controller' => 'EntiteDocs', 'action' => 'view', $fond->entite_doc->id]) : '' ?></td>
        </tr>		
		<tr>
			<th colspan=2><?= __('Lieu(x) de conservation') ?></th>
		</tr>	
		<tr class="imbriquee">
			<td colspan=2>
				<?php if (!empty($fond->lieu_conservations)): ?>
				<table cellpadding="0" cellspacing="0" >

					<?php foreach ($fond->lieu_conservations as $lieuConservations): ?>
					<tr class="imbriquee">
						<td><?= h($lieuConservations->nom) ?></td>
						<td><?= h($lieuConservations->adresse_1) ?></td>
						<td><?= h($lieuConservations->adresse_cp) ?></td>
						<td><?= h($lieuConservations->adresse_ville) ?></td>
						<td class="actions">
							<?= $this->Html->link(__('Consulter'), ['controller' => 'LieuConservations', 'action' => 'view', $lieuConservations->id]) ?>
						</td>
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
    <table class="vertical-table">	
        <tr>
            <th><?= __('Producteur') ?></th>
            <td><?= h($fond->producteur) ?></td>
        </tr>
        <tr>
			<th colspan=2 align=left><?= __('Historique') ?></th>
		</tr>
		<?php if (!empty($fond->historique)): ?>
		<tr>
			<td colspan=2>
				<div align=left>
				<?= $this->Text->autoParagraph(h($fond->historique)); ?>
				</div>
			</td>
		</tr>
		<?php endif; ?>
        <tr>
            <th><?= __('Statut juridique') ?></th>
            <td><?= $fond->has('type_stat_jurid') ? h($fond->type_stat_jurid->type) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Mode d\'entrée') ?></th>
            <td><?= $fond->has('type_entree') ? h($fond->type_entree->type) : '' ?></td>
        </tr>	
		<tr>
			<th colspan=2><?= __('Document(s) afférent(s) au mode d\'entrée') ?></th>
		</tr>	
		<tr class="imbriquee">
			<td colspan=2>	
				<?php if (!empty($fond->type_doc_afferents)): ?>
				<table cellpadding="0" cellspacing="0">
					<?php foreach ($fond->type_doc_afferents as $typeDocAfferents): ?>
					<tr class="imbriquee">
						<td><?= h($typeDocAfferents->type) ?></td>
						<!-- <td><?= h($typeDocAfferents->description) ?></td> --> 
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
	<table class="vertical-table">
        <tr>
            <th><?= __('Type de fonds') ?></th>
            <td><?= $fond->has('type_fond') ? h($fond->type_fond->type) : '' ?></td>			
        </tr>		
        <tr>
            <th><?= __('Fonds couplé à une collection d\'imprimés ?') ?></th>
            <td><?= $fond->ind_bib ? __('Oui') : __('Non'); ?></td>
        </tr>
        <tr>
            <th><?= __('URL d\'inventaire de la collection') ?></th>
            <td><?= $fond->has('url_collection') ? $this->Html->link($fond->url_collection, $fond->url_collection, ['target' => '_blank' ]) : '' ?></td>	
        </tr>		
		<tr>
			<th colspan=2><?= __('Précisions sur cette collection') ?></th>
		</tr>
		<?php if (!empty($fond->precision_bib)): ?>
		<tr>
			<td colspan=2>
				 <div align=left> 
				<?= $this->Text->autoParagraph(h($fond->precision_bib)); ?>
				 </div> 
			</td>
		</tr>
		<?php endif; ?>
		<tr>
			<th colspan=2><?= __('Type(s) de documents du fonds') ?></th>
		</tr>	
		<tr class="imbriquee">
			<td colspan=2>	
				<?php if (!empty($fond->type_docs)): ?>
				<table cellpadding="0" cellspacing="0">
					<?php  foreach ($fond->type_docs as $typeDocs): ?>
					<tr class="imbriquee">
						<td><?= h($typeDocs->type) ?></td>
					</tr>
					<?php endforeach; ?>
				</table>
				<?php endif; ?>
			</td>			
		</tr>	
		<tr>
			<th colspan=2><?= __('Type(s) de supports') ?></th>
		</tr>	
		<tr class="imbriquee">
			<td colspan=2>	
				<?php if (!empty($fond->type_supports)): ?>
				<table cellpadding="0" cellspacing="0">
					<?php  foreach ($fond->type_supports as $typeSupports): ?>
					<tr class="imbriquee">
						<td><?= h($typeSupports->type) ?></td>
					</tr>
					<?php endforeach; ?>
				</table>
				<?php endif; ?>
			</td>			
		</tr>		
		<tr>
            <!-- <th><?= __('Document(s) sur support(s) physique(s) ?') ?></th>
            <td><?= $fond->ind_nb_ml ? __('Oui') : __('Non'); ?></td> -->
            <th><?= __('Volumétrie physique en mètres linéaires') ?></th>
            <td>
				<?php if (!$fond->ind_nb_ml_inconnu) { ?><?= $this->Number->format($fond->nb_ml) ?> <?php } else { ?> <?= h('inconnue') ?> <?php } ?>
			</td>
        </tr>
        <tr>
            <!-- <th><?= __('Document(s) sur support(s) numérique(s) ?') ?></th>
            <td><?= $fond->ind_nb_go ? __('Oui') : __('Non'); ?></td> -->
            <th><?= __('Volumétrie numérique en giga-octets') ?></th>
            <td>
				<?php if (!$fond->ind_nb_go_inconnu) { ?><?= $this->Number->format($fond->nb_go) ?> <?php } else { ?> <?= h('inconnue') ?> <?php } ?>
			</td>			
        </tr>
        <tr>
            <th><?= __('Accroissement') ?></th>
            <td><?= $fond->has('type_accroissement') ? h($fond->type_accroissement->type) : '' ?></td>
        </tr>	
		<tr>
			<th colspan=2><?= __('Discipline(s)') ?></th>
		</tr>	
		<tr class="imbriquee">
			<td colspan=2>
				<?php if (!empty($fond->thematiques)): ?>
				<table cellpadding="0" cellspacing="0">
					<?php foreach ($fond->thematiques as $thematiques): ?>
					<tr class="imbriquee">
						<td><?= h($thematiques->intitule) ?></td>
					 </tr>
					<?php endforeach; ?>
				</table>
				<?php endif; ?> 
			</td>
		</tr>
		<tr>
			<th colspan=2><?= __('Aire(s) culturelle(s)') ?></th>
		</tr>	
		<tr class="imbriquee">
			<td colspan=2>
				<?php if (!empty($fond->aire_culturelles)): ?>
				<table cellpadding="0" cellspacing="0">
					<?php foreach ($fond->aire_culturelles as $aireCulturelles): ?>
					<tr class="imbriquee">
						<td><?= h($aireCulturelles->intitule) ?></td>
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
	<table class="vertical-table">
		<tr>
			<th colspan=2><?= __('Conditionnement(s)') ?></th>
		</tr>
		<tr class="imbriquee">
			<td colspan=2>
				<?php if (!empty($fond->type_conditionnements)): ?>
				<table cellpadding="0" cellspacing="0">
					<?php foreach ($fond->type_conditionnements as $typeConditionnements): ?>
					<tr class="imbriquee">
						<td><?= h($typeConditionnements->type) ?></td>
					</tr>
					<?php endforeach; ?>
				</table>
				<?php endif; ?>
			</td>
		</tr>		
        <tr>
            <th><?= __('Traitement') ?></th>
            <td><?= $fond->has('type_traitement') ? h($fond->type_traitement->type) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Instrument de recherche') ?></th>
            <td><?= $fond->has('type_instr_rech') ? h($fond->type_instr_rech->type) : '' ?></td>
        </tr>	
        <tr>
            <th><?= __('URL de l\'instrument de recherche') ?></th>
            <td><?= $fond->has('url_instr_rech') ? $this->Html->link($fond->url_instr_rech, $fond->url_instr_rech, ['target' => '_blank' ]) : '' ?></td>		
        </tr>			
        <tr>
            <th><?= __('Numérisation') ?></th>
            <td><?= $fond->has('type_numerisation') ? h($fond->type_numerisation->type) : '' ?></td>
        </tr>	
	</table>

<!-- ******************************************* OBSERVATIONS ******************************************* -->
	
	<br>	
	<?php if (!empty($fond->observations)) { ?>
	<h4><?= __('Observations') ?></h4>
	<?= $this->Text->autoParagraph(h($fond->observations)); ?>
	<?php } ?>

<!-- ******************************************* ORIENTATIONS ******************************************* -->

	<br>	
	<h4><?= __('Orientation du fonds') ?></h4>
	<table class="vertical-table">
	<tr>
		<th><?= __('Lieu de stockage cible') ?></th>
		<td><?= $fond->has('stockage') ? LIB_STOCKAGE_CIBLE[h($fond->stockage)] : '' ?></td>
	</tr>
	<tr>
		<th><?= __('Le fonds est-il communicable ?') ?></th>
		<td><?= $fond->has('stockage') ? LIB_COMMUNICATION[h($fond->communication)] : '' ?></td>
	</tr>
	</table>

<!-- ******************************************* MARCHE DE TRAITEMENT ******************************************* -->
        <?php if ( ($typeUserEnSession != PROFIL_CO) || ($typeUserEnSession == PROFIL_CO && $fond->type_prise_en_charge->id != NON_PRISE_EN_CHARGE) ) {?>
	<br>	
	<h4><?= __('Marché de traitement') ?></h4>
	<table class="vertical-table">
        <tr>
            <th><?= __('Prise en charge du fonds') ?></th>
            <td><?= $fond->has('type_prise_en_charge') ? h($fond->type_prise_en_charge->type) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Prestation') ?></th>
            <td><?= $fond->has('type_realisation_traitement') ? h($fond->type_realisation_traitement->type) : '' ?></td>
        </tr>	
		<tr>
            <th><?= __('Site d\'intervention') ?></th>
            <td><?= $fond->has('site_intervention') ? h($fond->site_intervention) : '' ?></td>			
		</tr>
		<tr>
            <th><?= __('Dates envisagées / effectives') ?></th>
            <td><?= $fond->has('dt_deb_prestation') ? h($fond->dt_deb_prestation->nice('Europe/Paris', 'fr-FR')) : '' ?> - <?= $fond->has('dt_fin_prestation') ? h($fond->dt_fin_prestation->nice('Europe/Paris', 'fr-FR')) : '' ?></td>			
		</tr>
		<tr>
            <th><?= __('Responsable d\'opérations') ?></th>
            <td><?= $fond->has('responsable_operation') ? h($fond->responsable_operation) : '' ?></td>			
		</tr>
	</table>
	<?php } ?>
	
<!-- ******************************************* DONNEES POUR L'ADMINISTRATEUR ******************************************* -->
	

<?php if ($typeUserEnSession == PROFIL_CC) { ?>	
	<br>
	<h4>Implantation en magasin</h4>
	<table>
	<?php foreach ($fond->adresses as $adresse): 
		if (!empty($adresse['volume'])) { ?>
		<tr>
			<td colspan=5>
				<b>Adresse n°<?php echo $adresse['num_seq'] + 1 ?></b>
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
				<?= h($adresse['volume']); ?>
			</td>
			<td>
				<?= h($adresse['magasin']); ?>
			</td>
			<td>
				<?= h($adresse['epi_deb']); ?>
				<?php if (!empty($adresse['epi_fin'])) { ?>
					à <?= h($adresse['epi_fin']) ; ?>
				<?php }  ?>
			</td>
			<td>
				<?= h($adresse['travee_deb']); ?>
				<?php if (!empty($adresse['travee_fin'])) { ?>
					à <?= h($adresse['travee_fin']) ; ?>
				<?php }  ?>
			</td>			
			<td>
				<?= h($adresse['tablette_deb']); ?>
				<?php if (!empty($adresse['tablette_fin'])) { ?>
					à <?= h($adresse['tablette_fin']) ; ?>
				<?php }  ?>
			</td>
		</tr>	
		<?php }
		endforeach; ?>
	</table>
	
	<br>
	<h4><?= __('Informations complémentaires pour l\'administrateur') ?></h4>
	<table class="vertical-table">		
	    <tr>
            <th><?= __('Identifiant du fonds en base') ?></th>
            <td><?= $this->Number->format($fond->id) ?></td>
        </tr>	
        <tr>
            <th><?= __('Date de création sur l\'application') ?></th>			
            <td><?= $fond->has('dt_creation') ? h($fond->dt_creation->nice('Europe/Paris', 'fr-FR')) : '' ?></tr>
        </tr>
        <tr>
            <th><?= __('Date de dernière modification') ?></th>
            <td><?= $fond->has('dt_der_modif') ? h($fond->dt_der_modif->nice('Europe/Paris', 'fr-FR')) : '' ?></tr>
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
            <td><?= $fond->has('dt_suppr') ? h($fond->dt_suppr->nice('Europe/Paris', 'fr-FR')) : '' ?></tr>
        </tr>
    </table>		
<?php } ?>
	<?php 
		// Celui qui consulte peut modifier uniquement s'il est Campus Condorcet ou s'il est Chargé d'archives pour l'entité documentaire dont dépend le fonds
		// La modification n'est pas possible pour un fonds déclaré comme supprimé : il faudra le réactiver avant.
		if (!$fond->ind_suppr) {
			if ( ($typeUserEnSession == PROFIL_CC) || ( ($typeUserEnSession == PROFIL_CA) && ($idEntiteDocEnSession == $fond->entite_doc_id) ) ){ ?>
			<div align="right">
				<?= $this->Html->link(__('Modifier'), ['action' => 'edit', $fond->id]) ?>
				<br><br>
			</div>
			<?php 
			} 
		} ?>
</div>
