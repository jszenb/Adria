<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); 
	
	// L'utilisateur CC peut effacer un utilisateur 
	if ($typeUserEnSession == PROFIL_CC) { ?>
	<ul class="side-nav">
		
		<li><?= $this->Form->postLink(__('Supprimer l\'utilisateur'), ['action' => 'delete', $user->id], ['confirm' => __('Voulez-vous vraiment supprimer l\'utilisateur {0} ?', $user->nom.' '.$user->prenom)]) ?> </li>		
	</ul>
	<?php } ?>
</nav>

<div class="users form large-9 medium-8 columns content">
    <?= $this->Form->create($user) ?>
    <fieldset>
        <legend><?= __("Modifier l'utilisateur")?></legend>
        <?php
			$typeUserEnSession = $_SESSION['Auth']['User']['type_user_id'];
			
            if ($typeUserEnSession == PROFIL_CA) {
				echo $this->Form->input('login', ['disabled' => 'disabled']);
			}
			else {
				echo $this->Form->input('login');
			}
            echo $this->Form->input('password', ['label' => 'Mot de passe']);
            echo $this->Form->input('nom');
            echo $this->Form->input('prenom', ['label' => 'Prénom']);
            echo $this->Form->input('num_tel', ['label' => 'Tél.']);			
            echo $this->Form->input('mail', ['label' => 'Courriel']);
			if ($typeUserEnSession == PROFIL_CA) {
				echo $this->Form->input('entite_doc_id', ['options' => $entiteDocs, 'empty' => true, 'disabled' => 'disabled', 'label' => 'Entité documentaire']);
				echo $this->Form->input('type_user_id', ['options' => $typeUsers,  'disabled' => 'disabled', 'label' => 'Type d\'utilisateur']);
			}
			else {
				echo $this->Form->input('entite_doc_id', ['options' => $entiteDocs, 'empty' => true, 'label' => 'Entité documentaire']);
				echo $this->Form->input('type_user_id', ['options' => $typeUsers, 'label' => 'Type d\'utilisateur']);
			}
        ?>
    </fieldset>
    <?= $this->Form->button(__('Enregistrer')) ?>
    <?= $this->Form->end() ?>
</div>
