<?php 
/********************************************************************************************
** Fichier : implantation.ctp
** Vue gérant l'affichage de l'implantation en magasin
** Contrôleur : Fonds
********************************************************************************************/
?>
<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">

	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
	
</nav>
<div class="fonds index large-9 medium-8 columns content">
<div class="right"><?= $this->Html->link(__('Imprimer'), ['action' => 'generatepdf', '?' => ['mode' => 'statistiques' ]], ['title'=>'PDF généré','onclick'=>'javascript:window.open(this.href,\'_blank\',\'toolbar=0,scrollbars=0,location=0,status=0,menubar=0,resizable=0,width=400,height=100\');return false;']) ?></div> 
	<h3><?= __('Implantation') ?></h3>
	
</div>


<?php echo $this->Html->script('jquery-2.1.4.min.js'); ?>
</script>



