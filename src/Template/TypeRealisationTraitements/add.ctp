<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
	<ul class="side-nav">
		<li><?= $this->Html->link(__('Liste des types de réalisation de traitement'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="typeRealisationTraitements form large-9 medium-8 columns content">
    <?= $this->Form->create($typeRealisationTraitement) ?>
    <fieldset>
        <legend><?= __('Ajouter un type de réalisation de traitement') ?></legend>
        <?php
            echo $this->Form->input('type');
            echo $this->Form->input('description');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
