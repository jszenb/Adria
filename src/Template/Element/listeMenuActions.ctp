	<!-- ----------------- Barre de menu ----------------- -->
	<ul class="side-nav">

        <li class="heading"><?= __('Consulter') ?></li>
		<?php if ($typeUserEnSession == PROFIL_CA) { ?>
			<li><?= $this->Html->link(__('Vos fonds'), ['controller' => 'Fonds', 'action' => 'index', '?' => ['type' => 'user']]) ?></li>		
		<?php } ?>
		<?php if ($typeUserEnSession == PROFIL_CO) { ?>
			<li><?= $this->Html->link(__('Fonds'), ['controller' => 'Fonds', 'action' => 'index', '?' => ['type' => 'nuser']]) ?></li>					
		<?php } else { ?>
			<li><?= $this->Html->link(__('Tous les fonds'), ['controller' => 'Fonds', 'action' => 'index', '?' => ['type' => 'nuser']]) ?></li>							
		<?php } ?>
		
		<?php if ($typeUserEnSession == PROFIL_CC) { ?>
			<li><?= $this->Html->link(__('Fonds supprimés'), ['controller' => 'Fonds', 'action' => 'index', '?' => ['type' => 'supprime']]) ?></li>				
		<?php } ?>
		
        <li><?= $this->Html->link(__('Etablissements'), ['controller' => 'Etablissements', 'action' => 'index']) ?></li>		
		<li><?= $this->Html->link(__('Entités documentaires'), ['controller' => 'EntiteDocs', 'action' => 'index']) ?></li>
		<li><?= $this->Html->link(__('Lieux de conservation'), ['controller' => 'LieuConservations', 'action' => 'index']) ?></li>
		<?php if ($typeUserEnSession != PROFIL_CO) { ?>		
			<li><?= $this->Html->link(__('Utilisateurs'), ['controller' => 'Users', 'action' => 'index']) ?></li>		
		<?php } ?>
		<li class="heading"><?= __('Explorer') ?></li>
		<li><?= $this->Html->link(__('Interroger la base'), ['controller' => 'Fonds', 'action' => 'recherche']) ?></li>		
		<li><?= $this->Html->link(__('Voir les statistiques'), ['controller' => 'Fonds', 'action' => 'statistiques']) ?></li>
		<li><?= $this->Html->link(__('Générer des rapports'), ['controller' => 'Fonds', 'action' => 'generaterapports']) ?></li>
		<!--
		<?php if ($typeUserEnSession == PROFIL_CC) { ?>
			<li><?= $this->Html->link(__('Voir l\'implantation'), ['controller' => 'Fonds', 'action' => 'implantation']) ?></li>		
		<?php } ?>	
		-->

		<?php if ($typeUserEnSession != PROFIL_CO) { ?>
		
		<li class="heading"><?= __('Ajouter') ?></li>

		<li><?= $this->Html->link(__('Un fonds'), ['controller' => 'Fonds', 'action' => 'add']) ?></li>		
		<?php } ?>		
		<?php if ($typeUserEnSession == PROFIL_CC) { ?>
			<li><?= $this->Html->link(__('Un établissement'), ['controller' => 'Etablissements', 'action' => 'add']) ?></li>
		<?php } ?>			
		<?php if ($typeUserEnSession == PROFIL_CC) { ?>
			<li><?= $this->Html->link(__('Une entité documentaire'), ['controller' => 'EntiteDocs','action' => 'add']) ?></li>
		<?php } ?>

		<?php if ($typeUserEnSession != PROFIL_CO) { ?>		
		<li><?= $this->Html->link(__('Un lieu de conservation'), ['controller' => 'LieuConservations', 'action' => 'add']) ?></li>		
        <?php } ?>		
		<?php if ($typeUserEnSession == PROFIL_CC) { ?>
			<li><?= $this->Html->link(__('Un utilisateur'), ['controller' => 'Users', 'action' => 'add']) ?></li>
		<?php } ?>				
		
		<?php if ($typeUserEnSession == PROFIL_CC) { ?>
	</ul>

	<ul class="side-nav">
		<div class="table-ref-head"> 
		<li class="heading">Tables de référence</li>
		</div>
		<div class="table-ref">	
			<li><?= $this->Html->link(__('Aire culturelle'), ['controller' => 'AireCulturelles','action' => 'index']) ?></li>
			<li><?= $this->Html->link(__('Raisons de suppression'), ['controller' => 'RaisonSuppressions','action' => 'index']) ?></li>
			<li><?= $this->Html->link(__('Disciplines'), ['controller' => 'Thematiques','action' => 'index']) ?></li>
			<li><?= $this->Html->link(__('Type d\'accroissements'), ['controller' => 'TypeAccroissements','action' => 'index']) ?></li>				
			<li><?= $this->Html->link(__('Type de conditionnements'), ['controller' => 'TypeConditionnements','action' => 'index']) ?></li>	
			<li><?= $this->Html->link(__('Type de documents'), ['controller' => 'TypeDocs','action' => 'index']) ?></li>			
			<li><?= $this->Html->link(__('Type de documents afférents'), ['controller' => 'TypeDocAfferents','action' => 'index']) ?></li>	
			<li><?= $this->Html->link(__('Type de fonds'), ['controller' => 'TypeFonds','action' => 'index']) ?></li>	
			<li><?= $this->Html->link(__('Type d\'instruments de recherche'), ['controller' => 'TypeInstrRechs','action' => 'index']) ?></li>
			<li><?= $this->Html->link(__('Type de modes d\'entrée'), ['controller' => 'TypeEntrees','action' => 'index']) ?></li>								
			<li><?= $this->Html->link(__('Type de numérisations'), ['controller' => 'TypeNumerisations','action' => 'index']) ?></li>
			<li><?= $this->Html->link(__('Type de statuts juridiques'), ['controller' => 'TypeStatJurids','action' => 'index']) ?></li>
			<li><?= $this->Html->link(__('Type de supports'), ['controller' => 'TypeSupports','action' => 'index']) ?></li>			
			<li><?= $this->Html->link(__('Type de traitements'), ['controller' => 'TypeTraitements','action' => 'index']) ?></li>
			<li><?= $this->Html->link(__('Type d\'utilisateurs'), ['controller' => 'TypeUsers','action' => 'index']) ?></li>		
			<li><?= $this->Html->link(__('Type de prise en charge'), ['controller' => 'TypePriseEnCharges','action' => 'index']) ?></li>	
			<li><?= $this->Html->link(__('Type de réalisation de traitement'), ['controller' => 'TypeRealisationTraitements','action' => 'index']) ?></li>	
		</div>
		<?php } ?>
    </ul> 
	<!-- ----------------- Fin barre de menu ----------------- -->
	<!-- ------------------ Zone de travail ------------------ -->