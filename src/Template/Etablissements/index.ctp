<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
</nav>
<div class="etablissements index large-9 medium-8 columns content">
    <h3><?= __('Etablissements') ?></h3>
	<p> <?php 
			$sous_libelle = '<b>Total : ' . $this->Paginator->counter('{{count}}') . ' établissements</b>' ;
			echo $sous_libelle;
		?> 
	</p>	
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th width=40%><?= $this->Paginator->sort('nom') ?></th>
                <th width=10%><?= $this->Paginator->sort('code') ?></th>
                <th width=15%><?= $this->Paginator->sort('adresse_1', 'Adresse') ?></th>
                <th width=7%><?= $this->Paginator->sort('adresse_cp', 'Code postal') ?></th>
                <th width=12%><?= $this->Paginator->sort('adresse_ville', 'Ville') ?></th>				
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>

			<?php 
			foreach ($etablissements as $etablissement): 
			?>
            <tr>
                <td><?= h($etablissement->nom) ?></td>
                <td><?= h($etablissement->code) ?></td>
                <td><?= h($etablissement->adresse_1) ?></td>
                <td><?= h($etablissement->adresse_cp) ?></td>
                <td><?= h($etablissement->adresse_ville) ?></td>				
                <td class="actions">
                    <?= $this->Html->link(__('Consulter'), ['action' => 'view', $etablissement->id]) ?>
                    
					<?php if ($typeUserEnSession == PROFIL_CC) { ?>
					
						<?= $this->Html->link(__('Modifier'), ['action' => 'edit', $etablissement->id]) ?>
					
					<?php } ?>
					
					<?php if ($typeUserEnSession == PROFIL_CC) { 
						
						// Si au moins une entité documentaire est liée à cet établissement, on ne peut pas le modifier : 
						if (empty($etablissement->entite_docs)) {?>
						
						<?= $this->Form->postLink(__('Supprimer'), ['action' => 'delete', $etablissement->id], ['confirm' => __('Voulez-vous vraiment supprimer l\'établissement {0}?', $etablissement->nom)]) ?>
					
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
