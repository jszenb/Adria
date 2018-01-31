<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
	<ul class="side-nav">
		<li><?= $this->Html->link(__('Liste des aires culturelles'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="aireCulturelles form large-9 medium-8 columns content">
    <?= $this->Form->create($aireCulturelle) ?>
    <fieldset>
        <legend><?= __('Modifier une aire culturelle') ?></legend>
        <?php
            echo $this->Form->input('intitule', ['label' => 'Intitulé']);
			echo $this->Form->input('couleur', ['label' => 'Couleur']);						
            echo $this->Form->input('description');
            //echo $this->Form->input('fonds._ids', ['options' => $fonds]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
