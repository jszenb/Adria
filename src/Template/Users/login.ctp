<div class="users index large-9 medium-8 columns content">
<!-- V01.52 -->
	<h1>ADRIA - IMAGE PREPRODUCTION</h1>
	<table cellpadding="0" cellspacing="0">
		<tr>
			<td colspan="2">
				<h3>Application dynamique pour le recensement et l'inventaire des archives<h3>
			</td>
		</tr>
		<tr>
			<td>
				<?= $this->Form->create() ?>
				<?= $this->Form->input('login', ['label' => 'Identifiant']) ?>
				<?= $this->Form->input('password', ['label' => 'Mot de passe']) ?> 
				<?= $this->Form->button('Connexion') ?>
				<?= $this->Form->end() ?>            
			</td>
			<td valign="center">
				<div class="input text" align="center">
					<h5>Bienvenue sur Adria Préproduction</h5>
					<p>
						VOUS ETES SUR LA PREPRODUCTION
						<br>Nouvelles fonctionnalités :
                                                <ul>
                                                <li>Gestion de la prestation de traitement externalisés
                                                <li>Gestion de l'implantation en magasin (page réservée à l'administrateur Condorcet)
					</p>
				</div>
			</td>
		</tr>
	</table>
</div>
