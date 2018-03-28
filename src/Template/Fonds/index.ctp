<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">

	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
	
</nav>
<div class="fonds index large-9 medium-8 columns content">
    <h3><?= __('Fonds') ?></h3>
	
	<!-- ******************************* Fonds de l'utilisateur connecté ******************************* -->
    <?php 				
	/* Préparation au comptage des volumétries et à l'affichage des résultats de volumétrie */
	require_once WWW_ROOT . DS . 'php/prepareVolumetrie.php';
	require_once WWW_ROOT . DS . 'php/calculTotauxVolumetrie.php';

	
	/* Détermination du type d'affichage : liste des fonds de l'utilisateur connecté ou des autres fonds ? */
	if ($typeAffichage=="user") { 
		$libelle = "Vos fonds";
		$sous_libelle = '<b>Total : ' . $this->Paginator->counter('{{count}}') . ' fonds représentant ' . str_replace(',','', $totalMlRecherche) . " ml (sur ". str_replace(',','',$totalMl). ") et "
						. str_replace(',','',$totalGoRecherche) . " Go (sur ". str_replace(',','',$totalGo) . ")</b>" ;
	}
	else {
		$sous_libelle = '<b>Total : ' . $this->Paginator->counter('{{count}}') . ' fonds représentant ' . str_replace(',','', $totalMlRecherche) . " ml et "
						. str_replace(',','',$totalGoRecherche) . " Go </b>" ;	
		if ($typeAffichage == "supprime") {
			$libelle = "Fonds supprimés";
		}
		else {
			$libelle = "Les fonds rejoignant le Grand Equipement Documentaire";
		}
	}?>
	<div class="right">
		<?= $this->Html->link(__('Télécharger'), [	'action' => 'generatepdf', 
												'?' => [
													'mode' => $typeAffichage, 
													'totalFonds' => $this->Paginator->counter('{{count}}'),
													'totalMl' => $totalMl, 
													'totalGo' => $totalGo, 
													'totalMlRecherche' => $totalMlRecherche, 
													'totalGoRecherche' => $totalGoRecherche]
												], 
												['title'=>'','onclick'=>'javascript:window.open(this.href,\'_blank\',\'toolbar=0,scrollbars=0,location=0,status=0,menubar=0,resizable=0,width=400,height=100\');return false;']) ?>
		&nbsp;&nbsp;
		<?php 
			if ( ($typeUserEnSession == PROFIL_CC) || ($typeUserEnSession == PROFIL_CA && $typeAffichage == "user") ) {
				echo $this->Html->link('Exporter', array('controller' => 'Fonds', 'action' => 'generatecsv', '?' => ['mode' => $typeAffichage]));	
			}
	?>
	</div>

	<h4><?php echo ($libelle); ?></h4>
	<div class="limite">
		<table>
			<tr>
				<td>
					<p><?php echo( $sous_libelle); ?> 
				</td>
				<td>
					<div align="right">
					<?= $this->Form->create(null, ['type' => 'get', 'url' => '/fonds/index', 'name' => 'f_limite']); ?>
					<?= $this->Form->hidden('type',['value' => $typeAffichage]); ?>
					<?= $this->Form->select('limite', 
											['' => 'Résultats par page', 20 => '20', 30 => '30', 40 => '40', 50 => '50', 80 => '80', 100 => '100'], 
											['class' => 'limite', 'label' => 'Nombre de résultats par page', 'onChange' => 'submit();']
											) ?>
                                            
                    <!-- La div suivant est déclarée ici pour inclure la raison de suppression dans le formulaire
                         mais son affichage se fait en popup (gérée en Jquery) -->
                    <div id="popup1" class="popup_block" align="left">
                        <p>Voulez-vous vraiment supprimer ce fonds ? <br>
                        <p>Pour confirmer, choisissez une raison de suppression puis cliquez sur "supprimer", sinon cliquez sur "annuler".<br><br>
                        <?php
                        /* Tableau nécessaire pour gérer les raisons de suppression */
                        $tabRaisonSuppression = [];
                        foreach ($raisonSuppressions as $raisonSuppression):

                            
                            $tabRaisonSuppression[$raisonSuppression->id] = addslashes($raisonSuppression->raison);     

                        endforeach;

                        
                        echo $this->Form->select('raisonSuppression', 
                                             $tabRaisonSuppression, 
                                             ['class' => 'raisonSuppression', 'label' => 'Raison de suppression', 'id' => 'raisonSuppression', 'onChange' => 'changeRaisonSuppression();']
                                            );
                        ?>

                        <table>
                            <tr>
                                <td align="center">
                                <?php
                                    echo $this->Html->link(__('Supprimer'), ['controller' => 'Fonds', 'action' => 'delete'], ['id' => 'adelete']);
                                ?>
                                </td>
                                <td align="center">
                                    <a href="#" class="close">Annuler</a>
                                </td>
                            </tr>
                        </table>

                    </div>
                                            
                                            
					<?= $this->Form->end();  ?>
					</div>
				</td>
			</tr>
		</table>
	</div>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th width=40%><?= $this->Paginator->sort('nom','Nom', ['direction' => 'desc']) ?></th>
                <!-- <th width=10%><?=$this->Paginator->sort('Etablissements.code','Etablissement') ?></th> -->
                <th width=10%>Etablissement</th>
                <!-- <th width=10%><?= $this->Paginator->sort('EntiteDocs.code', 'Entité documentaire') ?></th>-->
                <th width=10%>Entité documentaire</th>
		<!-- <th width=13%><?= $this->Paginator->sort('TypeFonds.type', 'Type de fonds') ?></th>-->
		<th width=13%>Type de fonds</th>
                <th width=6%><?= $this->Paginator->sort('nb_ml', 'Vol. ml') ?></th>
                <th width=6%><?= $this->Paginator->sort('nb_go', 'Vol. Go') ?></th>				
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
		
        <tbody>
            <?php 

			foreach ($fonds as $fond): 

					if ( ($typeUserEnSession == PROFIL_CC) || ( (in_array($typeUserEnSession, [PROFIL_CA, PROFIL_CO])) && (!$fond->ind_suppr) ) ): ?>
					<tr>
						<td><?= h($fond->nom) ?><?= $fond->ind_maj ? '&nbsp;&#x2714;' : '' ?>
						<?php if ($fond->ind_suppr) {
								echo('<b>(supprimé)</b>');
							} 
						?>
						
						</td>
						<td><?= $fond->entite_doc->has('etablissement') ? $this->Html->link($fond->entite_doc->etablissement->code, ['controller' => 'Etablissements', 'action' => 'view', $fond->entite_doc->etablissement->id]) : '' ?></td>
						<td><?= $fond->has('entite_doc') ? $this->Html->link($fond->entite_doc->code, ['controller' => 'EntiteDocs', 'action' => 'view', $fond->entite_doc->id]) : '' ?></td>
						<td><?= $fond->has('type_fond') ? h($fond->type_fond->type) : '' ?></td>				
						<td><?= $fond->ind_nb_ml_inconnu ? h('inconnue') : $this->Number->format($fond->nb_ml) ?></td>
						<td><?= $fond->ind_nb_go_inconnu ? h('inconnue') : $this->Number->format($fond->nb_go) ?></td>		
						<td class="actions">
							<?= $this->Html->link(__('Consulter'), ['action' => 'view', $fond->id]) ?>
							<?php 
								// Dans le cas des fonds supprimés, la modification de fonds n'est pas possible.
								if ($typeAffichage != "supprime") {
									if ( ($typeUserEnSession == PROFIL_CC) || ( ($typeUserEnSession == PROFIL_CA) && ($idEntiteDocEnSession == $fond->entite_doc->id) ) ){ 
										?>
										<?= $this->Html->link(__('Modifier'), ['action' => 'edit', $fond->id]) ?>
									<?php } 
								} 
								
								// L'utilisateur CC peut supprimer les fonds non-supprimés ou réactivés les fonds supprimés
								if ($typeUserEnSession == PROFIL_CC) { 
									if ($typeAffichage != "supprime") {
										// l'affichage en cours concerne les fonds non-supprimés : on peut donc les supprimer ?>
										<?php //$this->Form->postLink(__('Supprimer'), ['action' => 'delete', $fond->id], ['confirm' => __('Voulez-vous vraiment supprimer le fonds {0} ?', $fond->nom)])
                                              //echo $this->Html->link(__('Supprimer'), ['action' => '', $fond->id], ['id' => 'del'.$fond->id, 'onclick' => 'javascript:askMe("del'.$fond->id.'","'.$fond->nom.'")'])
                                        ?>
                                        <a href="#" data-width="500" data-rel="popup1" data-id = "<?= $fond->id ?>" class="poplight">Supprimer</a>
									<?php }
									else { 
										// l'affichage en cours concerne les fonds supprimés : on peut donc les réactivés 
										?>
										<?= $this->Form->postLink(__('Réactiver'), ['action' => 'reactivate', $fond->id], ['confirm' => __('Voulez-vous vraiment réactiver le fonds {0} ?', $fond->nom)]) ?>
									<?php }
								} ?>
						</td>
					</tr>
					<?php 
					endif;
			endforeach; ?>
			<!-- Volumétrie totale
			<tr>
				<td/>
				<td/>
				<td/>
				<td class="right"><b>Total :</b></td>
				<td><b><?php echo(number_format($totalMlEnCours, 0, '.', '') . " / " . str_replace(',','',$totalMl)) ?></b></td>
				<td><b><?php echo(number_format($totalGoEnCours, 0, '.', '') . " / " . str_replace(',','',$totalGo)) ?></b></td>
				<td/>
			</tr>		
			-->
        </tbody>
    </table>


	<?php echo $this->element('navigationIndex'); ?>

