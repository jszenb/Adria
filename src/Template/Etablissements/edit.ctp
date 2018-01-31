<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); 
	
	// L'utilisateur CC peut effacer un établissement 
	if ($typeUserEnSession == PROFIL_CC) { ?>
	<ul class="side-nav">
		
		<li><?= $this->Form->postLink(__('Supprimer l\'établissement'), ['action' => 'delete', $etablissement->id], ['confirm' => __('Voulez-vous vraiment supprimer l\'établissement {0} ?', $etablissement->code)]) ?> </li>		
	</ul>
	<?php } ?>
</nav>
<!-- 
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $etablissement->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $etablissement->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Etablissements'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Entite Docs'), ['controller' => 'EntiteDocs', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Entite Doc'), ['controller' => 'EntiteDocs', 'action' => 'add']) ?></li>
    </ul>
</nav>
-->
<div class="etablissements form large-9 medium-8 columns content">
    <?= $this->Form->create($etablissement) ?>
    <fieldset>
        <legend><?= __('Modifier l\'établissement') ?></legend>
        <?php
            echo $this->Form->input('nom');
            echo $this->Form->input('code');
            echo $this->Form->input('adresse_1');
            echo $this->Form->input('adresse_2');
            echo $this->Form->input('adresse_3');
            echo $this->Form->input('adresse_cp', ['label' => 'Code postal']);
            echo $this->Form->input('adresse_ville', ['label' => 'Ville']);
            echo $this->Form->input('adresse_pays', ['label' => 'Pays']);
            echo $this->Form->input('num_tel', ['label' => 'Tél.']);
            echo $this->Form->input('mail', ['label' => 'Courriel']);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Enregistrer')) ?>
    <?= $this->Form->end() ?>
</div>
