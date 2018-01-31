<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
	<ul class="side-nav">
		<li><?= $this->Html->link(__('Ajouter un type de documents'), ['action' => 'add']) ?></li>
    </ul>
</nav>
<div class="typeDocs index large-9 medium-8 columns content">
    <h3><?= __('Type de documents') ?></h3>
    <table cellpadding="0" cellspacing="0" >
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('type') ?></th>
                <th><?= $this->Paginator->sort('description') ?></th>				
				<!-- <th><?= h('Support') ?></th> -->
                <th class="actions" align="right"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($typeDocs as $typeDoc): ?>
            <tr>
                <td><?= h($typeDoc->type) ?></td>
				<td><?= h($typeDoc->description) ?></td>
				<!-- <td>
				<?php  /*
					if ($typeDoc->ind_physique){
						if ($typeDoc->ind_numerique){ 
							echo('Mixte');
						}
						else { 
							echo('Physique');
						}
					}
					else { 
							echo('Numérique');
					} */

				?>
				</td> -->		
                <td class="actions">
                    <?= $this->Html->link(__('Consulter'), ['action' => 'view', $typeDoc->id]) ?>
                    <?= $this->Html->link(__('Modifier'), ['action' => 'edit', $typeDoc->id]) ?>
                    <?= $this->Form->postLink(__('Supprimer'), ['action' => 'delete', $typeDoc->id], ['confirm' => __('Voulez-vous vraiment supprimer le type de document {0} ?', $typeDoc->type)]) ?>
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
