<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); 
	
	// L'utilisateur CC peut effacer un user 
	if ($typeUserEnSession == PROFIL_CC) { ?>
	<ul class="side-nav">
		 <li><?= $this->Form->postLink(__('Supprimer l\'utilisateur'), ['action' => 'delete', $user->id], ['confirm' => __('Voulez-vous vraiment supprimer l\'utilisateur {0} ?', $user->nom.' '.$user->prenom)]) ?> </li>		
	</ul>
	<?php } ?>
</nav>

<div class="users view large-9 medium-8 columns content">
    <h3><?= 'Utilisateur : '.h($user->nom).' '.h($user->prenom) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Nom') ?></th>
            <td><?= h($user->nom) ?></td>
        </tr>
        <tr>
            <th><?= __('Prénom') ?></th>
            <td><?= h($user->prenom) ?></td>
        </tr>
        <tr>
            <th><?= __('Entité documentaire') ?></th>
            <td><?= $user->has('entite_doc') ? $this->Html->link($user->entite_doc->code, ['controller' => 'EntiteDocs', 'action' => 'view', $user->entite_doc->id]) : '' ?></td>
        </tr>		
        <tr>
            <th><?= __('Tél.') ?></th>
            <td><?= h($user->num_tel) ?></td>
        </tr>		
        <tr>
            <th><?= __('Courriel') ?></th>
            <td><?= h($user->mail) ?></td>
        </tr>

        <tr>
            <th><?= __('Type') ?></th>
            <td><?= h($user->type_user->description) ?></td>
        </tr>
        <!-- <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($user->id) ?></td>
        </tr> -->
    </table>
	<?php 
		// Celui qui consulte peut modifier uniquement s'il est Campus Condorcet ou s'il est Chargé d'archives pour l'entité documentaire dont dépend le fonds
		if ( ($typeUserEnSession == PROFIL_CC) || ( ( ( $typeUserEnSession == PROFIL_CA ) || ( $typeUserEnSession == PROFIL_CO )) && ($idUserEnSession == $user['id']) ) ) { ?>
		<div align="right">
			<?= $this->Html->link(__('Modifier'), ['action' => 'edit', $user->id]) ?>
		<br><br>
		</div>
	<?php } ?>	
</div>
