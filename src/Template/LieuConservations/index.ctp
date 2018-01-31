<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
</nav>
<div class="lieuConservations index large-9 medium-8 columns content">
    <h3><?= __('Lieux de conservation') ?></h3>
	<p> <?php 
			$sous_libelle = '<b>Total : ' . $this->Paginator->counter('{{count}}') . ' lieux de conservation</b>' ;
			echo $sous_libelle;
		?> 
	</p>	
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th width=40%><?= $this->Paginator->sort('nom') ?></th>
                <th width=20%><?= $this->Paginator->sort('adresse_1', 'Adresse') ?></th>
                <th width=10%><?= $this->Paginator->sort('adresse_cp', 'Code postal') ?></th>
                <th width=15%><?= $this->Paginator->sort('adresse_ville', 'Ville') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lieuConservations as $lieuConservation): ?>
            <tr>
                <td><?= h($lieuConservation->nom) ?></td>
                <td><?= h($lieuConservation->adresse_1) ?></td>
                <td><?= h($lieuConservation->adresse_cp) ?></td>
                <td><?= h($lieuConservation->adresse_ville) ?></td>
                <td class="actions">
					<?php 
					// Le lieu de conservation est-il lié à l'entité documentaire de l'utilisateur ? Celle-ci est en session.
					$droitModification = false;
					foreach ($lieuConservation->entite_docs as $entiteDocs){
						
						if ($entiteDocs['id'] == $idEntiteDocEnSession) {
							$droitModification = true;
							break;
						}
					}
					?>
					
                    <?= $this->Html->link(__('Consulter'), ['action' => 'view', $lieuConservation->id]) ?>
                    
					<?php if ( ($typeUserEnSession == PROFIL_CC) || ( ($typeUserEnSession == PROFIL_CA) && ($droitModification) ) ) { ?>
					
						<?= $this->Html->link(__('Modifier'), ['action' => 'edit', $lieuConservation->id, '?' => ['droitModification' => $droitModification, 'typeUser' => $typeUserEnSession, 'entiteDoc' => $idEntiteDocEnSession]]) ?>
					
					<?php } ?>
					
					<?php if ($typeUserEnSession == PROFIL_CC) { ?>
						<?php if (empty($lieuConservation->fonds)) { ?>
							<?= $this->Form->postLink(__('Supprimer'), ['action' => 'delete', $lieuConservation->id], ['confirm' => __('Voulez-vous vraiment supprimer le lieu de conservation {0}?', $lieuConservation->nom)]) ?>
						<?php } 
					} ?>	
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
	<?php echo $this->element('navigationIndex'); ?>
	<!--
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->prev('< ' . __('précédent')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('suivant') . ' >') ?>
        </ul>
        <p><?= $this->Paginator->counter() ?></p>
    </div>
	-->
</div>
