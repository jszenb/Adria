<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); 
	
	// L'utilisateur CC peut effacer un établissement 
	if ($typeUserEnSession == PROFIL_CC) { ?>
	<ul class="side-nav">
		<li><?= $this->Html->link(__('Modifier l\'établissement'), ['action' => 'edit', $etablissement->id]) ?></li>
		<li><?= $this->Form->postLink(__('Supprimer l\'établissement'), ['action' => 'delete', $etablissement->id], ['confirm' => __('Voulez-vous vraiment supprimer l\'établissement {0} ?', $etablissement->code)]) ?> </li>		
	</ul>
	<?php } ?>
</nav>


<div class="etablissements view large-9 medium-8 columns content">
    <h3>Etablissement : <?= h($etablissement->nom)  ?></h3>
	<table class="vertical-table">

        <tr>
            <th><?= __('Code') ?></th>
            <td><?= h($etablissement->code) ?></td>
        </tr>
        <tr>
            <th><?= __('Adresse 1') ?></th>
            <td><?= h($etablissement->adresse_1) ?></td>
        </tr>
		<?php if (!empty($etablissement->adresse_2)) { ?>
        <tr>
            <th><?= __('Adresse 2') ?></th>
            <td><?= h($etablissement->adresse_2) ?></td>
        </tr>
		<?php } ?>
		<?php if (!empty($etablissement->adresse_3)) { ?>		
        <tr>
            <th><?= __('Adresse 3') ?></th>
            <td><?= h($etablissement->adresse_3) ?></td>
        </tr>
		<?php } ?>		
        <tr>
            <th><?= __('Code postal') ?></th>
            <td><?= h($etablissement->adresse_cp) ?></td>
        </tr>
        <tr>
            <th><?= __('Ville') ?></th>
            <td><?= h($etablissement->adresse_ville) ?></td>
        </tr>
        <!-- <tr>
            <th><?= __('Adresse Pays') ?></th>
            <td><?= h($etablissement->adresse_pays) ?></td>
        </tr> -->
        <tr>
            <th><?= __('Tél.') ?></th>
            <td><?= h($etablissement->num_tel) ?></td>
        </tr>
        <tr>
            <th><?= __('Courriel') ?></th>
            <td><?= h($etablissement->mail) ?></td>
        </tr>
        <!-- <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($etablissement->id) ?></td>
        </tr> -->
    </table>
    <!-- <div class="related"> -->
        <h4><?= __('Entité(s) documentaire(s) de l\'établissement') ?></h4>
        <?php if (!empty($etablissement->entite_docs)) {?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Nom') ?></th>
                <th><?= __('Code') ?></th>
                <th><?= __('Adresse 1') ?></th>
                <th><?= __('Code postal') ?></th>
                <th><?= __('Ville') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($etablissement->entite_docs as $entiteDocs): ?>
            <tr>
                <td><?= h($entiteDocs->nom) ?></td>
                <td><?= h($entiteDocs->code) ?></td>
                <td><?= h($entiteDocs->adresse_1) ?></td>
                <td><?= h($entiteDocs->adresse_cp) ?></td>
                <td><?= h($entiteDocs->adresse_ville) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('Consulter'), ['controller' => 'EntiteDocs', 'action' => 'view', $entiteDocs->id]) ?>

					<?php if ( ($typeUserEnSession == PROFIL_CC) || ( ($typeUserEnSession == PROFIL_CA) && ($idEntiteDocEnSession == $entiteDocs->id) ) ){ ?>
						<?= $this->Html->link(__('Modifier'), ['controller' => 'EntiteDocs', 'action' => 'edit', $entiteDocs->id]) ?>
					<?php } ?>

					<?php /* if ($typeUserEnSession == PROFIL_CC){ ?>
						<?= $this->Form->postLink(__('Supprimer'), ['controller' => 'EntiteDocs', 'action' => 'delete', $entiteDocs->id], ['confirm' => __('Voulez-vous vraiment supprimer l\'entité documentaire {0} ?', $entiteDocs->code)]) ?>
					<?php } */ ?>

                </td>
            </tr>
            <?php endforeach; ?>
        </table>
		<?php 
		}
		else {?>
			<p>Aucune entité documentaire déclarée pour cet établissement.</p>
		<?php } ?>
	<?php if ($typeUserEnSession == PROFIL_CC) { ?>
	<div align="right">
		<?= $this->Html->link(__('Modifier'), ['action' => 'edit', $etablissement->id]) ?>
		<br><br>
	</div>
	<?php } ?>
</div>
