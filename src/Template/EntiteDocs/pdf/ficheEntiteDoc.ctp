<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<?php
$cakeDescription = 'Fiche entité documentaire';
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
		Campus Condorcet - Cartographie dynamique des archives
    </title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->fetch('meta') ?>
    
    <?= $this->fetch('script') ?>
	
	<!-- style propre à cette impression -->
	<style>
	h4 {
		text-decoration: underline;
	}
	
	table{
		border: none;
		padding:5px 5px;		
	}
	
	th {

		font-size: 10;
		font-weight: bold;
		text-align: left;
		border: none;
	}

	td {
		text-align: left;	  
	}
	</style>
		
</head>
<body>
    <h3>Entité documentaire : <?= h($entiteDoc->nom) ?> (<?= h($entiteDoc->code) ?>)</h3>
    <table class="vertical-table">
        <tr>
            <th width="25%"><?= __('Code :') ?></th>
            <td width="75%"><?= h($entiteDoc->code) ?></td>
        </tr>
        <tr>
            <th><?= __('Etablissement :') ?></th>
            <td><?= h($entiteDoc->etablissement->code) ?> (<?= h($entiteDoc->etablissement->nom) ?>)</td>
        </tr>
        <tr>
            <th><?= __('Adresse 1 :') ?></th>
            <td><?= h($entiteDoc->adresse_1) ?></td>
        </tr>
		<?php if (!empty($entiteDoc->adresse_2)) { ?>
        <tr>
            <th><?= __('Adresse 2 :') ?></th>
            <td><?= h($entiteDoc->adresse_2) ?></td>
        </tr>
		<?php } ?>
		<?php if (!empty($entiteDoc->adresse_3)) { ?>		
        <tr>
            <th><?= __('Adresse 3 :') ?></th>
            <td><?= h($entiteDoc->adresse_3) ?></td>
        </tr>
		<?php } ?>		
        <tr>
            <th><?= __('Code postal :') ?></th>
            <td><?= h($entiteDoc->adresse_cp) ?></td>
        </tr>
        <tr>
            <th><?= __('Ville :') ?></th>
            <td><?= h($entiteDoc->adresse_ville) ?></td>
        </tr>
        <!-- <tr>
            <th><?= __('Adresse Pays :') ?></th>
            <td><?= h($entiteDoc->adresse_pays) ?></td>
        </tr> -->
        <tr>
            <th><?= __('Tél.') ?></th>
            <td><?= h($entiteDoc->num_tel) ?></td>
        </tr>
        <tr>
            <th><?= __('Courriel :') ?></th>
            <td><?= h($entiteDoc->mail) ?></td>
        </tr>
    </table>
	<h4><?= __('Référent(e) archives') ?></h4>
    <div class="related">

        <?php if (!empty($entiteDoc->users)) { ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th width="25%"><?= __('Nom') ?></th>
                <th width="25%"><?= __('Prénom') ?></th>
                <th width="20%"><?= __('Tél.') ?></th>				
                <th width="30%"><?= __('Courriel') ?></th>
            </tr>
            <?php foreach ($entiteDoc->users as $users): ?>
            <tr>
                <td><?= h($users->nom) ?></td>
                <td><?= h($users->prenom) ?></td>
				<td><?= h($users->num_tel) ?></td>
                <td><?= h($users->mail) ?></td>

            </tr>
            <?php endforeach; ?>
        </table>
		<?php 
		}
		else {?>
			<p>Aucun(e) référent(e) archives déclaré(e) pour cette entité documentaire.</p>
		<?php } ?>
    </div>	
	<br>	
	<h4><?= __('Fonds') ?></h4>
    <div class="related">
        <?php if (!empty($entiteDoc->fonds)) { ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th width="45%"><?= __('Nom') ?></th>
                <th width="35%"><?= __('Type de fonds') ?></th>				
                <th width="10%"><?= __('Vol. ml') ?></th>
                <th width="10%"><?= __('Vol. Go') ?></th>
            </tr>
            <?php 
			$totalMl = 0.0;
			$totalGo = 0.0;
			foreach ($entiteDoc->fonds as $fonds): 
			
				// Calcul des sommes de volumétrie qu'on affichera après la boucle.
				$totalMl = $totalMl + (float)$fonds->nb_ml;
				$totalGo = $totalGo + (float)$fonds->nb_go;
			?>
            <tr>
                <td><?= h($fonds->nom) ?></td>
                <td>
					<?= h($fonds->type_fond->type) ?>
				</td>
				<td><?= h($fonds->nb_ml) ?></td>
                <td><?= h($fonds->nb_go) ?></td>

            </tr>
			<tr><td colspan=4> </td></tr>
            <?php endforeach; ?>
			<tr>
				<td>&nbsp;</td>
				<td class="right"><i>Total : </i></td>
				<td><?php echo($totalMl); ?></td>
				<td><?php echo($totalGo); ?></td>
			</tr>
        </table>
		<?php 
		}
		else {?>
			<p>Aucun fonds déclaré pour cette entité documentaire.</p>
		<?php } ?>
    </div>
	<h4><?= __('Lieu(x) de conservation') ?></h4>
    <div class="related">

        <?php if (!empty($entiteDoc->lieu_conservations)){ ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th width="35%"><?= __('Nom') ?></th>
                <th width="25%"><?= __('Adresse') ?></th>
                <th width="20%"><?= __('Code postal') ?></th>
                <th width="20%"><?= __('Ville') ?></th>
            </tr>
            <?php foreach ($entiteDoc->lieu_conservations as $lieuConservations): ?>
            <tr>
                <td><?= h($lieuConservations->nom) ?></td>
                <td><?= h($lieuConservations->adresse_1) ?></td>
                <td><?= h($lieuConservations->adresse_cp) ?></td>
                <td><?= h($lieuConservations->adresse_ville) ?></td>
            </tr>
			<tr><td colspan=4> </td></tr>
            <?php endforeach; ?>
        </table>
		<?php 		
		}
		else {?>
			<p>Aucun lieu de conservation déclaré pour cette entité documentaire.</p>
		<?php } ?>		
    </div>
	<?php if ($nb_TypeFonds != 0) { ?>
	<br pagebreak="true" />
	<img src="<?php echo ($temp_img_stat_typeFonds); ?>" />
	<?php }
	if ($nb_Thematiques != 0) { ?>
	<br pagebreak="true" />
	<img src="<?php echo ($temp_img_stat_thematiques); ?>" />
	<?php }
	if ($nb_Aires != 0) { ?>	
	<br pagebreak="true" />
	<img src="<?php echo ($temp_img_stat_aires); ?>" />	
	<?php }
	if ($nb_Dates != 0) { ?>	
	<br pagebreak="true" />
	<img src="<?php echo ($temp_img_stat_dates); ?>" />	
	<?php } ?>
	
    </table>		
</body>
</html>
