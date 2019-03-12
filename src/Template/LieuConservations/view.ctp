<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php 
	$droitModification = false;
    $entiteDocLieuConservation = 0;
	foreach ($lieuConservation->entite_docs as $entiteDocs) {
		// Si l'identité documentaire de l'utilisateur est identique à l'une des identités du lieu, il peut le modifier.
		// Sinon, il ne le peut pas. 
		if ($idEntiteDocEnSession == $entiteDocs->id)	{
			$droitModification = true;
			$entiteDocLieuConservation = $idEntiteDocEnSession;
			break;
		}
	}
	
	//$entiteDocLieuConservation = $lieuConservation->entite_docs[0]->id ;
	echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); 
	
	
	// L'utilisateur CC peut effacer un établissement, et aussi bien l'utilisateur CC que l'utilisateur CA peuvent le modifier
	//if ($typeUserEnSession == PROFIL_CC) 
	if (($typeUserEnSession == PROFIL_CC) || ( ($typeUserEnSession == PROFIL_CA) && ( $idEntiteDocEnSession == $entiteDocLieuConservation) && $droitModification ) ) {
    ?>
	<ul class="side-nav">
		<?php
		if ($typeUserEnSession == PROFIL_CA) { ?>
			<li><?= $this->Html->link(__('Modifier le lieu de conservation'), ['action' => 'edit', $lieuConservation->id, '?' => ['droitModification' => 1, 'entiteDoc' => $entiteDocLieuConservation]]) ?></li>
		<?php } ?>
		<?php 
		if ($typeUserEnSession == PROFIL_CC): ?>
			<li><?= $this->Html->link(__('Modifier le lieu de conservation'), ['action' => 'edit', $lieuConservation->id, '?' => ['droitModification' => 1,]]) ?></li>
			<li><?= $this->Form->postLink(__('Supprimer le lieu de conservation'), ['action' => 'delete', $lieuConservation->id], ['confirm' => __('Voulez-vous vraiment supprimer le lieu de conservation {0} ?', $lieuConservation->nom)]) ?> </li>		
		<?php endif; ?>
	</ul>
	<?php } ?>
</nav>


