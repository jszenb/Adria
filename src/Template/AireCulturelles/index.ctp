<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
	<ul class="side-nav">
		<li><?= $this->Html->link(__('Ajouter une aire culturelle'), ['action' => 'add']) ?></li>
    </ul>
</nav>
<div class="aireCulturelles index large-9 medium-8 columns content">
    <h3><?= __('Aire culturelle') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <!-- <th><?= $this->Paginator->sort('id') ?></th> -->
                <th><?= $this->Paginator->sort('intitule', 'Intitulé') ?></th>
				<th><?= $this->Paginator->sort('description') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($aireCulturelles as $aireCulturelle): ?>
            <tr>
                <!-- <td><?= $this->Number->format($aireCulturelle->id) ?></td> -->
                <td><?= h($aireCulturelle->intitule) ?></td>
				<td><?= h($aireCulturelle->description) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('Consulter'), ['action' => 'view', $aireCulturelle->id]) ?>
                    <?= $this->Html->link(__('Modifier'), ['action' => 'edit', $aireCulturelle->id]) ?>
                    <?= $this->Form->postLink(__('Supprimer'), ['action' => 'delete', $aireCulturelle->id], ['confirm' => __('Voulez-vous vraiment supprimer cette aire culturelle {0} ?', $aireCulturelle->intitule)]) ?>
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
