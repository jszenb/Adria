<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
	<ul class="side-nav">
		<li><?= $this->Html->link(__('Liste des raisons de suppression'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="raisonSuppressions form large-9 medium-8 columns content">
    <?= $this->Form->create($raisonSuppression) ?>
    <fieldset>
        <legend><?= __('Modifier une raison de suppression') ?></legend>
        <?php
            echo $this->Form->input('raison');
            echo $this->Form->input('description');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
