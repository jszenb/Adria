<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
	<ul class="side-nav">
		<li><?= $this->Html->link(__('Ajouter un type de statut juridique'), ['action' => 'add']) ?></li>
    </ul>
</nav>
<div class="typeStatJurids index large-9 medium-8 columns content">
    <h3><?= __('Type de statut juridique') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <!-- <th><?= $this->Paginator->sort('id') ?></th> -->
                <th><?= $this->Paginator->sort('type') ?></th>
				<th><?= $this->Paginator->sort('description') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($typeStatJurids as $typeStatJurid): ?>
            <tr>
                <!-- <td><?= $this->Number->format($typeStatJurid->id) ?></td> -->
                <td><?= h($typeStatJurid->type) ?></td>
				<td><?= h($typeStatJurid->description) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('Consulter'), ['action' => 'view', $typeStatJurid->id]) ?>
                    <?= $this->Html->link(__('Modifier'), ['action' => 'edit', $typeStatJurid->id]) ?>
                    <?= $this->Form->postLink(__('Supprimer'), ['action' => 'delete', $typeStatJurid->id], ['confirm' => __('Voulez-vous vraiment supprimer le type de fonds {0} ?', $typeStatJurid->type)]) ?>
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
