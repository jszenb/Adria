<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); 
	
	// L'utilisateur CC peut effacer un établissement 
	if ($typeUserEnSession == PROFIL_CC) { ?>
	<ul class="side-nav">
		<li><?= $this->Form->postLink(__('Supprimer l\'entité documentaire'), ['action' => 'delete', $entiteDoc->id], ['confirm' => __('Voulez-vous vraiment supprimer l\'entité documentaire {0} ?', $entiteDoc->nom)]) ?> </li>		
	</ul>
	<?php } ?>
</nav>
<!-- 
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $entiteDoc->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $entiteDoc->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Entite Docs'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Etablissements'), ['controller' => 'Etablissements', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Etablissement'), ['controller' => 'Etablissements', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Fonds'), ['controller' => 'Fonds', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Fond'), ['controller' => 'Fonds', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Lieu Conservations'), ['controller' => 'LieuConservations', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Lieu Conservation'), ['controller' => 'LieuConservations', 'action' => 'add']) ?></li>
    </ul>
</nav>
-->
<div class="entiteDocs form large-9 medium-8 columns content">
    <?= $this->Form->create($entiteDoc) ?>
    <fieldset>
        <legend><?= __('Modifier l\'entité documentaire') ?></legend>
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
            echo $this->Form->input('etablissement_id', ['options' => $etablissements]);
			if ($typeUserEnSession == PROFIL_CC) { 
				echo $this->Form->input('lieu_conservations._ids', ['options' => $lieuConservations, 'label' => 'Lieu(x) de conservation']);
			}
			else {
				echo $this->Form->input('lieu_conservations._ids', ['options' => $lieuConservations, 'required' => true, 'label' => 'Lieu(x) de conservation']);
			}
			

        ?>
    </fieldset>
    <?= $this->Form->button(__('Enregistrer')) ?>
    <?= $this->Form->end() ?>
</div>
