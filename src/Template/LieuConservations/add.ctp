<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
</nav>
<!-- 
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Lieu Conservations'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Entite Docs'), ['controller' => 'EntiteDocs', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Entite Doc'), ['controller' => 'EntiteDocs', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Fonds'), ['controller' => 'Fonds', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Fond'), ['controller' => 'Fonds', 'action' => 'add']) ?></li>
    </ul>
</nav>
-->
 

<div class="lieuConservations form large-9 medium-8 columns content">
    <?= $this->Form->create($lieuConservation) ?>
    <fieldset>
	    <legend><?= __('Ajouter un lieu de conservation') ?></legend>
        <?php
            echo $this->Form->input('nom');
            echo $this->Form->input('adresse_1');
            echo $this->Form->input('adresse_2');
            echo $this->Form->input('adresse_3');
            echo $this->Form->input('adresse_cp', ['label' => 'Code postal']);
            echo $this->Form->input('adresse_ville', ['label' => 'Ville']);
            echo $this->Form->input('adresse_pays', ['label' => 'Pays']);
            echo $this->Form->input('entite_docs._ids', ['options' => $entiteDocs, 'label' => 'Entité documentaire', 'required' => true]);
            echo $this->Form->input('fonds._ids', ['options' => $fonds]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Enregistrer')) ?>
    <?= $this->Form->end() ?>
</div>
