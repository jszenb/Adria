<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
	<ul class="side-nav">
		<li><?= $this->Html->link(__('Ajouter une raison de suppression'), ['action' => 'add']) ?></li>
    </ul>
</nav>
<div class="raisonSuppressions index large-9 medium-8 columns content">
    <h3><?= __('Raison de suppression') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <!-- <th><?= $this->Paginator->sort('id') ?></th> -->
                <th><?= $this->Paginator->sort('raison') ?></th>
				<th><?= $this->Paginator->sort('description') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($raisonSuppressions as $raisonSuppression): ?>
            <tr>
                <!-- <td><?= $this->Number->format($raisonSuppression->id) ?></td> -->
                <td><?= h($raisonSuppression->raison) ?></td>
				<td><?= h($raisonSuppression->description) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('Consulter'), ['action' => 'view', $raisonSuppression->id]) ?>
                    <?= $this->Html->link(__('Modifier'), ['action' => 'edit', $raisonSuppression->id]) ?>
                    <?= $this->Form->postLink(__('Supprimer'), ['action' => 'delete', $raisonSuppression->id], ['confirm' => __('Voulez-vous vraiment supprimer cette raison de suppression {0} ?', $raisonSuppression->raison)]) ?>
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
