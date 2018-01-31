
<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
</nav>

<div class="users index large-9 medium-8 columns content">
    <h3><?= __('Utilisateurs') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('nom') ?></th>
                <th><?= $this->Paginator->sort('prenom', 'Prénom') ?></th>
                <th><?= $this->Paginator->sort('EntiteDocs.code', 'Entité documentaire') ?></th>				
                <th class="courriel"><?= $this->Paginator->sort('mail', 'Courriel') ?></th>
				<th><?= $this->Paginator->sort('TypeUsers.type', 'Type') ?></th>				
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php 	
			foreach ($users as $user): 
				// L'utilisateur CC peut voir tous les profils, mais les autres utilisateurs ne peuvent pas le voir
				if ( ($typeUserEnSession == PROFIL_CC) || ( $typeUserEnSession != PROFIL_CC ) && ( $user->type_user->id != PROFIL_CC ) )  {
			?>
            <tr>
                <td><?= h($user->nom) ?></td>
                <td><?= h($user->prenom) ?></td>
                <td><?= $user->has('entite_doc') ? $this->Html->link($user->entite_doc->code, ['controller' => 'EntiteDocs', 'action' => 'view', $user->entite_doc->id]) : '' ?></td>
                <td class="courriel"><?= h($user->mail) ?></td>				
				<td><?= h($user->type_user->description )?></td>				
                <td class="actions">
				
                    <?= $this->Html->link(__('Consulter'), ['action' => 'view', $user->id]) ?>
					
					<?php if ( ($typeUserEnSession == PROFIL_CC) || ( ( ( $typeUserEnSession == PROFIL_CA ) || ( $typeUserEnSession == PROFIL_CO ) ) && $idUserEnSession == $user['id'] ) ){ ?>
					
						<?= $this->Html->link(__('Modifier'), ['action' => 'edit', $user->id]) ?>
					
					<?php } ?>
					
					<?php if ($typeUserEnSession == PROFIL_CC) { ?>
						<?php if ($user->type_user->id != PROFIL_CC) { ?>
							<?= $this->Form->postLink(__('Supprimer'), ['action' => 'delete', $user->id], ['confirm' => __('Voulez-vous vraiment supprimer l\'utilisateur {0} ?', $user->id)]) ?>
						<?php }
					} ?>
                </td>
            </tr>
            <?php }
			endforeach; ?>
        </tbody>
    </table>
	<?php echo $this->element('navigationIndex'); ?>
	<!--
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->prev('< ' . __('précédent')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('suivant') . ' >') ?>
        </ul>
        <p><?= $this->Paginator->counter() ?></p>
    </div>
	-->
</div>
