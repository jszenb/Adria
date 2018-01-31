<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $entiteDocsLieuConservation->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $entiteDocsLieuConservation->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Entite Docs Lieu Conservations'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Entite Docs'), ['controller' => 'EntiteDocs', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Entite Doc'), ['controller' => 'EntiteDocs', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Lieu Conservations'), ['controller' => 'LieuConservations', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Lieu Conservation'), ['controller' => 'LieuConservations', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="entiteDocsLieuConservations form large-9 medium-8 columns content">
    <?= $this->Form->create($entiteDocsLieuConservation) ?>
    <fieldset>
        <legend><?= __('Edit Entite Docs Lieu Conservation') ?></legend>
        <?php
            echo $this->Form->input('entite_doc_id', ['options' => $entiteDocs]);
            echo $this->Form->input('lieu_conservation_id', ['options' => $lieuConservations]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
