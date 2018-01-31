<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = 'CakePHP: the rapid development php framework';
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
		Campus Condorcet - Cartographie dynamique des archives
    </title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->Html->css('base.css') ?>
    <?= $this->Html->css('cake.css') ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>


</head>
<body>
<div class="fonds index large-9 medium-8 columns content">
	<?php if ($typeUserEnSession == PROFIL_CA && $mode == "user") {
			foreach ($fonds as $fond) {
				$codeEntite = $fond->entite_doc->code;
				$nomEntite = $fond->entite_doc->nom;
				$title = $title.' '.$nomEntite.' ('.$codeEntite.')';
				break;
			}
	}
	?>
    <h3><?= __($title) ?></h3>
	<?php if ($mode=="user") { 
		$sous_libelle = '<b>Total : ' . $totalFonds . ' fonds représentant ' . str_replace(',','', $totalMlRecherche) . " ml (sur ". str_replace(',','',$totalMl). ") et "
						. str_replace(',','',$totalGoRecherche) . " Go (sur ". str_replace(',','',$totalGo) . ")</b>" ;
	}
	else {
		$sous_libelle = '<b>Total : ' . $totalFonds . ' fonds représentant ' . str_replace(',','', $totalMlRecherche) . " ml et "
						. str_replace(',','',$totalGoRecherche) . " Go </b>" ;		
	}?>	
	<h4><?php echo( $sous_libelle); ?> </h4>
    <table cellpadding="0" cellspacing="10">
        <thead>
            <tr>
                <th width="40%"><?= h('Nom') ?></th>
				<?php if ($mode != "user" ) { ?>
                <th width="25%"><?= h('Etablissement') ?></th>
                <th width="10%">Entité<br>documentaire</th>
				<?php } 
				?>
				<th><?= h('Type de fonds') ?></th>
                <th width="5%"><?= h('Vol. ml') ?></th>
                <th width="5%"><?= h('Vol. Go') ?></th>	
            </tr>
        </thead>
        <tbody>
			<tr>
				<?php if ($mode != "user" ) { ?>
					<td colspan="6" width="100%"><hr></td>
				<?php 
				}
				else { ?>
					<td colspan="4" width="100%"><hr></td>
				<?php
				}
				?>
			</tr>
			
            <?php 
				foreach ($fonds as $fond): 
					if ( ($typeUserEnSession == PROFIL_CC) || ( (in_array($typeUserEnSession, [PROFIL_CO, PROFIL_CA])) && (!$fond->ind_suppr) ) ): ?>
					<tr>
						<td width="40%"><?= h($fond->nom) ?><?php if ($fond->ind_suppr) { ?><?= h('<b>(supprimé)</b>') ?><?php } ?></td>
						<?php if ($mode != "user" ) { ?>
						<td width="25%"><?= h($fond->entite_doc->etablissement->code)?></td>
						<td width="10%"><?= h($fond->entite_doc->code)?></td>
						<?php 
						} 
						?>
						<td><?= h($fond->type_fond->type) ?></td>				
						<td width="5%"><?= h($fond->nb_ml) ?></td> 
						<td width="5%"><?= h($fond->nb_go) ?></td>			
					</tr>
					<tr>
						<?php if ($mode != "user" ) { ?>
							<td colspan="6" width="100%"><hr></td>
						<?php 
						}
						else { ?>
							<td colspan="4" width="100%"><hr></td>
						<?php
						}
						?>
					</tr>
					<?php 
					endif;
			endforeach; ?>
        </tbody>
    </table>	
</body>
</html>
