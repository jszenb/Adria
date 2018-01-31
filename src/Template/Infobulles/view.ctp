<html>
<head>
    <?= $this->Html->css('base.css') ?>
    <?= $this->Html->css('cake.css') ?>
</head>
<body>
	<table width="100%" border=0>
		<tr>
			<td colspan="2">
				<h5>Liste d'aide "<?php echo ($titre) ?>"</h5>
			</td>
		</tr>
		<?php foreach ($resultats as $resultat) { ?>
		<tr>
			<?php if ( $titre == 'Disciplines' || $titre == 'Aires culturelles') {?>
				<td width="15%"><?=  $resultat['intitule'] ?></td>
				<td width="85%"><?=  $resultat['description'] ?></td>				
				
			<?php 
			} 
			else {
			?>
				<td width="15%"><?=  $resultat['type'] ?></td>
				<td width="85%"><?=  $resultat['description'] ?></td>
			<?php
			}
			?>
		</tr>
		<?php } ?>
	</table>
<br/>
<center>
	<a href="javascript:close();">Fermer</a>
</center>
<br/>
</body>
</html>

