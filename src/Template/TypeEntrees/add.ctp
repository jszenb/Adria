<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
	<ul class="side-nav">
		<li><?= $this->Html->link(__('Liste des types de mode d\'entrée'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="typeEntrees form large-9 medium-8 columns content">
    <?= $this->Form->create($typeEntree) ?>
    <fieldset>
        <legend><?= __('Ajouter un type de mode d\'entrée') ?></legend>
        <?php
            echo $this->Form->input('type');
            echo $this->Form->input('description');
            echo $this->Form->input('couleur');			
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