<!-- 
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->hasPrev() ? $this->Paginator->prev('< ' . __('précédent')) : '' ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->hasNext() ? $this->Paginator->next(__('suivant') . ' >') : '' ?>
        </ul>
        <p><?= $this->Paginator->counter('{{page}} sur {{pages}}') ?></p>
    </div>	
-->
</div>

<?php echo $this->Html->script('jquery-2.1.4.min.js'); ?>
<script type="text/javascript">
jQuery(function($){
						   		   
	//Lorsque vous cliquez sur un lien de la classe poplight
	$('a.poplight').on('click', function() {
		var popID = $(this).data('rel'); //Trouver la pop-up correspondante
		var popWidth = $(this).data('width'); //Trouver la largeur
        var popFondsId = $(this).data('id') ; // Id du fonds à supprimer

		//Faire apparaitre la pop-up et ajouter le lien d'annulation de l'action
		$('#' + popID).fadeIn().css({ 'width': popWidth});
        //.append('<a href="#" class="close">Annuler</a>');
		
        // Modification de l'action du lien pour pointer sur le bon fonds à supprimer. Pour la raison, on met par défaut celle qui est sélectionnée
        // au moment de l'affichage. 
        $("#adelete").attr("href","Fonds/delete/" + popFondsId + "?raisonSuppression=" + $("#raisonSuppression").val() ) ;
        
		//Récupération du margin, qui permettra de centrer la fenêtre - on ajuste de 80px en conformité avec le CSS
		var popMargTop = ($('#' + popID).height() + 80) / 2;
		var popMargLeft = ($('#' + popID).width() + 80) / 2;
		
		//Apply Margin to Popup
		$('#' + popID).css({ 
			'margin-top' : -popMargTop,
			'margin-left' : -popMargLeft
		});
		
		//Apparition du fond - .css({'filter' : 'alpha(opacity=80)'}) pour corriger les bogues d'anciennes versions de IE
		$('body').append('<div id="fade"></div>');
		$('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn();
		
		return false;
	});
	
	
	//Close Popups and Fade Layer
	$('body').on('click', 'a.close, #fade', function() { //Au clic sur le body...
		$('#fade , .popup_block').fadeOut(function() {
			$('#fade').remove();  
	}); //...ils disparaissent ensemble
		
		return false;
	});

	
});

function changeRaisonSuppression() {
    var monHref = $("#adelete").attr("href");
    tabHref = monHref.split("?");
    monHref = tabHref[0] + "?raisonSuppression=" + $("#raisonSuppression").val();
    $("#adelete").attr("href",monHref) ;
}


</script>
