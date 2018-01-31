<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
</nav>
<!-- 
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Users'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Entite Docs'), ['controller' => 'EntiteDocs', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Entite Doc'), ['controller' => 'EntiteDocs', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Type Users'), ['controller' => 'TypeUsers', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Type User'), ['controller' => 'TypeUsers', 'action' => 'add']) ?></li>
    </ul>
</nav>
-->
<div class="users form large-9 medium-8 columns content">
    <?= $this->Form->create($user) ?>
    <fieldset>
        <legend><?= __('Créer un utilisateur') ?></legend>
        <?php
            echo $this->Form->input('login');
            echo $this->Form->input('password', ['label' => 'Mot de passe']);
            echo $this->Form->input('nom');
            echo $this->Form->input('prenom', ['label' => 'Prénom']);
            echo $this->Form->input('num_tel', ['label' => 'Tél.']);			
            echo $this->Form->input('mail', ['label' => 'Courriel']);
            echo $this->Form->input('entite_doc_id', ['options' => $entiteDocs, 'empty' => true, 'label' => 'Entité documentaire']);
            echo $this->Form->input('type_user_id', ['options' => $typeUsers, 'empty' => true, 'label' => 'Type d\'utilisateur']);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Enregistrer')) ?>
    <?= $this->Form->end() ?>
</div>