<div class="lieuConservations view large-9 medium-8 columns content">
    <h3>Lieu de conservation : <?= h($lieuConservation->nom) ?> </h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Adresse 1') ?></th>
            <td><?= h($lieuConservation->adresse_1) ?></td>
        </tr>
		<?php if (!empty($lieuConservation->adresse_2)) { ?>
        <tr>
            <th><?= __('Adresse 2') ?></th>
            <td><?= h($lieuConservation->adresse_2) ?></td>
        </tr>
		<?php } ?>
		<?php if (!empty($lieuConservation->adresse_3)) { ?>		
        <tr>
            <th><?= __('Adresse 3') ?></th>
            <td><?= h($lieuConservation->adresse_3) ?></td>
        </tr>
		<?php } ?>
        <tr>
            <th><?= __('Code postal') ?></th>
            <td><?= h($lieuConservation->adresse_cp) ?></td>
        </tr>
        <tr>
            <th><?= __('Ville') ?></th>
            <td><?= h($lieuConservation->adresse_ville) ?></td>
        </tr>
    </table>
	<h4><?= __('Entité(s) documentaire(s)') ?></h4>	
    <div class="related">

        <?php if (!empty($lieuConservation->entite_docs)) { ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Nom') ?></th>
                <th><?= __('Code') ?></th>
                <th><?= __('Adresse 1') ?></th>
                <th><?= __('Code postal') ?></th>
                <th><?= __('Ville') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($lieuConservation->entite_docs as $entiteDocs): ?>
            <tr>

                <td><?= h($entiteDocs->nom) ?></td>
                <td><?= h($entiteDocs->code) ?></td>
                <td><?= h($entiteDocs->adresse_1) ?></td>

                <td><?= h($entiteDocs->adresse_cp) ?></td>
                <td><?= h($entiteDocs->adresse_ville) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('Consulter'), ['controller' => 'EntiteDocs', 'action' => 'view', $entiteDocs->id]) ?>

					<?php if ( ($typeUserEnSession == PROFIL_CC) || ( ($typeUserEnSession == PROFIL_CA) && ($idEntiteDocEnSession == $entiteDocs->id) ) ){ ?>
						<?= $this->Html->link(__('Modifier'), ['controller' => 'EntiteDocs', 'action' => 'edit', $entiteDocs->id]) ?>
					<?php } ?>

					<?php /* if ($typeUserEnSession == PROFIL_CC){ ?>
						<?= $this->Form->postLink(__('Supprimer'), ['controller' => 'EntiteDocs', 'action' => 'delete', $entiteDocs->id], ['confirm' => __('Voulez-vous vraiment supprimer l\'entité documentaire {0} ?', $entiteDocs->code)]) ?>
					<?php } */ ?>				

                </td>
            </tr>
            <?php endforeach; ?>
        </table>
		<?php 
		}
		else {?>
			<p>Aucune entité documentaire conservée en ce lieu.</p>
		<?php } ?>
    </div>
	<h4><?= __('Fonds') ?></h4>	
    <div class="related">

        <?php if (!empty($lieuConservation->fonds)) { ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Nom') ?></th>
                <th><?= __('Type de fonds') ?></th>				
                <th><?= __('Vol. ml') ?></th>
                <th><?= __('Vol. Go') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php 
			$totalMl = 0.0;
			$totalGo = 0.0;
			foreach ($lieuConservation->fonds as $fonds): 
				// Calcul des sommes de volumétrie qu'on affichera après la boucle.
				$totalMl = $totalMl + (float)$fonds->nb_ml;
				$totalGo = $totalGo + (float)$fonds->nb_go;
			?>
            <tr>
                <?php // empty($fond->dt_der_modif) ? $dateAffichee = $fond->dt_creation->nice('Europe/Paris', 'fr-FR') : $dateAffichee = $fond->dt_der_modif->nice('Europe/Paris', 'fr-FR') ?>
                <?php empty($fonds->dt_der_modif) ? $dateAffichee = $fonds->dt_creation : $dateAffichee = $fonds->dt_der_modif ?>
                <td><?= h($fonds->nom) ?><?= $fonds->ind_maj ? '&nbsp;&#x2714; (' : '' ?><?= $fonds->ind_maj ?  $dateAffichee->nice('Europe/Paris', 'fr-FR') . ')' : '' ?></td>

                <td>
					<?= h($fonds->type_fond->type) 
					/*foreach ($type_fonds as $type_fond): 
						if ($type_fond->id == $fonds->type_fond_id) { ?>
							<?= h($type_fond->type) ?>
							<?php 
							break;
						}
					endforeach; */
					?>
				</td>
				<td><?= h($fonds->nb_ml) ?></td>
                <td><?= h($fonds->nb_go) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('Consulter'), ['controller' => 'Fonds', 'action' => 'view', $fonds->id]) ?>
					<?php 
						// Celui qui consulte peut modifier uniquement s'il est Campus Condorcet ou s'il est Chargé d'archives pour l'entité documentaire dont dépend le fonds
						if ( ($typeUserEnSession == PROFIL_CC) || ( ($typeUserEnSession == PROFIL_CA) && ($idEntiteDocEnSession == $fonds['entite_doc_id']) ) ){ ?>
						<?= $this->Html->link(__('Modifier'), ['controller' => 'Fonds', 'action' => 'edit', $fonds->id]) ?>
					<?php } ?>
                </td>
            </tr>
            <?php endforeach; ?>
			<tr>
				<td>&nbsp;</td>
				<td class="right">Total : </td>
				<td><?php echo($totalMl); ?></td>
				<td><?php echo($totalGo); ?></td>
			</tr>			
        </table>
		<?php 
		}
		else {?>
			<p>Aucun fonds déclaré pour cette entité documentaire.</p>
		<?php } ?>
    </div> 
	<?php 
		// Celui qui consulte peut modifier uniquement s'il est Campus Condorcet ou s'il est Chargé d'archives pour l'entité documentaire dont dépend le fonds
		if ( ($typeUserEnSession == PROFIL_CC) || ( ($typeUserEnSession == PROFIL_CA) && $droitModification )){ ?>
		<div align="right">
		<?= $this->Html->link(__('Modifier'), ['action' => 'edit', $lieuConservation->id, '?' => ['droitModification' => $droitModification, 'typeUser' => $typeUserEnSession, 'entiteDoc' => $idEntiteDocEnSession]]) ?>		
		<br><br>
		</div>	
	<?php } ?>	
</div>
