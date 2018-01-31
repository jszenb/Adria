<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
	<ul class="side-nav">
		<li><?= $this->Html->link(__('Liste des types de fonds'), ['action' => 'index']) ?></li>
    </ul>
</nav>

<div class="typeFonds form large-9 medium-8 columns content">
    <?= $this->Form->create($typeFond) ?>
    <fieldset>
        <legend><?= __('Modifier un type de fonds') ?></legend>
        <?php
            echo $this->Form->input('type');
            echo $this->Form->input('num_seq', ['label' => 'Numéro de séquence']);					
            echo $this->Form->input('description');
			echo $this->Form->input('couleur');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
