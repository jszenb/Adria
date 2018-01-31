<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Entite Docs Lieu Conservation'), ['action' => 'edit', $entiteDocsLieuConservation->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Entite Docs Lieu Conservation'), ['action' => 'delete', $entiteDocsLieuConservation->id], ['confirm' => __('Are you sure you want to delete # {0}?', $entiteDocsLieuConservation->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Entite Docs Lieu Conservations'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Entite Docs Lieu Conservation'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Entite Docs'), ['controller' => 'EntiteDocs', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Entite Doc'), ['controller' => 'EntiteDocs', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Lieu Conservations'), ['controller' => 'LieuConservations', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Lieu Conservation'), ['controller' => 'LieuConservations', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="entiteDocsLieuConservations view large-9 medium-8 columns content">
    <h3><?= h($entiteDocsLieuConservation->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Entite Doc') ?></th>
            <td><?= $entiteDocsLieuConservation->has('entite_doc') ? $this->Html->link($entiteDocsLieuConservation->entite_doc->code, ['controller' => 'EntiteDocs', 'action' => 'view', $entiteDocsLieuConservation->entite_doc->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Lieu Conservation') ?></th>
            <td><?= $entiteDocsLieuConservation->has('lieu_conservation') ? $this->Html->link($entiteDocsLieuConservation->lieu_conservation->id, ['controller' => 'LieuConservations', 'action' => 'view', $entiteDocsLieuConservation->lieu_conservation->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($entiteDocsLieuConservation->id) ?></td>
        </tr>
    </table>
</div>
