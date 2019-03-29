<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">

	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
	
</nav>
<div class="fonds index large-9 medium-8 columns content">
    <h3><?= __('Implantation en magasins') ?></h3>
	<p>Cliquez sur le magasin pour masquer ou démasquer son affichage
	
	<!-- ******************************* Implantation dans les magasins ******************************* -->
	<div class="right">
	</div>
	<div class="recherche">
		<table class="recherche">
			<tr class="recherche">
				<?php foreach (LISTE_MAGASINS as $i=>$j) : ?>
				<td><b><a href="javascript:show('<?= $i ?>');"><?= $j ?></a></b></td>
				<?php endforeach; ?>
				<td><b><a href="javascript:showAll();">Tout montrer</a></b></td>
				<td><b><a href="javascript:hideAll();">Tout cacher</a></b></td>
			</tr>
		</table>
	</div>

	<?php 
	$monMagasin = '';
	$monTotal = 0;
       	foreach ($adresses as $adresse): 
		if ($monMagasin != $adresse->magasin){ 

			// Changement de magasin : on clot l'affichage du magasin précédent et on ouvre l'affichage du prochain
			if ($monMagasin != '') {
			?>
				<tr>
					<td colspan=6 />
					<td class=right>Total : <?= $monTotal ?></td>
				</tr>
			</table>
			</div>
				<?php
				}
				
				$monTotal = 0;
			?>
			<div id=<?=  $adresse->magasin ?>>
			<table>
				<tr>
					<th>Magasin <?= h($adresse->magasin) ?></td>
					<th>Epi (début)</th>
					<th>Epi (fin)</th>
					<th>Travée (début)</th>
					<th>Travée (fin)</th>
					<th width="50%">Nom du fonds</th>
					<th class=right>Volumétrie (ml)</th>
				</tr>
				<tr>
					<td>&nbsp;</td>
			<?php 

				$monMagasin = $adresse->magasin;
			} 
			else { ?>
				<tr>
					<td>&nbsp;</td>
			<?php
			} ?>
			
					<td> <?= h($adresse->epi_deb) ?> </td>
					<td> <?= h($adresse->epi_fin) ?> </td>
					<td> <?= h($adresse->travee_deb) ?> </td>
					<td> <?= h($adresse->travee_fin) ?> </td>
					<td> <?= h($adresse->fondnom) ?> (<?= h($adresse->entite) ?>) </td>
					<td class=right> <?= h($adresse->fondml) ?> </td>
				</tr>
			
		<?php 
			$monTotal += $adresse->fondml;
		endforeach; ?>
			<tr>
				<td colspan=6 />
				<td class=right>Total : <?= $monTotal ?></td>
			</tr>
		</table>
		</div>
	</div>
</div>


</div>

<?php echo $this->Html->script('jquery-2.1.4.min.js'); ?>
<script type="text/javascript">
function show(magasin) {
	monMag = '#' + magasin ;
	$(monMag).slideToggle(0);
}
function showAll(){
	<?php foreach (LISTE_MAGASINS as $i=>$j) : ?>
	$('<?php echo("#". $i) ?>').show();
	<?php endforeach; ?>
}
function hideAll(){
	<?php foreach (LISTE_MAGASINS as $i=>$j) : ?>
	$('<?php echo("#". $i) ?>').hide();
	<?php endforeach; ?>
}
</script>
