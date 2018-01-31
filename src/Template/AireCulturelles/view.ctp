<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
	<ul class="side-nav">
		<li><?= $this->Html->link(__('Modifier cette aire culturelle'), ['action' => 'edit', $aireCulturelle->id]) ?></li>
    </ul>
</nav>
<div class="aireCulturelles view large-9 medium-8 columns content">
    <h3>Aire culturelle : <?= h($aireCulturelle->intitule) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Intitulé') ?></th>
            <td><?= h($aireCulturelle->intitule) ?></td>
        </tr>
        <tr>
            <th><?= __('Couleur') ?></th>
            <td><?= h($aireCulturelle->couleur) ?></td>
        </tr>		
        <!-- <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($aireCulturelle->id) ?></td>
        </tr> -->
    </table>
    <div class="row">
        <h4><?= __('Description') ?></h4>
        <?= $this->Text->autoParagraph(h($aireCulturelle->description)); ?>
    </div>
	<h4><?= __('Fonds concerné(s)') ?></h4>
    <div class="related">
        <?php if (!empty($aireCulturelle->fonds)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Nom') ?></th>
                <th><?= __('Type de fonds') ?></th>	
                <th><?= __('Vol. ml') ?></th>	
				<th><?= __('Vol. Go') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($aireCulturelle->fonds as $fonds): ?>
            <tr>
                <td><?= h($fonds->nom) ?></td>

				<?php 
					$libelleTypeFonds = "";
					
					foreach ($typeFonds as $unTypeFonds) {
						
						if ($unTypeFonds['id'] == $fonds['type_fond_id']){
							$libelleTypeFonds = $unTypeFonds['type'];
							break;
						}
					}
				?>
                <td><?= h($libelleTypeFonds) ?></td>
				<td><?= h($fonds->nb_ml) ?></td>
                <td><?= h($fonds->nb_go) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('Consulter'), ['controller' => 'Fonds', 'action' => 'view', $fonds->id]) ?>
                    <?= $this->Html->link(__('Modifier'), ['controller' => 'Fonds', 'action' => 'edit', $fonds->id]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    </div>
</div>
