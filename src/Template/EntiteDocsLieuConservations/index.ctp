<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Entite Docs Lieu Conservation'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Entite Docs'), ['controller' => 'EntiteDocs', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Entite Doc'), ['controller' => 'EntiteDocs', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Lieu Conservations'), ['controller' => 'LieuConservations', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Lieu Conservation'), ['controller' => 'LieuConservations', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="entiteDocsLieuConservations index large-9 medium-8 columns content">
    <h3><?= __('Entite Docs Lieu Conservations') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('id') ?></th>
                <th><?= $this->Paginator->sort('entite_doc_id') ?></th>
                <th><?= $this->Paginator->sort('lieu_conservation_id') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($entiteDocsLieuConservations as $entiteDocsLieuConservation): ?>
            <tr>
                <td><?= $this->Number->format($entiteDocsLieuConservation->id) ?></td>
                <td><?= $entiteDocsLieuConservation->has('entite_doc') ? $this->Html->link($entiteDocsLieuConservation->entite_doc->code, ['controller' => 'EntiteDocs', 'action' => 'view', $entiteDocsLieuConservation->entite_doc->id]) : '' ?></td>
                <td><?= $entiteDocsLieuConservation->has('lieu_conservation') ? $this->Html->link($entiteDocsLieuConservation->lieu_conservation->id, ['controller' => 'LieuConservations', 'action' => 'view', $entiteDocsLieuConservation->lieu_conservation->id]) : '' ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $entiteDocsLieuConservation->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $entiteDocsLieuConservation->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $entiteDocsLieuConservation->id], ['confirm' => __('Are you sure you want to delete # {0}?', $entiteDocsLieuConservation->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
        </ul>
        <p><?= $this->Paginator->counter() ?></p>
    </div>
</div>
