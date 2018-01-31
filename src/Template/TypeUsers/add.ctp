<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
	<ul class="side-nav">
		<li><?= $this->Html->link(__('Liste des types d\'utilisateur'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="typeUsers form large-9 medium-8 columns content">
    <?= $this->Form->create($typeUser) ?>
    <fieldset>
        <legend><?= __('Ajouter un type d\'utilisateur') ?></legend>
        <?php
            echo $this->Form->input('type');
            echo $this->Form->input('description');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Enregistrer')) ?>
    <?= $this->Form->end() ?>
</div>
