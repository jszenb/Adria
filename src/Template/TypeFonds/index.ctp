<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
	<ul class="side-nav">
		<li><?= $this->Html->link(__('Ajouter un type de fonds'), ['action' => 'add']) ?></li>
    </ul>
</nav>
<div class="typeFonds index large-9 medium-8 columns content">
    <h3><?= __('Type de fonds') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('type') ?></th>
                <th><?= $this->Paginator->sort('num_seq', 'Séquence') ?></th>
				<th><?= $this->Paginator->sort('description') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($typeFonds as $typeFond): ?>
            <tr>
                <td><?= h($typeFond->type) ?></td>
                <td><?= $this->Number->format($typeFond->num_seq) ?></td>
				<td><?= h($typeFond->description) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('Consulter'), ['action' => 'view', $typeFond->id]) ?>
                    <?= $this->Html->link(__('Modifier'), ['action' => 'edit', $typeFond->id]) ?>
                    <?= $this->Form->postLink(__('Supprimer'), ['action' => 'delete', $typeFond->id], ['confirm' => __('Voulez-vous vraiment supprimer le type de fonds {0} ?', $typeFond->type)]) ?> 
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
