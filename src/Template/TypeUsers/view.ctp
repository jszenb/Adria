<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
	<ul class="side-nav">
		<li><?= $this->Html->link(__('Modifier ce type d\'utilisateur'), ['action' => 'edit', $typeUser->id]) ?></li>
    </ul>
</nav>
<div class="typeUsers view large-9 medium-8 columns content">
		<h3>Type d'utilisateurs : <?= h($typeUser->type) ?></h3>
		<table class="vertical-table">
			<tr>
				<th><?= __('Type') ?></th>
				<td><?= h($typeUser->type) ?></td>
			</tr>
		</table>
		<div class="row">
			<h4><?= __('Description') ?></h4>
			<?= $this->Text->autoParagraph(h($typeUser->description)); ?>
		</div>
        <h4><?= __('Utilisateur(s) concerné(s)') ?></h4>
        <?php if (!empty($typeUser->users)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Nom') ?></th>
                <th><?= __('Prenom') ?></th>			
                <th><?= __('Login') ?></th>
                <th><?= __('Mail') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($typeUser->users as $users): ?>
            <tr>
                <td><?= h($users->nom) ?></td>
                <td><?= h($users->prenom) ?></td>			
                <td><?= h($users->login) ?></td>
                <td class="courriel"><?= h($users->mail) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('Consulter'), ['controller' => 'Users', 'action' => 'view', $users->id]) ?>

                    <?= $this->Html->link(__('Modifier'), ['controller' => 'Users', 'action' => 'edit', $users->id]) ?>


                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>
