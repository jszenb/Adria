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
 
 /**
  * Application ADRIA
  * Campus Condorcet (2016)
 */

$cakeDescription = 'Adria - Campus Condorcet';
?>
<!DOCTYPE html>
<html>
<head>
	<!-- *******************************************************************
	*
	* Application Adria
	* Campus Condorcet (2016)
	*
	******************************************************************** -->
    <?= $this->Html->charset() ?>
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

    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
	
	<!-- Jquery -->
	<?php echo $this->Html->script('jquery-2.1.4.min.js'); ?>

</head>
<body>
	<!-- -------------------- Bandeau supérieur -------------------------- -->
    <nav class="top-bar expanded" data-topbar role="navigation">
		
        <section class="top-bar-section">
			<?= $this->Html->link(
					$this->Html->image('logo_campus_condorcet.png', ['alt' => 'Campus Condorcet', 'width' => '533', 'height' => '100']),
					'http://www.campus-condorcet.fr',
					['target' => '_blank', 'escapeTitle' => false]); 
			?>

             <ul class="right"> 
				<?= $this->Html->link('Guide de l\'utilisateur consultation','/files/guides/CC_GED_ARC_ADRIA_Guide_consultation_vf.pdf', ['target' => '_blank', 'escapeTitle' => false, 'class' => 'button' ]); ?>
				<?= $this->Html->link('Guide de l\'utilisateur référent archives','/files/guides/CC_GED_ARC_ADRIA_Guide_referents_vf.pdf', ['target' => '_blank', 'escapeTitle' => false, 'class' => 'button' ]); ?>
				<?= $this->Html->link('Intranet', 'http://campus-condorcet.fr/intranet/Accueil-intranet', ['target' => '_blank', 'escapeTitle' => false, 'class' => 'button' ])?>		
				<?= $this->Html->link('Déconnexion', '/users/logout', ['escapeTitle' => false, 'class' => 'button' ])?>					
            </ul> 
        </section>
    </nav>
    <?= $this->Flash->render() ?>
	<!-- ------------ Zone de travail et barre de menu ------------------ -->
    <section class="container clearfix">
        <?= $this->fetch('content') ?>
		<?= $this->Flash->render('auth') ?>
    </section>
	<!-- -------------------- Fin zone de travail ------------------------ -->
	<!-- -------------------- Bandeau inférieur -------------------------- -->	
    <footer>
		<div align='right'><?= $this->Html->image('sceau_35px.gif', ['alt' => 'sceau Campus Condorcet']); ?></div>
    </footer>

	
	<!-- --------------- Gestion de la barre de menu --------------------- -->
	<script type="text/javascript">
		$(function() {     
			var menu_ul = $('.table-ref-head > li'),
			menu_a  = $('.table-ref > li');
			menu_a.hide();
			
			
			menu_ul.click(function(e) {

				e.preventDefault();
			
				if (menu_a.is(":visible")){menu_a.slideUp('normal');}
				else if (menu_a.is(":hidden")){menu_a.slideDown('normal');}

			});
		});
	</script> 

</body>

</html>
