<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
	<ul class="side-nav">
		<li><?= $this->Html->link(__('Liste des types de documents'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="typeDocs form large-9 medium-8 columns content">
    <?= $this->Form->create($typeDoc) ?>
    <fieldset>
        <legend><?= __('Ajouter un type de documents') ?></legend>
        <?php
            echo $this->Form->input('type');
            echo $this->Form->input('description');
            echo $this->Form->input('couleur');			
		?>
		<!--
		<h6>Forme(s) des documents</h6>
		<?php /*
            echo $this->Form->input('ind_ecrit', ['label' =>'Document écrit']);
            echo $this->Form->input('ind_graphique', ['label' =>'Document graphique']);
            echo $this->Form->input('ind_audio', ['label' =>'Document audio']);
            echo $this->Form->input('ind_video', ['label' =>'Document vidéo']);
            echo $this->Form->input('ind_audiovisuel', ['label' =>'Document audovisuel']);
            echo $this->Form->input('ind_objet', ['label' =>'Objet']); */
		?>
		<br>
		<h6>Support(s) des documents</h6>
		<?php /*
            echo $this->Form->input('ind_physique', ['label' =>'Support physique']);
            echo $this->Form->input('ind_numerique', ['label' =>'Support numérique']); */
		?>
		
		<br>
		<?php
            //echo $this->Form->input('fonds._ids', ['options' => $fonds]);
        ?>
    -->
	</fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
