<html>
<head>
    <?= $this->Html->css('base.css') ?>
    <?= $this->Html->css('cake.css') ?>
</head>
<body>
<center>
Votre PDF est prêt.<br>
Pour le visualiser, <?php echo $this->Html->link('cliquez ici','/files/pdf/'.$filename.'.pdf'); ?>
<br/>
<a href="javascript:close();">Fermer</a>
<br/>
</center>
</body>
</html>
