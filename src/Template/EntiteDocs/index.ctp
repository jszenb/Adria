<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
</nav>
<div class="entiteDocs index large-9 medium-8 columns content">


    <h3><?= __('Entités documentaires') ?></h3>
	<p> <?php 
			$sous_libelle = '<b>Total : ' . $this->Paginator->counter('{{count}}') . ' entités documentaires</b>' ;
			echo $sous_libelle;
		?> 
	</p>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th width=30%><?= $this->Paginator->sort('nom') ?></th>
                <th width=10%><?= $this->Paginator->sort('code') ?></th>
				<th width=10%><?= $this->Paginator->sort('Etablissements.code', 'Etablissement') ?></th>
                <th width=15%><?= $this->Paginator->sort('adresse_1', 'Adresse') ?></th>
                <th width=7%><?= $this->Paginator->sort('adresse_cp','Code postal') ?></th>
				<th width=12% ><?= $this->Paginator->sort('adresse_ville','Ville') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>

        <tbody>
            <?php 
			foreach ($entiteDocs as $entiteDoc): 
			?>
            <tr>
                <td width=30%><?= h($entiteDoc->nom) ?></td>
                <td width=10%><?= h($entiteDoc->code) ?></td>
				<td width=10%><?= $entiteDoc->has('etablissement') ? $this->Html->link($entiteDoc->etablissement->code, ['controller' => 'Etablissements', 'action' => 'view', $entiteDoc->etablissement->id]) : '' ?></td>
                <td width=15%><?= h($entiteDoc->adresse_1) ?></td>
                <td width=7%><?= h($entiteDoc->adresse_cp) ?></td>
                <td width=12%><?= h($entiteDoc->adresse_ville) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('Consulter'), ['action' => 'view', $entiteDoc->id]) ?>
                    
					<?php if ( ($typeUserEnSession == PROFIL_CC) || ( ($typeUserEnSession == PROFIL_CA) && ($idEntiteDocEnSession == $entiteDoc->id) ) ){ ?>
						
						<?= $this->Html->link(__('Modifier'), ['action' => 'edit', $entiteDoc->id, '?' => ['entiteDoc' => $idEntiteDocEnSession,'typeUser' => $typeUserEnSession ]]) ?>
					
					<?php } ?>
					
					<?php if ($typeUserEnSession == PROFIL_CC) { 
					
						// Si au moins un fonds ou un lieu de conservation est lié à cet établissement, on ne peut pas le modifier : 
						if (empty($entiteDoc->fonds) && empty($entiteDoc->lieuConservations)) {?>
					
							<?= $this->Form->postLink(__('Supprimer'), ['action' => 'delete', $entiteDoc->id], ['confirm' => __('Voulez-vous vraiment supprimer l\'entité documentaire {0} ? ', $entiteDoc->code)]) ?>
					
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
