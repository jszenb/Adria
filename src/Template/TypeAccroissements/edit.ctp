<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
	<ul class="side-nav">
		<li><?= $this->Html->link(__('Liste des types d\'accroissement'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="typeAccroissements form large-9 medium-8 columns content">
    <?= $this->Form->create($typeAccroissement) ?>
    <fieldset>
        <legend><?= __('Modifier un type d\'accroissement') ?></legend>
        <?php
            echo $this->Form->input('type');
            echo $this->Form->input('description');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
