<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>


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
 <!-- Fichier vide pour laisser croire à l'object TCPDF qu'il est lié à un HTML -->
 <!-- NE PAS SUPPRIMER CE FICHIER -->
</body>
</html>
