<?php
/********************************************************************************************/
// Application ADRIA
// EntiteDocsController.php
// Contrôleur pour la classe des entités documentaires
//
// Campus Condorcet (2016)
/********************************************************************************************/
namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\View\View;
use Cake\Filesystem\File;

/**
 * EntiteDocs Controller
 *
 * @property \App\Model\Table\EntiteDocsTable $EntiteDocs
 */
class EntiteDocsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
		if (empty($this->request->query('sort'))) {
			$this->paginate = [
				//'contain' => ['Etablissements','LieuConservations', 'Fonds']
				'contain' => ['Etablissements'],
				'limit' => 30,
				'sortWhitelist' => [
					'nom',	
					'code',
					'Etablissements.code',
					'adresse_1',
					'adresse_cp',
					'adresse_ville'
				], 
				'order' => ['nom' => 'asc']
			];
		}
		else {
			$this->paginate = [
				//'contain' => ['Etablissements','LieuConservations', 'Fonds']
				'contain' => ['Etablissements'],
				'limit' => 30,
				'sortWhitelist' => [
					'nom',	
					'code',
					'Etablissements.code',
					'adresse_1',
					'adresse_cp',
					'adresse_ville'
				]			
			];			
		}
		//dump($this->paginate);
        $this->set('entiteDocs', $this->paginate($this->EntiteDocs));
        $this->set('_serialize', ['entiteDocs', 'count']);
    }

    /**
     * View method
     *
     * @param string|null $id Entite Doc id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $entiteDoc = $this->EntiteDocs->get($id, [
            'contain' => [
				'Etablissements', 
				'LieuConservations' => function($q) {
							return $q
									->order(['LieuConservations.nom' => 'ASC']);
						},  
				'Fonds' => function($q) {
							return $q
									->contain('TypeFonds')
									->where('ind_suppr <> 1')
									->order(['Fonds.nom' => 'ASC']);
						}, 
				'Users' => function($q) {
							return $q
									->where(['type_user_id != ' => PROFIL_CO]);
						}, 
			]
        ]);
		
		$this->set('entiteDoc', $entiteDoc);
		$this->set('_serialize', ['entiteDoc']);
		
		// Statistiques type de fonds
		$res_TypeFonds = $this->statTypeFonds($entiteDoc['id']);
		$this->set('tab_typeFonds', $res_TypeFonds[1]);
		$this->set('nb_typeFonds', $res_TypeFonds[0]->count());
		
		// Statistiques par thématiques
		$res_Thematiques = $this->statThematiques($entiteDoc['id']);
		$this->set('tab_Thematiques', $res_Thematiques[1]);
		$this->set('nb_Thematiques', $res_Thematiques[0]->count());
		$this->set('couleurs_Thematiques', $res_Thematiques[2]);

		// Statistiques par aires culturelles
		$res_Aires = $this->statAires($entiteDoc['id']);
		$this->set('tab_Aires', $res_Aires[1]);
		$this->set('nb_Aires', $res_Aires[0]->count());	
		$this->set('couleurs_Aires', $res_Aires[2]);

		// Statistiques par dates extrêmes
		$res_dateExtreme = $this->statDates($entiteDoc['id']);
		$this->set('tab_Dates', $res_dateExtreme[1]);		
		$this->set('nb_Dates', $res_dateExtreme[0]->count());
    }
	
	/**
    * statTypeFonds method
    *
    * Renvoie la requête pour les statistiques fonds par type de fonds, ainsi que le tableau de résultat construit avec elle. 
    */
	public function statTypeFonds($entiteDocId)
	{
		$stat_typeFonds = $this->EntiteDocs->Fonds->find('all', ['contain' => ['TypeFonds']]);
		
		$stat_typeFonds->select([
				'count' => $stat_typeFonds->func()->sum('Fonds.nb_ml'),
				'libelle' => 'TypeFonds.type'
				])
				->where(['ind_suppr <> ' => 1])		
				->where(['entite_doc_id ' => $entiteDocId])
				->group('type_fond_id')
				->order(['TypeFonds.type' => 'ASC']);
	
		$tab_typeFonds = "[['Type de fonds', 'Volumétrie en ml']";
		foreach ($stat_typeFonds as $ligne) {
			$tab_typeFonds .= ",['" . addslashes($ligne['libelle']) . "'," . number_format($ligne['count'], 2,'.','') . "]";
		}
		
		$tab_typeFonds .= "]";				
		return([$stat_typeFonds, $tab_typeFonds]);
	}
	
	/**
    * statThematiques method
    *
    * Cette méthode recherche les volumétries ml par thématiques, et renvoie
    * le tableau de données permettant de construire le graphique associé dans la vue
	* ainsi qu'un tableau contenant les couleurs pour chaque thématique. Enfin, 
	* elle renvoie la requête même. 
    */	
	public function statThematiques($entiteDocId)
	{
		$stat_Thematiques = $this->EntiteDocs->Fonds->find('all');
		$stat_Thematiques->select([
				'count' => $stat_Thematiques->func()->sum('Fonds.nb_ml'),
				'libelle' => 'Thematiques.intitule',
				'couleur' => 'Thematiques.couleur'
				])
				->matching('Thematiques')
				->where(['ind_suppr <> ' => 1])				
				->where(['entite_doc_id ' => $entiteDocId])				
				->group('Thematiques.id')
				->order(['Thematiques.intitule' => 'ASC']);		
		$tab_Thematiques = "[['Thématiques', 'Volumétrie des fonds (en mètre linéaire)']";
		$lg = $stat_Thematiques->count();
		$i = 0;		
		$tab_Couleurs = "[";		
		foreach ($stat_Thematiques as $ligne) {
			$tab_Thematiques .= ",['" . addslashes($ligne['libelle']) . "'," . number_format($ligne['count'], 2,'.','') . "]";
			
			if ($i < ($lg - 1) ) {
				$tab_Couleurs .= "'" . $ligne['couleur'] . "',";
			}
			else {
				$tab_Couleurs .= "'" . $ligne['couleur'] . "'";
			}
			$i ++ ;			
		}
		
		$tab_Thematiques .= "]";
		$tab_Couleurs .= "]";
		
		return([$stat_Thematiques, $tab_Thematiques, $tab_Couleurs]);
	}
	
	/**
    * statAires method
    *
    * Cette méthode recherche les volumétries ml par aires culturelles, et renvoie
    * le tableau de données permettant de construire le graphique associé dans la vue
	* ainsi qu'un tableau contenant les couleurs pour chaque aire. Enfin, 
	* elle renvoie la requête même. 
    */	
	public function statAires($entiteDocId)
	{
		$stat_Aires = $this->EntiteDocs->Fonds->find('all');
		$stat_Aires->select([
				'count' => $stat_Aires->func()->sum('Fonds.nb_ml'),
				'libelle' => 'AireCulturelles.intitule',
				'couleur' => 'AireCulturelles.couleur'
				])
				->matching('AireCulturelles')
				->where(['ind_suppr <> ' => 1])		
				->where(['entite_doc_id ' => $entiteDocId])
				->group('AireCulturelles.id')
				->order(['AireCulturelles.intitule' => 'ASC']);		
		$tab_Aires = "[['Aire culturelles', 'Volumétrie des fonds (en mètre linéaire)']";
		$lg = $stat_Aires->count();
		$i = 0;
		$tab_Couleurs = "[";
		foreach ($stat_Aires as $ligne) {
			
			$tab_Aires .= ",['" . addslashes($ligne['libelle']) . "'," . number_format($ligne['count'], 2,'.','') . "]";
			
			if ($i < ($lg - 1) ) {
				$tab_Couleurs .= "'" . $ligne['couleur'] . "',";
			}
			else {
				$tab_Couleurs .= "'" . $ligne['couleur'] . "'";
			}
			$i ++ ;
		}
		
		$tab_Aires .= "]";	
		$tab_Couleurs .= "]";
		
		return([$stat_Aires, $tab_Aires, $tab_Couleurs]);	
		
	}

	/**
    * statDates method
    *
    * Renvoie la requête pour les statistiques dates extrêmes 
    */	
	public function statDates($entiteDocId)	
	{
		// Statistiques dates extrêmes
		$stat_dateExtreme = $this->EntiteDocs->Fonds->find('all');
		$stat_dateExtreme->select([
				'nom' => 'Fonds.nom',
				'debut' => 'Fonds.annee_deb',
				'fin' => 'Fonds.annee_fin'
				])
				->where([
					['ind_suppr <> ' => 1],
					['entite_doc_id ' => $entiteDocId ],
					['(annee_deb is not null or annee_fin is not null)']
				])				
				->order([
					'Fonds.annee_deb' => 'ASC',
					'Fonds.annee_fin' => 'ASC',
					'Fonds.nom' => 'ASC'
				]);				
		$tab_Dates= "[[{ type: 'string', id: 'Fonds' }, 
		               { type: 'string', id: 'dummy bar label' }, 
					   { type: 'string', role: 'tooltip', 'p': {'html': true} }, 
					   { type: 'date', id: 'Début' }, 
					   { type: 'date', id: 'Fin' }
					]";
					
		foreach ($stat_dateExtreme as $ligne) {
			
			// Gestion des fonds n'ayant qu'une année de renseignée : 
			// on aligne la date manquante sur la seule date connue.
			$debut = $ligne['debut'];
			$fin = $ligne['fin'];
			
			if (empty($fin) and !empty($debut)) {
				$fin = $debut;
			}
			if (empty($debut) and !empty($fin)) {
				$debut = $fin;
			}			
			
			// Création du message à afficher pour le graphique :
			$diff = $fin - $debut;			
			$label = '<b>Fonds '.addslashes($ligne['nom']).'</b><hr>'.$debut.' - '.$fin.'<br>  '. $diff. ' année(s)<br><br>';			
			$tab_Dates .= ',[\''.addslashes($ligne['nom']).'\',\''.addslashes($ligne['nom']).'\',\''.$label.'\', new Date('.$debut.', 0), new Date('.$fin.', 11)]';
		}
		
		$tab_Dates .= "]";
		
		return([$stat_dateExtreme, $tab_Dates]);	
		
	}
	
    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $entiteDoc = $this->EntiteDocs->newEntity();
        if ($this->request->is('post')) {
            $entiteDoc = $this->EntiteDocs->patchEntity($entiteDoc, $this->request->data);
            if ($this->EntiteDocs->save($entiteDoc)) {
                $this->Flash->success(__('Entité documentaire créé.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('L\'entité documentaire n\'a pas pu être créée.\nVérifiez les messages d\'erreur indiqués dans le formulaire.'));
            }
        }
        $etablissements = $this->EntiteDocs->Etablissements->find('list', ['limit' => 200, 'order' => ['code' => 'ASC']]);
        $lieuConservations = $this->EntiteDocs->LieuConservations->find('list', ['limit' => 200, 'order' => ['nom' => 'ASC']]);
        $this->set(compact('entiteDoc', 'etablissements', 'lieuConservations'));
        $this->set('_serialize', ['entiteDoc']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Entite Doc id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$idEntiteDocEnSession = $this->request->query('entiteDoc');
		$typeUserEnSession =  $this->request->query('typeUser');
		
        $entiteDoc = $this->EntiteDocs->get($id, [
            'contain' => ['LieuConservations']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $entiteDoc = $this->EntiteDocs->patchEntity($entiteDoc, $this->request->data);
            if ($this->EntiteDocs->save($entiteDoc)) {
                $this->Flash->success(__('Entité documentaire modifiée.'));
                return $this->redirect(['action' => 'view/'.$entiteDoc['id']]);
            } else {
                $this->Flash->error(__('L\'entité documentaire n\'a pas pu être modifiée.\nVérifiez les messages d\'erreur indiqués dans le formulaire.'));
            }
        }
        $etablissements = $this->EntiteDocs->Etablissements->find('list', ['limit' => 200, 'order' => ['code' => 'ASC']]);
		
		//$lieuConservations = $this->EntiteDocs->LieuConservations->find('list')->contain(['EntiteDocs']);
		if ($typeUserEnSession == PROFIL_CA) {
			$lieuConservations = $this->EntiteDocs->LieuConservations->find('list')->matching(
									'EntiteDocs', function ($q) use  ($idEntiteDocEnSession){
										return $q->where(['EntiteDocs.id' => $idEntiteDocEnSession ]);
									});
		}
		else{
			$lieuConservations = $this->EntiteDocs->LieuConservations->find('list', [
			'limit' => 200, 
			'order' => ['nom' => 'ASC']
			]);
		}
		
        $this->set(compact('entiteDoc', 'etablissements', 'lieuConservations'));
        $this->set('_serialize', ['entiteDoc']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Entite Doc id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $entiteDoc = $this->EntiteDocs->get($id);
		
		try {
				$this->EntiteDocs->delete($entiteDoc);
		} 
		catch (Exception $e) {
			$error = 'Cette entité documentaire ne peut pas être supprimer : des données liées ont été détectées.';
			// The exact error message is $e->getMessage();
			$this->set('error', $error);
			$this->Flash->error(__('Cette entité documentaire ne peut pas être supprimer : des données liées ont été détectées.'));
		}
		
		$this->Flash->success(__('The entite doc has been deleted.'));

		/*
        if ($this->EntiteDocs->delete($entiteDoc)) {
            $this->Flash->success(__('The entite doc has been deleted.'));
        } else {
            $this->Flash->error(__('Cette entité documentaire ne peut pas être supprimer : des données liées ont été détectées.'));
        }*/
        return $this->redirect(['action' => 'index']);
    }

	/**
     * isAuthorized method
     *
     * @param string|null $user user en cours dans la session
     * @return true or false
     * @throws nothing
     */
	public function isAuthorized($user)
	{
		/**********************************************************************
		* Règles d'autorisation :
		* Le user CC a tout les droits : c'est le contrôleur App général qui 
		* règle son cas (appel en fin de fonction).
		* Le user CA peut :
		*     - dresser la liste des entités (index)
		*     - consulter toutes les entités documentaires (view)
		*     - modifier son entité documentaire (edit)
		*     - ne peut pas ajouter une entité documentaire (add)
		*     - ne peut pas supprimer d'entité documentaire
		**********************************************************************/

		if (isset($user['type_user_id']) && $user['type_user_id'] == PROFIL_CA) {

			
			if (isset($this->request->params['action'])) {
				$action = $this->request->params['action'];
				
				// Action consultation ou lister les entités : 
				// actions possibles sans condition
				if(in_array($action, ['index','view','generatepdf'])) {

					return true;
					
				}
				else {
					//debug('action non index et non view');
					
					// Autres actions :
					// à évaluer selon les cas d'action :
					//   - action d'ajouter : KO
					//   - action d'éditer : OK pour son entité documentaire, sinon KO
					//   - action de supprimer : KO
					if (in_array($action, ['add', 'delete'])) {
						
						return false;
						
					}
					else {
						// L'identifiant de l'entite documentaire pour laquelle on veut l'action
						// est dans les paramètres reçus de la page avec l'action. 
						if (isset($this->request->params['pass'][0])) {
							
							$entiteDoc = $this->request->params['pass'][0];
							
							//debug('entiteDoc du paramètre : '.$entiteDoc);
							//debug('entiteDoc du user : '.$user['entite_doc_id']);
							
							// Si l'entité documentaire pour laquelle l'action est demandée est
							// celle du user, c'est OK. Sinon c'est KO.
							if ($user['entite_doc_id'] == $entiteDoc) {
								return true;
							}
							else {
								return false;
							}
						}
					}
				}
			}
		}
		// Dans tous les autres cas, le contrôleur App principale prend la décision
		return parent::isAuthorized($user);
	}	

    /**
     * generatepdf method
	 * Production de la fiche entité documentaire pour l'entité en cours
     * @return void
     */	
    public function generatepdf() {
		
		$view = new View();
		$title = "";
		$filename = "";
		$query = "";
		$template = "";
		
		$pathTemplate = ROOT . DS . 'webroot' . DS . 'files' . DS . 'templates' . DS ;	
		$pathTemp = ROOT . DS . 'webroot' . DS . 'files' . DS . 'temp' . DS ;
		$pathTempCourt =  'files/temp/' ;

		
		$user = $this->request->session()->read('Auth');
		$monIdUser = $user['User']['id'];

		// Le template est le canevas dans lequel on va injecter les données de l'entité
		$template = 'EntiteDocs/pdf/ficheEntiteDoc';
		
		// On crée un fichier temporaire qui contiendra l'état
		$filename = "ficheEntiteDoc".$monIdUser.'-'.mt_rand();
		$title = "Fiche entité documentaire" ;
		
		// Récupération des données de l'entité
		$id = $this->request->query('id');	
		$query = $this->EntiteDocs->get($id, [
            'contain' => [
				'Etablissements', 
				'LieuConservations' => function($q) {
							return $q
									->order(['LieuConservations.nom' => 'ASC']);
						},  
				'Fonds' => function($q) {
							return $q
									->contain('TypeFonds')
									->where('ind_suppr <> 1')
									->order(['Fonds.nom' => 'ASC']);
						}, 
				'Users'
			]
        ]);
		
		// Affectation des données à la variable ad hoc dans la vue 
		// permettant de générer le pdf
		$view->set('entiteDoc',$query);
		$view->set('_serialize', ['entiteDoc']);		
		
		// Statistiques type de fonds
		$res_TypeFonds = $this->statTypeFonds($id);
		$nameImgGraphe = $this->createImgGraphe ('prepareGrapheTypeDeFonds', $pathTemplate, $pathTemp, $monIdUser, $res_TypeFonds[1], '');
		$view->set('temp_img_stat_typeFonds', $pathTempCourt . $nameImgGraphe);	
		$view->set('nb_TypeFonds',$res_TypeFonds[0]->count());
		//dump ($pathTempCourt . $nameImgGraphe) ;
		
		// Statistiques par thématiques
		$res_Thematiques = $this->statThematiques($id);		
		$nameImgGraphe = $this->createImgGraphe ('prepareGrapheThematiques', $pathTemplate, $pathTemp, $monIdUser, $res_Thematiques[1], $res_Thematiques[2]);
		$view->set('temp_img_stat_thematiques', $pathTempCourt . $nameImgGraphe);
		$view->set('nb_Thematiques',$res_Thematiques[0]->count());
	
		//dump ($pathTempCourt . $nameImgGraphe) ;
	
		// Statistiques par aires culturelles
		$res_Aires = $this->statAires($id);
		$nameImgGraphe = $this->createImgGraphe ('prepareGrapheAires', $pathTemplate, $pathTemp, $monIdUser, $res_Aires[1], $res_Aires[2]);
		$view->set('temp_img_stat_aires', $pathTempCourt . $nameImgGraphe);
		$view->set('nb_Aires',$res_Aires[0]->count());
		
		//dump ($pathTempCourt . $nameImgGraphe) ;

		// Statistiques par aires culturelles
		$res_Dates = $this->statDates($id);
		$nameImgGraphe = $this->createImgGraphe ('prepareGrapheDates', $pathTemplate, $pathTemp, $monIdUser, $res_Dates[1], '');
		$view->set('temp_img_stat_dates', $pathTempCourt . $nameImgGraphe);
		$view->set('nb_Dates',$res_Dates[0]->count());
		//dump ($pathTempCourt . $nameImgGraphe) ;
		
		// Affectation des autres variables.
		$this->set('filename',$filename);
		$this->set('title',$title);
        $view->set(compact('title', 
		                   'filename', 
		                   'temp_img_stat_typeFonds', 
						   'temp_img_stat_thematiques', 
						   'temp_img_stat_aires', 
						   'temp_img_stat_dates',
						   'nb_TypeFonds',
						   'nb_Thematiques',
						   'nb_Aires',
						   'nb_Dates',
						   'couleurs_Thematiques',
						   'couleurs_Aires'));

		//dump($view);
		
		// La fonction sort en neutralisant le layout par défaut
		// (pour éviter l'affichage du menu par exemple), et 
		// appelle le template qu'on a déterminé pour la fiche
        $view->render('EntiteDocs/pdf/generatepdf', 'pdf/ficheentitedoc');
		$this->viewBuilder()->autoLayout(false);
    }	

    /**
     * createImgGraphe method
	 * Création d'un fichier image contenant un graphique
	 * Entrées : - $grapheTemplate : nom du template HTML pour le graphique à générer
	 *           - $pathTemplate : répertoire où sont stockés les templates 
	 *           - $pathTemplaire : répertoire temporaire où créer des fichiers
	 *           - $idUser : identifiant de l'utilisateur en cours (utile pour être sûr que les fichiers créés sont uniques)
	 *           - $tabDonnees : tableau des données pour générer le graphique
     * @return le nom du fichier contenant le graphique
     */		
	
	function createImgGraphe( $grapheTemplate, $pathTemplate, $pathTemporaire, $idUser, $tabDonnees, $tabCouleurs ) {
		
		// Commande phantomjs pour créer l'image.
		$phantomBin = ROOT . DS . 'bin' . DS . 'phantomjs  --ignore-ssl-errors=yes ' . ROOT . DS . 'webroot' . DS . 'js' . DS . 'generationGraphes.js';
			
		// Il nous faut :
		// - retrouver le fichier de template, 
		// - préparer un fichier temporaire où générer le graphique au format HTML pour ce template avec les données en cours,
		// - construire un nom pour l'image à produire à partir de cet HTML,
		// - préparer le fichier avec cette image. 
				
		// Fichier dans lequel est stocké le template HTML pour le graphe
		$templateHtmlGraphe = $pathTemplate . $grapheTemplate . '.html' ;
		
		// Fichier temporaire contenant ce fichier pour le cas en cours 
		$fileHtmlGraphe = $pathTemporaire . $grapheTemplate . $idUser . '-'.mt_rand() . '.html';
		
		// Construction du nom du fichier contenant l'image à produire
		$nameImgGraphe = $grapheTemplate . $idUser . '-'.mt_rand() . '.png';
		
		// Fichier temporaire pour cette image 
		$fileImgGraphe = $pathTemporaire . $nameImgGraphe ;

		// Tous les noms et chemins sont prêts. On construit donc le fichier HTML à partir
		// duquel le graphique sera construit. Pour cela, on copie le template dans 
		// le fichier temporaire HTML. Dans ce dernier, on remplace le mot "tableau"
		// par le tableau de données reçu en paramètre.
		// On crée les fichiers sources temporaires pour les graphiques
		
		$file = new File ($templateHtmlGraphe); // Pointeur sur le template
		
		if ($file->exists()) {
			$file->copy($fileHtmlGraphe); // Recopie du template dans notre fichier temporaire HTML
			$file->close(); // Fermeture du template
			$file = new File ($fileHtmlGraphe); // Ouverture du fichier temporaire HTML
			$file->replaceText("tableau", $tabDonnees); // Remplacement dans ce fichier du mot "tableau" par les données ad hoc
			$file->replaceText("couleur", $tabCouleurs); // Remplacement dans ce fichier du mot "tableau" par les données ad hoc
			$file->close(); // Fermeture du fichier temporaire HTML qui est prêt pour générer l'image
		}
		else {
			dump("ADRIA : Erreur avec la génération du fichier des statistiques Type de Fonds. Votre PDF ne peut pas être produit. Contactez votre administrateur.");
			return (false);
		}

		// A partir du fichier HTML temporaire, qui contient le javascript pour générer le graphique,
		// on va construire une image. Cela se fait en générant virtuellement la page HTML par
		// un Phantomjs. Le résultat est placé dans le fichier temporaire contenant l'image souhaitée
		$phantomCommand = $phantomBin . " file:///" . $fileHtmlGraphe . " " .  $fileImgGraphe ;
		$output = exec($phantomCommand); 
			
		if ($output == -1) {
			dump("ADRIA : le fantôme a échoué pour " . $grapheTemplate . ". Contactez votre administrateur.");
			return (false);
		}
		
		// Tout s'est bien déroulé. Le fichier d'image est prêt. On renvoie son nom à la méthode appelante.
		return ($nameImgGraphe) ;
	}
	
}
