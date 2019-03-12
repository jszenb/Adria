<?php
/********************************************************************************************/
// Application ADRIA
// FondsController.php
// Contrôleur pour la classe Fonds
//
// Campus Condorcet (2016)
/********************************************************************************************/
namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\Time;
use Cake\Datasource\ConnectionManager;
use Cake\View\View;
use Cake\Mailer\Email;

/**
 * Fonds Controller
 *
 * @property \App\Model\Table\FondsTable $Fonds
 */
class FondsController extends AppController
{
	// Paramètres par défaut pour la méthode paginate()
	public $paginate = [
		'Fonds' =>  [
			'limit' => 30,
			'sortWhitelist' => [
				'Etablissements.code',
				'EntiteDocs.code',
				'TypeFonds.type',
				'nom', 
				'nb_ml',
				'nb_go'
			]
		]
	];
	
    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
		// On récupère les données par utilisateur : d'abord celles du user en cours, ensuite celle des autres users.
		$user = $this->Auth->user();
		
		// Récupération du paramètre indiquant la liste de fonds que l'on souhaite voir :
		if ($this->request->query('type') != "") {
			$type = $this->request->query('type');	
		}
		else{
			// Par défaut, pour tous les users, sauf le CA, on voit tous les fonds. Pour le CA, on voit ses fonds par défaut
			if ($user['type_user_id'] != PROFIL_CA) {
				$type = "nuser";
			}
			else {
				$type = "user";
			}
		}
		
		// Si ce paramètre est nul, par défaut on affiche les données de l'utilisateur en cours
		if ($type == "") {
			$type = "user";
		}
		
		// Récupération du paramètre indiquant la liste de fonds que l'on souhaite voir :
		if ($this->request->query('sort') != "") {
			$sort = $this->request->query('sort');	
		}
		else {
			$sort = "";
		}		
		
		// Récupération du paramètre indiquant la liste de fonds que l'on souhaite voir :
		if ($this->request->query('limite') != "") {
			$this->paginate =['Fonds'=>['limit' => $this->request->query('limite')]];	
		}
		else {
			// Par défaut, on affiche 30 fonds
			$this->paginate =['Fonds'=>['limit' => 30]];
		}			
		
		$queryFondsId = $this->Fonds->find();
		$queryFondsId->select(['Fondsid' => 'Fonds.Id']);		
        
		// Traitons donc d'abord les utilisateurs qui sont chargés d'archives
		if ($user['type_user_id'] == PROFIL_CA) {
			// Construction de la requête des listes de fonds, selon le type souhaité :
			if ($type == "user") {
				// On veut voir la liste des fonds de l'utilisateur en cours, c-a-d de son entité documentaire
				$query = $this->Fonds->find('all', [
					'conditions' => ['entite_doc_id ' => $user['entite_doc_id'],
									 'ind_suppr <> ' => '1'
						],
					'contain' => [ 'EntiteDocs' => ['Etablissements'], 'TypeFonds']
				]);
				$queryFondsId->where(['entite_doc_id ' => $user['entite_doc_id'], 'ind_suppr <> ' => '1']);
			}
			else {
				// Sinon on récupère tous les fonds
				$query = $this->Fonds->find('all', [
					'conditions' => [
									//'entite_doc_id <>' => $user['entite_doc_id'],
									'ind_suppr <> ' => '1'
						],
					'contain' => [ 'EntiteDocs'  => ['Etablissements'], 'TypeFonds']
					]);	
				$queryFondsId->where(['ind_suppr <> ' => '1']);					
			}
		}
		else {
			// Pour l'utilisateur CO : tous les fonds, sans distinction d'utilisateur			
			// Pour l'utilisateur CC : tous les fonds, sans distinction d'utilisateur, mais avec distinction entre fonds supprimés et non supprimés
			$query = $this->Fonds->find('all', [
					'contain' => [ 'EntiteDocs'  => ['Etablissements'], 'TypeFonds']			
					]);	
					
			if ($type == "supprime") {
				$query->where(['ind_suppr ' => '1']);
				$queryFondsId->where(['ind_suppr ' => '1']);
			}
			else {
				$query->where(['ind_suppr <> ' => '1']);
				$queryFondsId->where(['ind_suppr <> ' => '1']);
			}
		}
		
		// Définition d'un tri par défaut si le paginateur dans l'URL n'a pas été sollicité
		// Ne pas le placer dans la variable $paginate pour éviter de l'imposer systématiquement
		if ( empty($sort) ) {
			//$query->order(["TypeFonds.num_seq" => "ASC", "Fonds.nom" => "ASC"]);
			$query->order(["Fonds.nom" => "ASC"]);
		}
		
		$this->set('fonds', $this->paginate($query));	
		$this->set('sumMl', $this->Fonds->find()->select(['somme' => $query->func()->sum('nb_ml')])->where(['id IN' => $queryFondsId]));
		$this->set('sumGo', $this->Fonds->find()->select(['somme' => $query->func()->sum('nb_go')])->where(['id IN' => $queryFondsId]));

		$this->set('_serialize', ['fonds']);
		
		// Retour du type d'affichage pour la disposition de l'écran :
		$this->set('typeAffichage', $type);

		// Retour de la volumétrie totale :
		$this->set('volumetrie', $this->volumetrieTotale());
        
        // Renvoi des raisons de suppression
        $this->set('raisonSuppressions',$this->Fonds->RaisonSuppressions->find('all', ['limit' => 200, 'order' => ['id' => 'ASC']]));
    }

    /**
     * View method
     *
     * @param string|null $id Fond id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
		
        $fond = $this->Fonds->get($id, [
            'contain' => [
				'EntiteDocs', 
				'TypeFonds', 
				'TypeTraitements', 
				'TypeNumerisations', 
				'TypeInstrRechs', 
				'TypeStatJurids', 
				'TypeEntrees', 
				'TypeAccroissements',
				'TypePriseEnCharges',
				'TypeRealisationTraitements',
				'RaisonSuppressions', 
				'Adresses',
				'TypeDocAfferents' => function($q) {return $q->order(['TypeDocAfferents.type' => 'ASC']);}, 
				'AireCulturelles' => function($q) {return $q->order(['AireCulturelles.intitule' => 'ASC']);}, 
				'LieuConservations' => function($q) {return $q->order(['LieuConservations.nom' => 'ASC']);}, 
				'Thematiques' => function($q) {return $q->order(['Thematiques.intitule' => 'ASC']);}, 
				'TypeConditionnements' => function($q) {return $q->order(['TypeConditionnements.type' => 'ASC']);}, 
				'TypeDocs' => function($q) {return $q->order(['TypeDocs.type' => 'ASC']);}, 
				'TypeSupports' => function($q) {return $q->order(['TypeSupports.type' => 'ASC']);}
			]
        ]);
        $this->set('fond', $fond);
        $this->set('_serialize', ['fond']);
    }

	
    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $erreur_detectee = false;
        $volumetrie = array();
		
        // Crée à partir d'une chaîne datetime.
        $time = Time::now('Europe/Paris')->i18nFormat('YYYY-MM-dd HH:mm:ss');		
        $message = '';

        // Données nécessaires sur l'utilisateur pour savoir qui est l'utilisateur (son profil, son entité documentaire)
        $monUser = $this->request->session()->read('Auth');

        // Routine de création
        $fond = $this->Fonds->newEntity();
        if ($this->request->is('post')) {
			
            // Récupération des données saisies par l'utilisateur
            $fond = $this->Fonds->patchEntity($fond, $this->request->data);
			
            // S'il y a un type de document lié à un support écrit, il faut un métrage linéaire > 0
            // S'il y a un type de document lié à un support numérique, il faut un nombre de giga-octet > 0
            $volumetrie = $this->metrageLineaire($fond, $fond['nb_ml'], $fond['nb_go'], $fond['ind_nb_ml_inconnu'], $fond['ind_nb_go_inconnu'] );
			
            if (!$erreur_detectee) {
				
               // Si les volumétries sont inconnues, on force leurs mesures à zéro
               $fond['ind_nb_ml_inconnu'] ? $fond['nb_ml'] = 0 : null ;
               $fond['ind_nb_go_inconnu'] ? $fond['nb_go'] = 0 : null ;
			
               // Si les dates extrêmes sont inconnues, on force leurs valeurs à zéro
               $fond['ind_annee'] ? $fond['annee_deb'] = '' : null ;
               $fond['ind_annee'] ? $fond['annee_fin'] = '' : null ;			
				
               // Remplissons encore quelques champs : date de dernière modification
               $fond['dt_creation'] = $time;	
				
               // Si le marché de traitement est sans prise en charge, toutes les valeurs sont nulles
               if ($fond['type_prise_en_charge_id'] == NON_PRISE_EN_CHARGE){
                  $fond['site_intervention'] = '';
                  $fond['responsable_operation'] = '';
                  $fond['dt_deb_prestation'] = '';
                  $fond['dt_fin_prestation'] = '';
               }

               if ($this->Fonds->save($fond)) {
                  // Envoi d'un mail pour prévenir l'administrateur que le fonds a été ajouté
                  // Attention : laisser les "" sinon le parseur PHP ne comprend pas bien le \n
                  $message = "Bonjour,\n\nUn nouveau fonds a été ajouté dans Adria." ;
                  $message .= "\n\nUtilisateur : " . $monUser['User']['nom'] . ' ' . $monUser['User']['prenom'] ;
                  $message .= "\nLogin utilisateur : " . $monUser['User']['login'] ;
                  $message .= "\nCourriel utilisateur : " . $monUser['User']['mail'];
                  $message .= "\nNom du fonds ajouté : " . $fond['nom'] ;
                  $message .= "\nEntité documentaire du fonds ajouté : ". $this->Fonds->EntiteDocs->get($fond['entite_doc_id'])['nom']  ;
                  $message .= "\nIdentifiant du fonds ajouté : " . $fond['id'] ;
                  $message .= "\nDate de l'ajout : " . $fond['dt_creation'] ;					
                  $message .= "\n\n-----------------";
                  $message .= "\n\nCeci est un message automatique envoyé par Adria.";
					
                  $email = new Email('default');
                  $email->to(MAIL_ADMIN)
                        ->subject('[Administrateur Adria] : ajout de nouveau fonds')
                        ->send( $message );

                  // On indique à l'utilisateur que le fonds est créé et on le renvoie sur la 
                  // page de consultation du fonds.
                  $this->Flash->success(__('Fonds ajouté.'));

                  return $this->redirect(['action' => 'view/'.$fond['id']]);
               } 
               else {
                  $this->Flash->error(__('Le fonds n\'a pas pu être créé.'));
               }
            }
        }
		
        // Affichage des données nécessaires à la page de création : 
		
        $monEntiteDoc = $monUser['User']['entite_doc_id'];
        $monTypeUser = $monUser['User']['type_user_id'];
	
        // Le profil CC peut créer les données comme il le souhaite
        // Le profil CA peut créer des données uniquement pour son entité documentaire et ses lieux de conservation
        if ($monTypeUser == PROFIL_CC) {
            $entiteDocs = $this->Fonds->EntiteDocs->find('list', ['limit' => 200]);
            $lieuConservations = $this->Fonds->LieuConservations->find('list', ['limit' => 200, 'order' => ['nom' => 'ASC']]);
        }
        else {
            $entiteDocs = $this->Fonds->EntiteDocs->find('list')
                               ->where(['EntiteDocs.id' => $monEntiteDoc]);
			
            // Limitation des lieux de conservation à ceux dépendant de l'entité documentaire en question
            $lieuConservations = $this->Fonds->EntiteDocs->LieuConservations->find('list', ['limit' => 200, 'order' => ['LieuConservations.nom' => 'ASC']]);
            $lieuConservations->matching('EntiteDocs', function ($q) use ($monEntiteDoc)  {
                                                                         return $q->where(['EntiteDocs.id' => $monEntiteDoc]);
                                                       });
        }
		
        // Récupération des données de références
        $typeFonds = $this->Fonds->TypeFonds->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);
        $typeTraitements = $this->Fonds->TypeTraitements->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);
        $typeNumerisations = $this->Fonds->TypeNumerisations->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);
        $typeInstrRechs = $this->Fonds->TypeInstrRechs->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);
        $typeStatJurids = $this->Fonds->TypeStatJurids->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);
        $typeEntrees = $this->Fonds->TypeEntrees->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);
        $typeDocAfferents = $this->Fonds->TypeDocAfferents->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);
        $typeAccroissements = $this->Fonds->TypeAccroissements->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);		
        $typePriseEnCharges = $this->Fonds->TypePriseEnCharges->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);
        $priseEnChargeNon = $this->Fonds->TypePriseEnCharges->find('all', ['where' => ['type' => PAS_PRISE_EN_CHARGE]])->first();
        $typeRealisationTraitements = $this->Fonds->TypeRealisationTraitements->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);		
        $realisationTraitementAucun = $this->Fonds->TypeRealisationTraitements->find('all', ['where' => ['type' => AUCUN_TRAITEMENT_REALISE]])->first();	
        $raisonSuppressions = $this->Fonds->RaisonSuppressions->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);
        $aireCulturelles = $this->Fonds->AireCulturelles->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);
        $thematiques = $this->Fonds->Thematiques->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);
        $typeConditionnements = $this->Fonds->TypeConditionnements->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);
        $typeDocs = $this->Fonds->TypeDocs->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);		
        $typeSupports = $this->Fonds->TypeSupports->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);			
	
        $this->set(compact('fond', 
                           'entiteDocs', 
                           'typeFonds', 
                           'typeTraitements', 
                           'aideTraitements', 
                           'typeNumerisations', 
                           'typeInstrRechs', 
                           'typeStatJurids', 
                           'typeEntrees', 
                           'typeAccroissements', 
                           'typePriseEnCharges', 
                           'priseEnChargeNon', 
                           'typeRealisationTraitements',
                           'realisationTraitementAucun', 
                           'raisonSuppressions',
                           'typeDocAfferents', 
                           'aireCulturelles',
                           'lieuConservations',
                           'thematiques', 
                           'typeConditionnements', 
                           'typeDocs', 
                           'typeSupports'));
        $this->set('_serialize', ['fond']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Fond id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
	$erreur_detectee = false;
	$volumetrie = array();
	// Crée à partir d'une chaîne datetime.
	$time = Time::now('Europe/Paris')->i18nFormat('YYYY-MM-dd HH:mm:ss');
		
	$fond = $this->Fonds->get($id, [ 'contain' => ['TypeDocAfferents', 'AireCulturelles', 'LieuConservations', 'Thematiques', 'TypeConditionnements', 'TypeDocs', 'TypeSupports', 'Adresses'] ]);
		
	if ($this->request->is(['patch', 'post', 'put'])) {

		//dump($this->request->data);
		
		$fond = $this->Fonds->patchEntity($fond, $this->request->data);
			
		//dump($fond);
				
		if ($fond->errors()) {
			$erreur_detectee = true;
			$this->Flash->error(__('Le fonds n\'a pas pu être modifié.'));
		}
	
		if (!$erreur_detectee) {
				
			// Si les volumétries sont inconnues, on force leurs mesures à zéro
			$fond['ind_nb_ml_inconnu'] ? $fond['nb_ml'] = 0 : null ;
			$fond['ind_nb_go_inconnu'] ? $fond['nb_go'] = 0 : null ;
			
			// Si les dates extrêmes sont inconnues, on force leurs valeurs à zéro
			$fond['ind_annee'] ? $fond['annee_deb'] = '' : null ;
			$fond['ind_annee'] ? $fond['annee_fin'] = '' : null ;					
			
			// Remplissons encore quelques champs : date de dernière modification
			$fond['dt_der_modif'] = $time;
	
			// Si le marché de traitement est sans prise en charge, toutes les valeurs sont nulles
       			if ($fond['type_prise_en_charge_id'] == NON_PRISE_EN_CHARGE){
				$fond['site_intervention'] = '';
				$fond['responsable_operation'] = '';
				$fond['dt_deb_prestation'] = '';
				$fond['dt_fin_prestation'] = '';
			}
	
			if ($this->Fonds->save($fond)) {
				$this->Flash->success(__('Fonds modifié.'));
	
				return $this->redirect(['action' => 'view/'.$fond['id']]);
			} else {
				$this->Flash->error(__('Le fonds n\'a pas pu être modifié.'));
			}
		}
       	}
	// Affichage des données nécessaires à la page de création : 
		
	// Données nécessaires sur l'utilisateur pour savoir qui est l'utilisateur (son profil, son entité documentaire)
	$monUser = $this->request->session()->read('Auth');
		
	$monEntiteDoc = $monUser['User']['entite_doc_id'];
		
	$monTypeUser = $monUser['User']['type_user_id'];
	
	// Le profil CC peut créer les données comme il le souhaite
	// Le profil CA peut créer des données uniquement pour son entité documentaire et ses lieux de conservation
	if ($monTypeUser == PROFIL_CC) {
		$entiteDocs = $this->Fonds->EntiteDocs->find('list', ['limit' => 200]);
		$lieuConservations = $this->Fonds->LieuConservations->find('list', ['limit' => 200, 'order' => ['nom' => 'ASC']]);
	}
	else {
		$entiteDocs = $this->Fonds->EntiteDocs->find('list')
			->where(['EntiteDocs.id' => $monEntiteDoc]);
		
		// Limitation des lieux de conservation à ceux dépendant de l'entité documentaire en question
		$lieuConservations = $this->Fonds->EntiteDocs->LieuConservations->find('list', ['limit' => 200, 'order' => ['LieuConservations.nom' => 'ASC']]);
		$lieuConservations->matching('EntiteDocs', function ($q) use ($monEntiteDoc)  {
			return $q->where(['EntiteDocs.id' => $monEntiteDoc]);
		});
	}
		
	// Récupération des données de références
	$typeFonds = $this->Fonds->TypeFonds->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);
	$typeTraitements = $this->Fonds->TypeTraitements->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);
        $typeNumerisations = $this->Fonds->TypeNumerisations->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);
        $typeInstrRechs = $this->Fonds->TypeInstrRechs->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);
        $typeStatJurids = $this->Fonds->TypeStatJurids->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);
        $typeEntrees = $this->Fonds->TypeEntrees->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);
        $typeDocAfferents = $this->Fonds->TypeDocAfferents->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);
        $typeAccroissements = $this->Fonds->TypeAccroissements->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);
	$typePriseEnCharges = $this->Fonds->TypePriseEnCharges->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);
	$priseEnChargeNon = $this->Fonds->TypePriseEnCharges->find('all', ['where' => ['type' => PAS_PRISE_EN_CHARGE]])->first();
	$typeRealisationTraitements = $this->Fonds->TypeRealisationTraitements->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);		
	$realisationTraitementAucun = $this->Fonds->TypeRealisationTraitements->find('all', ['where' => ['type' => AUCUN_TRAITEMENT_REALISE]])->first();		
        $raisonSuppressions = $this->Fonds->RaisonSuppressions->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);
        $aireCulturelles = $this->Fonds->AireCulturelles->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);
        $thematiques = $this->Fonds->Thematiques->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);
        $typeConditionnements = $this->Fonds->TypeConditionnements->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);
        $typeDocs = $this->Fonds->TypeDocs->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);	
        $typeSupports = $this->Fonds->TypeSupports->find('list', ['limit' => 200, 'order' => ['id' => 'ASC']]);			
		
        $this->set(compact(	'fond', 
				'entiteDocs', 
				'typeFonds', 
				'typeTraitements', 
				'typeNumerisations', 
				'typeInstrRechs', 
				'typeStatJurids', 
				'typeEntrees', 
				'typeAccroissements', 
				'typePriseEnCharges', 'priseEnChargeNon', 
				'typeRealisationTraitements', 'realisationTraitementAucun', 
				'raisonSuppressions', 
				'typeDocAfferents', 
				'aireCulturelles', 
				'lieuConservations', 
				'thematiques', 
				'typeConditionnements', 
				'typeDocs', 
				'typeSupports'));
        $this->set('_serialize', ['fond']);
		
    }

    /**
     * Delete method
     * ATTENTION : cette méthode n'est appelée QUE depuis l'écran de liste des fonds (index)
     * Cela explique pourquoi on doit récupérer à la main la raison de la suppression !
     * Notre suppression n'est JAMAIS physique mais toujours logique : c'est ici un UPDATE
     * en base de données en fait (on pose un indicateur à vrai)     .
     *
     * @param string|null $id Fond id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
		$time = Time::now('Europe/Paris')->i18nFormat('YYYY-MM-dd HH:mm:ss');
		
		// Notre suppression est logique !
		// C'est donc une modification où l'on pose l'indicateur de suppression à vrai
		//$this->request->allowMethod(['post', 'delete']);       
		$fond = $this->Fonds->get($id, [
            'contain' => ['TypeDocAfferents', 'AireCulturelles', 'LieuConservations', 'Thematiques', 'TypeConditionnements', 'TypeDocs']
        ]);		
		
		$fond->ind_suppr = true;
		$fond->dt_suppr = $time;

        // On récupère la raison de la suppression
        if (isset($this->request->query['raisonSuppression'])) {
            $fond->raison_suppression_id = $this->request->query["raisonSuppression"] ;
        }
		
		if ($this->Fonds->save($fond)) {
			$this->Flash->success(__('Le fonds a été supprimé.'));
			return $this->redirect(['action' => 'index']);
		} else {
			$this->Flash->error(__('Le fonds n\'a pas pu être supprimé.'));
		}		
		/*
        $this->request->allowMethod(['post', 'delete']);
        $fond = $this->Fonds->get($id);
        if ($this->Fonds->delete($fond)) {
            $this->Flash->success(__('The fond has been deleted.'));
        } else {
            $this->Flash->error(__('The fond could not be deleted. Please, try again.'));
        }
		*/

        return $this->redirect(['action' => 'index']);
		
    }
	
	public function logout() {
		$this->redirect($this->Auth->logout());
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
		*     - dresser la liste des fonds (index)
		*     - consulter tous les fonds (view)
		*     - modifier son propre fonds (edit)
		*     - créer un fonds pour lui-même (add)
		*     - ne peut pas supprimer un fond
		**********************************************************************/

		if (isset($user['type_user_id']) && $user['type_user_id'] == PROFIL_CA) {

			
			if (isset($this->request->params['action'])) {
				$action = $this->request->params['action'];
				
				// Action consultation ou lister les données : 
				// actions possibles sans condition
				if(in_array($action, ['index','view', 'add', 'edit', 'statistiques', 'recherche', 'generatepdf', 'generatecsv', 'generaterapports'])) {

					return true;
					
				}
				else {
					return false;
				}
			}
		}
		// Dans tous les autres cas, le contrôleur App principale prend la décision
		return parent::isAuthorized($user);
	}	
	/**
     * metrageLineaire method
     *
     * @param $unFonds : informations relatives à un fonds
	 *        $unMetrageLineaire : une valeur numérique indiquant une volumetrie en metres linéaires
	 *        $unMetrageGo : une valeur numérique indiquant une volumétrie en gigaoctets
	 *        $volumetrieInconnueMl : booléen vrai si la volumétrie ML est déclarée comme inconnue
	 *        $volumetrieInconnueGo : booléen vrai si la volumétrie Go est déclarée comme inconnue
     * @return tableau avec les clefs suivantes :
	 *         - Clef 'erreur_detectee' : true si une erreur est détectée, false sinon. 
	 *         - Clef 'ind_nb_ml' : true si on a un support physique
	 *         - Clef 'nb_ml' : valeur de la volumétrie en mètre linéaire à conserver.
	 *         - Clef 'ind_nb_go' : trye si on a un support numérique
	 *         - Clef 'nb_go' : valeur de la volumétrie en gigaoctet à conserver.
	 *         On a une erreur si l'on détecte un type de support physique et que le nombre de mètres lineaires est null ou zéro
	 *         On a une erreur si l'on détecte un type de support numérique et que le nombre deGo est null ou zéro  
     * @throws nothing
     */
	public function metrageLineaire ($unFonds, $uneVolumetrieML, $uneVolumetrieGo, $volumetrieInconnueMl, $volumetrieInconnueGo) {
		
		// Booléens nécessaires aux traitements
		$ind_nb_ml = false;
		$ind_nb_go = false;
		
		// Pour pouvoir faire les contrôles de type et de volumétrie, j'ai besoin d'avoir plus d'informations sur les Types de document
		$typeDocsSupport = $this->Fonds->TypeDocs->find('all');
		
		// Retour de la méthode :
		// - par défaut : pas d'erreur
		// - par défaut : les volumétries valent ce que l'on a lu dans le fonds en cours
		$monRetour = array();
		$monRetour['erreur_detectee'] = false;
		$monRetour['ind_nb_ml'] = true;
		$monRetour['nb_ml'] = $uneVolumetrieML;
		$monRetour['ind_nb_go'] = true;
		$monRetour['nb_go'] = $uneVolumetrieGo;
		
		// On examine chaque type de documents associés au fonds unFonds reçu en paramètre :
		// Si ce type de document est associé à un support physique, on vérifie si une volumétrie a été indiquée : la volumétrie est obligatoire. 
		// Idem pour le type numérique.
		foreach ($unFonds->type_docs as $monTypeDoc) {
			
			foreach ($typeDocsSupport as $monTypeDocsSupport) {
					 
				if ($monTypeDocsSupport['id'] == $monTypeDoc['id']) {	

					// Cas du support physique
					if ( $monTypeDocsSupport['ind_physique'] ) {
						
						// Le support physique est détecté : le métrage linéaire devient obligatoire
						$ind_nb_ml = true;

						if ( (!$volumetrieInconnueMl) && ( ($uneVolumetrieML == '') || ($uneVolumetrieML <= 0) ) )  {
							$this->Flash->error(__('Un type de document est de format physique : saisissez une volumétrie non nulle et positive en mètres linéaires.'));
							$monRetour['erreur_detectee'] = true;
							return $monRetour;
							break;							
						}
						
					} 

					// Cas du support numérique	
					if ($monTypeDocsSupport['ind_numerique']) {
						
						// Le support numérique est détecté : le nombre de Go devient obligatoire
						$ind_nb_go = true;
						
						if ( (!$volumetrieInconnueGo) && ( ($uneVolumetrieGo == '') || ($uneVolumetrieGo <= 0) ) ) {
							$this->Flash->error(__('Un type de document est de format numérique : saisissez une volumétrie non nulle et positive en giga-octets.'));	
							$monRetour['erreur_detectee'] = true;
							return $monRetour;						
							break;							
						}
					}						 
				}
			}
		}
		
		// Si aucun support physique ou aucun support numérique n'ont été détectée, 
		// les volumes sont remis à zéro
		if (!$ind_nb_ml) {
			$monRetour['ind_nb_ml'] = false;
			$monRetour['nb_ml'] = 0;
		}
		
		if (!$ind_nb_go) {
			$monRetour['ind_nb_go'] = false;
			$monRetour['nb_go'] = 0;
		}		
			
		return $monRetour;		
	}

	/**
     * Statistiques method
     *
     * @param néant
     * @return des valeurs pour générer les écrans de statistiques
     * @throws nothing
     */    
    public function statistiques() {
		
		$titre = "";
		$abscisse = "";
		$ordonnee = "";
		$ordonnee2 = "";
		$ordonnee3 = "";
		$typeGraphique = "";
		$statDemandee = "";
		$query = array();
		$queryTotaux = array();
		
		if (isset($this->request->data['typestat'])) {
			$statDemandee = $this->request->data['typestat'];
		}
		else {
			// Pas de stat demandée
			$statDemandee = 0;
		}
					
		switch ($statDemandee) {
			case 1 :
				// Volumétrie totale des fonds (exprimée en mètres linéaires) par entité documentaire
				$titre = "Volumétrie totale des fonds exprimée en mètres linéaires par entité documentaire";
				$abscisse = "Entité documentaire";
				$ordonnee = "Volumétrie totale en ml";
				$typeGraphique = "colonne";
				$query = $this->stat_VolFondsMlEntDoc();
				break;	
				
			case 2 :
				// Volumétrie totale des fonds (exprimée en mètres linéaires) par établissement
				$titre = "Volumétrie totale des fonds exprimée en mètres linéaires par établissement";
				$abscisse = "Etablissement";
				$ordonnee = "Volumétrie totale en ml";
				$typeGraphique = "colonne";
				$query = $this->stat_VolFondsMlEtablissement();
				break;		
				
			case 3 :
				// Volumétrie totale des fonds (exprimée en Go) par entité documentaire
				$titre = "Volumétrie totale des fonds exprimée en giga-octets par entité documentaire";
				$abscisse = "Entité documentaire";
				$ordonnee = "Volumétrie totale en Go";
				$typeGraphique = "colonne";
				$query = $this->stat_VolFondsGoEntDoc();
				break;	
				
			case 4 :
				// Volumétrie totale des fonds (exprimée en Go) par établissement
				$titre = "Volumétrie totale des fonds exprimée en giga-octets par établissement";
				$abscisse = "Etablissement";
				$ordonnee = "Volumétrie totale en Go";
				$typeGraphique = "colonne";
				$query = $this->stat_VolFondsGoEtablissement();
				break;	
				
			case 5 :
				// Nombre total de fonds par entité documentaire
				$titre = "Nombre total de fonds par entité documentaire";
				$abscisse = "Entité documentaire";
				$ordonnee = "Nombre total de fonds";
				$typeGraphique = "colonne";
				$query = $this->stat_NbTotFondsEntDoc();
				break;	
				
			case 6 :
				// Nombre total de fonds par établissements
				$titre = "Nombre total de fonds par établissement";
				$abscisse = "Etablissement";
				$ordonnee = "Nombre total de fonds";
				$typeGraphique = "colonne";
				$query = $this->stat_NbTotFondsEtablissement();
				break;	

				case 7 :
				// Nombre total de fonds et volumétrie totale des fonds (en ml) par entité documentaire
				$titre = "Nombre total de fonds et volumétrie totale des fonds (en ml) par entité documentaire";
				$abscisse = "Entité documentaire";
				$ordonnee = "Nombre total de fonds";
				$ordonnee2 = "Volumétrie totale en ml";
				$typeGraphique = "multiple";
				$query = $this->stat_NbTotFondsVolMlEntDoc();
				break;
				
			case 8 :
				// Comparatif des volumétries par établissement
				$titre = "Comparatif des volumétries par établissement";
				$abscisse = "Etablissement";
				$ordonnee = "Nombre total de fonds";				
				$ordonnee2 = "Volumétrie totale des fonds en ml";
				// Désactivation de l'affichage des volumétries en gigaoctets suite à anomalie n°17
				//$ordonnee3 = "Volumétrie totale des fonds en Go";
				$this->set('ordonnee3', $ordonnee3); // je fais le set ici pour des besoins d'interprétation dans la vue
				$typeGraphique = "multiple";
				$query = $this->stat_ComparatifVolEtab();
				break;					

			case 9:
				// Répartition des fonds par type de fonds (nb de fonds)
				$titre = "Répartition des fonds par type de fonds (% établis d'après le nombre de fonds)";
				$abscisse = "Type de fonds";
				$ordonnee = "Nombre de fonds";
				$typeGraphique = "camembert";
				$query = $this->stat_RepartitionFondsParTypeFonds($this->request->data['entitedoc'], 'nb');
				break;		

			case 10:
				// Répartition des fonds par type de document
				$titre = "Répartition des fonds par type de documents (% établis d'après le nombre de fonds)";
				$abscisse = "Type de documents";
				$ordonnee = "Nombre de fonds";
				$typeGraphique = "camembert";
				$query = $this->stat_RepartitionFondsParTypeDoc($this->request->data['entitedoc']);
				break;					
				
			case 11:
				// Répartition des fonds par type de support
				$titre = "Répartition des fonds par types de supports (% établis d'après le nombre de fonds)";
				$abscisse = "Type de supports";
				$ordonnee = "Nombre de fonds";
				$typeGraphique = "camembert";
				$query = $this->stat_RepartitionFondsParTypeSupport($this->request->data['entitedoc']);
				break;	
				
			case 12 :
				// Composition par type de traitement
				$titre = "Répartition des fonds par état de traitement (% établis d\'après le nombre de fonds et la volumétrie ml)";
				$abscisse = "Etat de traitement";
				$ordonnee = "Nombre total de fonds (%)";
				$ordonnee2 = "Volumétrie totale en ml (%)";
				$typeGraphique = "multipleCas9";
				$query = $this->stat_NbTotFondsTraitement($this->request->data['entitedoc']);
				$queryTotaux = $this->stat_InfosFonds();
				$this->set('totaux', $queryTotaux);
				break;	
				
			case 13:
				// Répartition des fonds par statut juridique
				$titre = "Répartition des fonds par statut juridique (% établis d'après le nombre de fonds)";
				$abscisse = "Statut juridique";
				$ordonnee = "Nombre de fonds";
				$typeGraphique = "camembert";
				$query = $this->stat_RepartitionFondsParStatutJuridique($this->request->data['entitedoc']);
				break;	
				
			case 14:
				// Répartition des fonds par mode d'entrée
				$titre = "Répartition des fonds par mode d\'entrée (% établis d'après le nombre de fonds)";
				$abscisse = "Mode d'entrée";
				$ordonnee = "Nombre de fonds";
				$typeGraphique = "camembert";
				$query = $this->stat_RepartitionFondsParModeDentree($this->request->data['entitedoc']);
				break;	

			case 15:
				// Répartition des fonds par dates extrêmes
				$titre = "Couverture chronologique des fonds (d'après les dates extrêmes)";
				$abscisse = "Nom du fonds";
				$ordonnee = "Dates";
				$typeGraphique = "timeline";	
				$query = $this->stat_RepartitionFondsParDatesExtremes($this->request->data['entitedoc']);
				break;	

			case 16:
				// Répartition des fonds par thématiques
				$titre = "Répartition des fonds par disciplines";
				$abscisse = "Disciplines";
				$ordonnee = "Nombre de fonds";
				$typeGraphique = "camembert";
				$query = $this->stat_RepartitionFondsParThematiques($this->request->data['entitedoc']);
				break;					

			case 17:
				// Répartition des fonds par aires culturelles
				$titre = "Répartition des fonds par aires culturelles";
				$abscisse = "Aires culturelles";
				$ordonnee = "Nombre de fonds";
				$typeGraphique = "camembert";
				$query = $this->stat_RepartitionFondsParAiresCulturelles($this->request->data['entitedoc']);
				break;					

			case 20:
				$titre = "Répartition des aires culturelles par discipline (d\'après le nombre de fonds)";
				$abscisse = "Disciplines";
				$ordonnee = "Aires culturelles";
				$ordonnee2 = "Nombre de fonds";
				$typeGraphique = "stacked";
				
				// Attention : $query contient ici non pas une requête mais un tableau de données !!
				//$query = $this->stat_RepartitionAiresCulturellesParThematiques();
				//$res = $this->stat_RepartitionAiresCulturellesParThematiques();
				$res = $this->stat_VolumetrieCroisee('thematiques', 'nb');
				$query = $res[0];
				$this->set('tab_couleurs', $res[1]);
				
				break;					
								
			case 21:
				$titre = "Répartition des disciplines par aire culturelle (d\'après le nombre de fonds)";
				$abscisse = "Aires culturelles";
				$ordonnee = "Disciplines";
				$ordonnee2 = "Nombre de fonds";
				$typeGraphique = "stacked";
				
				// Attention : $query contient ici non pas une requête mais un tableau de données !!
				//$query = $this->stat_RepartitionThematiquesParAiresCulturelles();
				//$res = $this->stat_RepartitionThematiquesParAiresCulturelles();
				$res = $this->stat_VolumetrieCroisee('aires', 'nb');
				$query = $res[0];
				$this->set('tab_couleurs', $res[1]);				
				break;				

			case 18:
				// Volumétries ml par thématiques réparties par aires culturelles
				$titre = "Répartition des aires culturelles par discipline (d\'après la volumétrie ml)";
				$abscisse = "Disciplines";
				$ordonnee = "Aires culturelles";
				$ordonnee2 = "Volumétrie";
				$typeGraphique = "stacked";
				
				// Attention : $query contient ici non pas une requête mais un tableau de données !!
				//$query = $this->stat_VolumetrieCroisee('thematiques', 'ml');
				$res = $this->stat_VolumetrieCroisee('thematiques', 'ml');
				$query = $res[0];
				$this->set('tab_couleurs', $res[1]);				
				break;	

			case 19:
				// Volumétries ml par aires culturelles réparties par thématiques
				$titre = "Répartition des disciplines par aire culturelle (d\'après la volumétrie ml)";
				$abscisse = "Aires culturelles";
				$ordonnee = "Disciplines";
				$ordonnee2 = "Volumétrie";
				$typeGraphique = "stacked";
				//$typeGraphique = "sankey";
				
				// Attention : $query contient ici non pas une requête mais un tableau de données !!
				
				//$res = $this->stat_VolumetrieCroiseeSankey('aires', 'ml');
				$res = $this->stat_VolumetrieCroisee('aires', 'ml');
				$query = $res[0];
				$this->set('tab_couleurs', $res[1]);					
				break;	
				
			case 22:
				// Répartition des fonds par type de fonds (volumétrie ml)
				$titre = "Répartition des fonds par type de fonds (% établis d'après la volumétrie ml)";
				$abscisse = "Type de fonds";
				$ordonnee = "Volumétrie ml";
				$typeGraphique = "camembert";
				$query = $this->stat_RepartitionFondsParTypeFonds($this->request->data['entitedoc'], 'ml');
				break;					
				
			case 23 :
				// Composition par type de traitement
				$titre = "Fonds traités par le marché de traitement";
				$abscisse = "Fonds proposés au marché de traitement";
				$ordonnee = "Nombre total de fonds";
				$ordonnee2 = "Volumétrie totale en ml";
				//$typeGraphique = "multipleCas9";
				$typeGraphique = "multiple";
				$query = $this->stat_NbTotFondsPriseEnCharges($this->request->data['entitedoc']);
				$queryTotaux = $this->stat_InfosFonds();
				$this->set('totaux', $queryTotaux);
				break;	

                        case 24 :
                                // Composition par type de traitement
                                $titre = "Prestation réalisée dans le cadre du marché de traitement";
                                $abscisse = "Prestation réalisée";
                                $ordonnee = "Nombre total de fonds";
                                $ordonnee2 = "Volumétrie totale en ml";
                                //$typeGraphique = "multipleCas9";
                                $typeGraphique = "multiple";
                                $query = $this->stat_NbTotFondsTraitementEnvisageRealise($this->request->data['entitedoc']);
                                $queryTotaux = $this->stat_InfosFonds();
                                $this->set('totaux', $queryTotaux);
                                break;
				
			default:
				$titre = '';
				$abscisse = '';
				$ordonnee = '';
				$ordonnee2 = '';
				$ordonnee3 = '';
				$typeGraphique = '';
				break;
				
		}
		
		// Pour les besoins des dates extrêmes :
		$entiteDocs=$this->Fonds->EntiteDocs->find('list', ['limit' => 200]);
		$this->set('entiteDocs',$entiteDocs);
		
		if (isset($this->request->data['entitedoc'])) {
			$this->set('entiteDocDates',$this->request->data['entitedoc']);
		}
		else {
			$this->set('entiteDocDates','');
		}

		
		$this->set('query', $query);
		$this->set('titre', $titre);
		$this->set('abscisse', $abscisse);
		$this->set('ordonnee', $ordonnee);
		$this->set('ordonnee2', $ordonnee2);
		$this->set('typeGraphique', $typeGraphique);		
		
	}
	/**
     * stat_RepartitionFondsParTypeFonds method
     * Cette méthode calcule le nombre de fonds par type de fonds
     * @param $myEntiteDoc : id de l'entité pour laquelle faire le calcul (si vaut 'all' : toutes)
	 *        $mode : type de calcul à rendre : 'nb' : on compte le nombre de fonds (valeur par défaut) ; 'ml' : on somme les volumétries ml
     * @return $maQuery qui contient la requête adéquate 
     * @throws nothing
     */    
    public function stat_RepartitionFondsParTypeFonds($myEntiteDoc, $mode = 'nb') {
		
		$maQuery = $this->Fonds->find('all', ['contain' => ['TypeFonds']]);
		
		if ($mode == 'nb') {
			$maQuery->select(['count' => $maQuery->func()->count('Fonds.id')]);
		}
		else {
			$maQuery->select(['count' => $maQuery->func()->sum('Fonds.nb_ml')]);
		}
		
		// Autres paramètres de la requête : 
		$maQuery->select(['libelle' => 'TypeFonds.type',
					'couleur' => 'TypeFonds.couleur'
					])		
				->where(['ind_suppr <> ' => 1])				
				->group('type_fond_id')
				->order(['TypeFonds.type' => 'ASC']);		
		
		// Ajout de la condition sur les entités documentaires (anomalie 21)
		if ($myEntiteDoc != 'all') {
			// On limite à l'entité documentaire donnée en paramètre :
			$maQuery->where(['entite_doc_id' => $myEntiteDoc]);
		}
		return($maQuery);
    }

	/**
     * stat_VolFondsMlEntDoc method
     * Cette méthode calcule la volumétrie en mètres linéaires des fonds par entité documentaire
     * @param néant
     * @return $maQuery qui contient la requête adéquate 
     * @throws nothing
     */    
    public function stat_VolFondsMlEntDoc() {
		$maQuery = $this->Fonds->find('all', ['contain' => ['EntiteDocs']]);
		$maQuery->select([
				'count' => $maQuery->func()->sum('Fonds.nb_ml'),
				'libelle' => 'EntiteDocs.code'
				])
				->where(['ind_suppr <> ' => 1])
				->group('entite_doc_id')
				->order(['EntiteDocs.code' => 'ASC']);		
		return($maQuery);
    }	

	/**
     * stat_VolFondsMlEtablissement method
     * Cette méthode calcule la volumétrie en mètres linéaires des fonds par établissement
     * @param néant
     * @return $maQuery qui contient la requête adéquate 
     * @throws nothing
     */    
    public function stat_VolFondsMlEtablissement() {
		$conn = ConnectionManager::get('default');
		$maQuery = $conn->execute("select sum(fonds.nb_ml) as count, etablissements.code as libelle 
									from fonds, entite_docs, etablissements 
									where fonds.entite_doc_id = entite_docs.id and entite_docs.etablissement_id = etablissements.id and fonds.ind_suppr <> 1 
									group by etablissements.id
									order by etablissements.code");
		return($maQuery);
    }	

	/**
     * stat_VolFondsGoEtablissement method
     * Cette méthode calcule la volumétrie en giga-octets des fonds par établissement
     * @param néant
     * @return $maQuery qui contient la requête adéquate 
     * @throws nothing
     */    
    public function stat_VolFondsGoEtablissement() {
		$conn = ConnectionManager::get('default');
		$maQuery = $conn->execute("select sum(fonds.nb_go) as count, etablissements.code as libelle 
									from fonds, entite_docs, etablissements 
									where fonds.entite_doc_id = entite_docs.id and entite_docs.etablissement_id = etablissements.id and fonds.ind_suppr <> 1
									group by etablissements.id
									order by etablissements.code");
		return($maQuery);
    }	
		
	/**
     * stat_VolFondsGoEntDoc method
     * Cette méthode calcule la volumétrie en giga-octets des fonds par entité documentaire
     * @param néant
     * @return $maQuery qui contient la requête adéquate 
     * @throws nothing
     */    
    public function stat_VolFondsGoEntDoc() {
		$maQuery = $this->Fonds->find('all', ['contain' => ['EntiteDocs']]);
		$maQuery->select([
				'count' => $maQuery->func()->sum('Fonds.nb_go'),
				'libelle' => 'EntiteDocs.code'
				])
				->where(['ind_suppr <> ' => 1])				
				->group('entite_doc_id')
				->order(['EntiteDocs.code' => 'ASC']);		
		return($maQuery);
    }	
	/**
     * stat_NbTotFondsEntDoc method
     * Cette méthode calcule le nombre de fonds par entité documentaire
     * @param néant
     * @return $maQuery qui contient la requête adéquate 
     * @throws nothing
     */    
    public function stat_NbTotFondsEntDoc() {
		$maQuery = $this->Fonds->find('all', ['contain' => ['EntiteDocs']]);
		$maQuery->select([
				'count' => $maQuery->func()->count('Fonds.id'),
				'libelle' => 'EntiteDocs.code'
				])
				->where(['ind_suppr <> ' => 1])				
				->group('entite_doc_id')
				->order(['EntiteDocs.code' => 'ASC']);		
		return($maQuery);
    }	
	
	/**
     * stat_ComparatifVolEtab method
     * Cette méthode calcule la volumétrie des fonds en ml et en Go par établissement afin d'en afficher un comparatif
     * @param néant
     * @return $maQuery qui contient la requête adéquate 
     * @throws nothing
     */    
    public function stat_ComparatifVolEtab() {
		$conn = ConnectionManager::get('default');
		$maQuery = $conn->execute("select count(fonds.id) as count, etablissements.code as libelle, sum(fonds.nb_ml) as somme, sum(fonds.nb_go) as somme2
									from fonds, entite_docs, etablissements 
									where fonds.entite_doc_id = entite_docs.id and entite_docs.etablissement_id = etablissements.id and fonds.ind_suppr <> 1
									group by etablissements.id
									order by etablissements.code");									
		return($maQuery);
    }	
	/**
     * stat_NbTotFondsVolMlEntDoc method
     * Cette méthode calcule le nombre de fonds et leur volumétrie par entité documentaire
     * @param néant
     * @return $maQuery qui contient la requête adéquate 
     * @throws nothing
     */    
    public function stat_NbTotFondsVolMlEntDoc() {
		$maQuery = $this->Fonds->find('all', ['contain' => ['EntiteDocs']]);
		$maQuery->select([
				'count' => $maQuery->func()->count('Fonds.id'),
				'somme' => $maQuery->func()->sum('Fonds.nb_ml'),
				'libelle' => 'EntiteDocs.code'
				])
				->where(['ind_suppr <> ' => 1])				
				->group('entite_doc_id')
				->order(['EntiteDocs.code' => 'ASC']);		
		return($maQuery);
    }
	
	/**
     * stat_NbTotFondsTraitement method
     * Cette méthode calcule le nombre de fonds et leur volumétrie par type de traitement
     * @param néant
     * @return $maQuery qui contient la requête adéquate 
     * @throws nothing
     */    
    public function stat_NbTotFondsTraitement($myEntiteDoc) {
		$maQuery = $this->Fonds->find('all', ['contain' => ['TypeTraitements']]);
		$maQuery->select([
				'count' => $maQuery->func()->count('Fonds.id'),
				'somme' => $maQuery->func()->sum('Fonds.nb_ml'),
				'libelle' => 'TypeTraitements.type'
				])
				->where(['ind_suppr <> ' => 1])				
				->group('type_traitement_id')
				->order(['TypeTraitements.type' => 'ASC']);		
				
		// Cas où le graphique doit être générée pour une entité documentaire précise
		if ($myEntiteDoc != 'all') {
			$maQuery->where(['entite_doc_id' => $myEntiteDoc]);
		}				
				
		return($maQuery);
    }	
     /**
     * stat_NbTotFondsPriseEnCharges method
     * Cette méthode calcule le nombre de fonds et leur volumétrie par type de prise en charge
     * @param néant
     * @return $maQuery qui contient la requête adéquate
     * @throws nothing
     */
    public function stat_NbTotFondsPriseEnCharges($myEntiteDoc) {
	$maQuery = $this->Fonds->find('all', ['contain' => ['TypePriseEnCharges']]);
	$maQuery->select([
                                'count' => $maQuery->func()->count('Fonds.id'),
                                'somme' => $maQuery->func()->sum('Fonds.nb_ml'),
                                'libelle' => 'TypePriseEnCharges.type'
                                ])
                                ->where(['ind_suppr <> ' => 1,
                                         'TypePriseEnCharges.type = ' =>  FONDS_TRAITE ,
                                       ])
                                ->group('type_prise_en_charge_id')
                                ->order(['TypePriseEnCharges.type' => 'ASC']);

                // Cas où le graphique doit être générée pour une entité documentaire précise
                if ($myEntiteDoc != 'all') {
                        $maQuery->where(['entite_doc_id' => $myEntiteDoc]);
                }

                return($maQuery);
    }

     /**
     * stat_NbTotFondsTraitementEnvisageRealise
     * Cette méthode calcule le nombre de fonds et leur volumétrie par type de traitement envisagé/réalisé
     * @param néant
     * @return $maQuery qui contient la requête adéquate
     * @throws nothing
     */
    public function stat_NbTotFondsTraitementEnvisageRealise($myEntiteDoc) {
        $maQuery = $this->Fonds->find('all', ['contain' => ['TypeRealisationTraitements', 'TypePriseEnCharges']]);
        $maQuery->select([
                                'count' => $maQuery->func()->count('Fonds.id'),
                                'somme' => $maQuery->func()->sum('Fonds.nb_ml'),
                                'libelle' => 'TypeRealisationTraitements.type'
                                ])
                                ->where(['ind_suppr <> ' => 1,
                                         'TypeRealisationTraitements.type NOT IN ' =>  [AUCUN_TRAITEMENT_REALISE] ,
                                         'TypePriseEnCharges.type =' =>  FONDS_TRAITE ,
                                       ])
                                ->group('type_realisation_traitement_id')
                                ->order(['TypeRealisationTraitements.type' => 'ASC']);

                // Cas où le graphique doit être générée pour une entité documentaire précise
                if ($myEntiteDoc != 'all') {
                        $maQuery->where(['entite_doc_id' => $myEntiteDoc]);
                }

                return($maQuery);
    }

	
	
	/**
     * stat_NbTotFondsEtablissement method
     * Cette méthode calcule le nombre de fonds par établissement
     * @param néant
     * @return $maQuery qui contient la requête adéquate 
     * @throws nothing
     */    
    public function stat_NbTotFondsEtablissement() {
		$conn = ConnectionManager::get('default');
		$maQuery = $conn->execute("select count(fonds.id) as count, etablissements.code as libelle 
									from fonds, entite_docs, etablissements 
									where fonds.entite_doc_id = entite_docs.id and entite_docs.etablissement_id = etablissements.id and fonds.ind_suppr <> 1
									group by etablissements.id
									order by etablissements.code");
		return($maQuery);
    }	
	
	/**
     * stat_RepartitionFondsParStatutJuridique method
     * Cette méthode calcule le nombre de fonds par statut juridique
     * @param néant
     * @return $maQuery qui contient la requête adéquate 
     * @throws nothing
     */    
    public function stat_RepartitionFondsParStatutJuridique($myEntiteDoc) {
		$maQuery = $this->Fonds->find('all', ['contain' => ['TypeStatJurids']]);
		$maQuery->select([
				'count' => $maQuery->func()->count('Fonds.id'),
				'libelle' => 'TypeStatJurids.type', 
				'couleur' => 'TypeStatJurids.couleur'
				])
				->where(['ind_suppr <> ' => 1])				
				->group('type_stat_jurid_id')
				->order(['TypeStatJurids.type' => 'ASC']);	

		// Cas où le graphique doit être générée pour une entité documentaire précise
		if ($myEntiteDoc != 'all') {
			$maQuery->where(['entite_doc_id' => $myEntiteDoc]);
		}
				
		return($maQuery);
    }		

	/**
     * stat_RepartitionFondsParTypeSupport method
     * Cette méthode calcule le nombre de fonds par support
     * @param néant
     * @return $maQuery qui contient la requête adéquate 
     * @throws nothing
     */    
    public function stat_RepartitionFondsParTypeSupport($myEntiteDoc) {
		$conn = ConnectionManager::get('default');
		
		$requete = "select count(fonds.id) as count, type_supports.type as libelle, type_supports.couleur as couleur
									from fonds, fonds_type_supports, type_supports
									where fonds.id = fonds_type_supports.fond_id
										and fonds_type_supports.type_support_id = type_supports.id and fonds.ind_suppr <> 1 ";
		
		// Cas où le graphique doit être générée pour une entité documentaire précise
		if ($myEntiteDoc != 'all') {
			$requete .= " and fonds.entite_doc_id = " . $myEntiteDoc . " ";
		}
		
		$requete .= " group by type_supports.type
					order by type_supports.type asc";
					
		$maQuery = $conn->execute($requete);										
		return($maQuery);
    }	

	/**
     * stat_RepartitionFondsParTypeDoc method
     * Cette méthode calcule le nombre de fonds par type de documents
     * @param néant
     * @return $maQuery qui contient la requête adéquate 
     * @throws nothing
     */    
    public function stat_RepartitionFondsParTypeDoc($myEntiteDoc) {
		$conn = ConnectionManager::get('default');
		$requete = "select	count(fonds.id) as count, type_docs.type as libelle, type_docs.couleur as couleur
					from	fonds, fonds_type_docs, type_docs
					where	fonds.id = fonds_type_docs.fond_id
							and fonds_type_docs.type_doc_id = type_docs.id and fonds.ind_suppr <> 1" ;
		
		// Cas où le graphique doit être générée pour une entité documentaire précise
		if ($myEntiteDoc != 'all') {
			$requete .= " and fonds.entite_doc_id = " . $myEntiteDoc . " ";
		}
		
		$requete .= " group by type_docs.type
					order by type_docs.type asc";
					
		$maQuery = $conn->execute($requete);
		
		return($maQuery);
    }	
	/**
     * stat_RepartitionFondsParModeDentree method
     * Cette méthode calcule le nombre de fonds par mode d'entrée
     * @param néant
     * @return $maQuery qui contient la requête adéquate 
     * @throws nothing
     */    
    public function stat_RepartitionFondsParModeDentree($myEntiteDoc) {
		$maQuery = $this->Fonds->find('all', ['contain' => ['TypeEntrees']]);
		$maQuery->select([
				'count' => $maQuery->func()->sum('Fonds.nb_ml'),
				'libelle' => 'TypeEntrees.type',
				'couleur' => 'TypeEntrees.couleur'
				])
				->where(['ind_suppr <> ' => 1])				
				->group('type_entree_id')
				->order(['TypeEntrees.type' => 'ASC']);		
				
		// Cas où le graphique doit être générée pour une entité documentaire précise
		if ($myEntiteDoc != 'all') {
			$maQuery->where(['entite_doc_id' => $myEntiteDoc]);
		}
		
		return($maQuery);
    }
	
	/**
     * stat_RepartitionFondsParThematiques method
     * Cette méthode calcule le nombre de fonds par thématiques
     * @param néant
     * @return $maQuery qui contient la requête adéquate 
     * @throws nothing
     */    
    public function stat_RepartitionFondsParThematiques($myEntiteDoc) {
		$maQuery = $this->Fonds->find('all');
		$maQuery->select([
				'count' => $maQuery->func()->count('Fonds.id'),
				'libelle' => 'Thematiques.intitule',
				'couleur' => 'Thematiques.couleur'				
				])
				->matching('Thematiques')
				->where(['ind_suppr <> ' => 1])	
				//->where(['intitule !=' => 'A renseigner']) 				
				->group('Thematiques.id')
				->order(['Thematiques.intitule' => 'ASC']);		

		// Cas où le graphique doit être générée pour une entité documentaire précise
		if ($myEntiteDoc != 'all') {
			$maQuery->where(['entite_doc_id ' => $myEntiteDoc]);
		}				
				
		return($maQuery);
    }	

	/**
     * stat_RepartitionFondsParAiresCulturelles method
     * Cette méthode calcule le nombre de fonds par aires culturelles
     * @param néant
     * @return $maQuery qui contient la requête adéquate 
     * @throws nothing
     */    
    public function stat_RepartitionFondsParAiresCulturelles($myEntiteDoc) {
		$maQuery = $this->Fonds->find('all');
		$maQuery->select([
				'count' => $maQuery->func()->count('Fonds.id'),
				'libelle' => 'AireCulturelles.intitule',
				'couleur' => 'AireCulturelles.couleur'
				])
				->matching('AireCulturelles')
				->where(['ind_suppr <> ' => 1])	
				//->where(['intitule !=' => 'A renseigner']) 				
				->group('AireCulturelles.id')
				->order(['AireCulturelles.intitule' => 'ASC']);	
				
		// Cas où le graphique doit être générée pour une entité documentaire précise
		if ($myEntiteDoc != 'all') {
			$maQuery->where(['entite_doc_id ' => $myEntiteDoc]);
		}						
				
		return($maQuery);
    }
	
	 /**
     * stat_RepartitionFondsParDatesExtremes method
     * Cette méthode renvoie pour une entité documentaire données les dates extrêmes de chaque fonds
     * @param néant
     * @return $maQuery qui contient la requête adéquate 
     * @throws nothing
     */    
    public function stat_RepartitionFondsParDatesExtremes($myEntiteDoc) {
		$maQuery = $this->Fonds->find('all', ['contain' => 'EntiteDocs']);
		
		$maQuery->order([
					'Fonds.annee_deb' => 'ASC',
					'Fonds.annee_fin' => 'ASC',
					'Fonds.nom' => 'ASC'
				]);	
				
		if ($myEntiteDoc != 'all') {
				$maQuery
					->select([
						'nom' => 'Fonds.nom',
						'debut' => 'Fonds.annee_deb',
						'fin' => 'Fonds.annee_fin'
					])	
					->where([
						['ind_suppr <> ' => 1],
						['entite_doc_id' => $myEntiteDoc ],
						['(annee_deb is not null or annee_fin is not null)']
					]);			
		}
		else {
				$maQuery
					->select([
						'nom' => 'CONCAT(Fonds.nom, " - ", EntiteDocs.code)',
						'debut' => 'Fonds.annee_deb',
						'fin' => 'Fonds.annee_fin'
					])	
					->where([
						['ind_suppr <> ' => 1],
						['(annee_deb is not null or annee_fin is not null)'],
						['(annee_deb <> 0 and annee_fin <> 0)']
					]);
		}
		
		return($maQuery);		
	}
	
	/**
     * stat_InfosFonds method
     * Cette méthode calcule le nombre total de fonds et la volumétrie total en ml sans autre condition ou regroupement
     * @param néant
     * @return $maQuery qui contient la requête adéquate et différentes variables pour la page
     * @throws nothing
     */    
	public function stat_InfosFonds() {
		$maQuery = $this->Fonds->find('all');
		$maQuery->select([
				'totalFonds' => $maQuery->func()->count('Fonds.id'),
				'totalML' => $maQuery->func()->sum('Fonds.nb_ml')
				])	
				->where(['ind_suppr <> ' => 1]);
		return($maQuery);		
	}
	
	/**
     * stat_RepartitionAiresCulturellesParThematiques method
     * Cette méthode calcule le nombre total de fonds par aires culturelles et par thématiques
     * @param néant
     * @return $tab_donnees contenant les données pour construire le graphique
     * @throws nothing
     */    
	public function stat_RepartitionAiresCulturellesParThematiques() {
		// Il faut construire un tableau de données contenant en colonne les aires, en ligne les thematiques
		// Dans chaque case, on met le nombre de thematiques par aires.
		// On va donc faire une requête dynamique qui pour chaque thématique et pour chaque aire, compte les informations
		$aireCulturelles = $this->Fonds->AireCulturelles->find('all', ['order' => ['id' => 'ASC']]);
		$thematiques = $this->Fonds->Thematiques->find('all', ['order' => ['id' => 'ASC']]);
		
		$conn = ConnectionManager::get('default');		
		
		$s_maQuery = "select count(fonds.id) as nombre
					  from   fonds, fonds_thematiques, aire_culturelles_fonds
					  where  fonds.id = fonds_thematiques.fond_id
					     and fonds.id = aire_culturelles_fonds.fond_id
						 and fonds_thematiques.thematique_id = :thematique_id  
						 and aire_culturelles_fonds.aire_culturelle_id = :aire_id 
						 and fonds.ind_suppr <> 1";
		$tab_donnees = "[['Thématiques'";
		$tab_couleurs = "[";
		$lg = $aireCulturelles->count();
		$i = 0;
		
		// constitution de la première ligne du tableau qui sera la légende du graphe :
		foreach ($aireCulturelles as $aire) {
			
			$tab_donnees .=  ",'" . addslashes($aire['intitule']) . "'";
			if ($i < ($lg - 1 ) ) {
				$tab_couleurs .= "'" . $aire['couleur'] . "',";
			}
			else {
				$tab_couleurs .= "'" . $aire['couleur'] . "'";
			}
			$i++;
		}
		
		$tab_donnees .= "]";
		$tab_couleurs .= "]";
		
		//dump($tab_couleurs);
		
		// Constitution des lignes suivantes : une ligne par thématique 
		foreach ($thematiques as $thematique) {
			$tab_donnees = $tab_donnees . ",['" .  addslashes($thematique['intitule']) . "'";
			foreach ($aireCulturelles as $aire) {
				$maQuery = $conn->prepare($s_maQuery);
				$maQuery->bindValue('thematique_id', $thematique['id'], 'integer');
				$maQuery->bindValue('aire_id', $aire['id'], 'integer');
				$maQuery->execute();
				$res = $maQuery->fetch('assoc');
				$tab_donnees = $tab_donnees .  "," . $res['nombre'] ;
			}
			$tab_donnees .= "]";
		}
		// Fermeture du tableau
		$tab_donnees .= "]";
		
		return ([$tab_donnees, $tab_couleurs]);
		
	}	

	/**
     * stat_RepartitionThematiquesParAiresCulturelles method
     * Cette méthode calcule le nombre total de fonds par thématiques et par aires culturelles
     * @param néant
     * @return $tab_donnees contenant les données pour construire le graphique
     * @throws nothing
     */    
	public function stat_RepartitionThematiquesParAiresCulturelles() {
		// Il faut construire un tableau de données contenant en colonne les aires, en ligne les thematiques
		// Dans chaque case, on met le nombre de thematiques par aires.
		// On va donc faire une requête dynamique qui pour chaque thématique et pour chaque aire, compte les informations
		$aireCulturelles = $this->Fonds->AireCulturelles->find('all', ['order' => ['id' => 'ASC']]);
		$thematiques = $this->Fonds->Thematiques->find('all', ['order' => ['id' => 'ASC']]);
		
		$conn = ConnectionManager::get('default');		
		
		
		$s_maQuery = "select count(fonds.id) as nombre
					  from   fonds, fonds_thematiques, aire_culturelles_fonds
					  where  fonds.id = fonds_thematiques.fond_id
					     and fonds.id = aire_culturelles_fonds.fond_id
						 and fonds_thematiques.thematique_id = :thematique_id  
						 and aire_culturelles_fonds.aire_culturelle_id = :aire_id 
						 and fonds.ind_suppr <> 1";
		$tab_donnees = "[['Aires culturelles'";
		$tab_couleurs = "[";
		$lg = $thematiques->count();
		$i = 0;
		
		// constitution de la première ligne du tableau qui sera la légende du graphe :
		foreach ($thematiques as $thematique) {
			
			$tab_donnees .=  ",'" . addslashes($thematique['intitule']) . "'";
			if ($i < ($lg - 1 ) ) {
				$tab_couleurs .= "'" . $thematique['couleur'] . "',";
			}
			else {
				$tab_couleurs .= "'" . $thematique['couleur'] . "'";
			}
			$i++;			
		}
		
		$tab_donnees .= "]";
		$tab_couleurs .= "]";
	
		// Constitution des lignes suivantes : une ligne par thématique 
		foreach ($aireCulturelles as $aire) {
			$tab_donnees = $tab_donnees . ",['" .  addslashes($aire['intitule']) . "'";
			foreach ($thematiques as $thematique) {
				$maQuery = $conn->prepare($s_maQuery);
				$maQuery->bindValue('thematique_id', $thematique['id'], 'integer');
				$maQuery->bindValue('aire_id', $aire['id'], 'integer');
				$maQuery->execute();
				$res = $maQuery->fetch('assoc');
				$tab_donnees = $tab_donnees .  "," . $res['nombre'] ;
			}
			$tab_donnees .= "]";
		}
		// Fermeture du tableau
		$tab_donnees .= "]";
		
		return ([$tab_donnees, $tab_couleurs]);
		
	}	
	
	
	/**
     * stat_VolumetrieCroisee method
     * Cette méthode calcule la volumétrie totale des fonds par thématiques et par aires culturelles
     * @param $type : le mode de construction du tableau 
	 *        $methode : calcul du nombre de fonds ("nb") ou la somme de mètres linéaires ("ml")
     * @return $tab_donnees contenant les données pour construire le graphique
     * @throws nothing
     */    
	public function stat_VolumetrieCroisee($type, $methode) {
		// Il faut construire un tableau de données contenant les volumétries par thématiques et aires.
		// Si le type est "thématiques", cela signifie qu'on veut pour chaque thématique (ligne du tableau), les volumétries (données du tableau) par aires culturelles (colonnes du tableau)
		// Si le type est "aires", cela signifie qu'on veut pour chaque aire (ligne du tableau), les volumétries (données du tableau) par thématiques (colonnes du tableau)
		// On doit donc paramétrer la requête et la constitution du tableau
		$query_thematiques = $this->Fonds->Thematiques->find('all', ['order' => ['id' => 'ASC']]) ; // ->where(['intitule !=' => 'A renseigner']) ;
		$query_aires = $this->Fonds->AireCulturelles->find('all', ['order' => ['id' => 'ASC']]) ; // ->where(['intitule !=' => 'A renseigner']);
		if ($type == 'thematiques') {
			
			// Ligne du tableau : thématique
			$lignes = $query_thematiques ;
			$param1 = 'thematique_id';
			
			// Colonne du tableau : aires
			$colonnes = $query_aires ;
			$param2 = 'aire_id';
			
			$tab_donnees = "[['Thématiques'";	

		}
		else {
			// Ligne du tableau : aire
			$lignes = $query_aires ;
			$param1 = 'aire_id';
			
			// Colonne du tableau : Thematiques
			$colonnes = $query_thematiques ;
			$param2 = 'thematique_id';
			
			$tab_donnees = "[['Aires culturelles'";				
		}
		
		// Requête standard pour les deux cas, c'est la constitution du tableau de données qui fera la différence
		$conn = ConnectionManager::get('default');	
		
		//dump($methode);
		if ($methode == "nb") {		
			$s_maQuery = "select count(fonds.id) as nombre
						from   fonds, fonds_thematiques, aire_culturelles_fonds
						where  fonds.id = fonds_thematiques.fond_id
						 and fonds.id = aire_culturelles_fonds.fond_id
						 and fonds_thematiques.thematique_id = :thematique_id  
						 and aire_culturelles_fonds.aire_culturelle_id = :aire_id 
						 and fonds.ind_suppr <> 1";
		}
		else {
			$s_maQuery = "select ifnull(sum(round(fonds.nb_ml, 2)), 0) as nombre
						from   fonds, fonds_thematiques, aire_culturelles_fonds
						where  fonds.id = fonds_thematiques.fond_id
						 and fonds.id = aire_culturelles_fonds.fond_id
						 and fonds_thematiques.thematique_id = :thematique_id  
						 and aire_culturelles_fonds.aire_culturelle_id = :aire_id 
						 and fonds.ind_suppr <> 1";			
		}

		$tab_couleurs = "[";
		$lg = $colonnes->count();
		$i = 0;
						 
		// constitution de la première ligne du tableau qui sera la légende du graphe :
		foreach ($colonnes as $colonne) {
			
			$tab_donnees .=  ",'" . addslashes($colonne['intitule']) . "'";
			if ($i < ($lg - 1 ) ) {
				$tab_couleurs .= "'" . $colonne['couleur'] . "',";
			}
			else {
				$tab_couleurs .= "'" . $colonne['couleur'] . "'";
			}
			$i++;				
		}
		
		$tab_donnees .= "]";
		$tab_couleurs .= "]";		
		
		// Constitution des lignes suivantes : le paramétrage ligne / colonne permet de différencier
		// les deux types de graphiques possibles
		foreach ($lignes as $ligne) {
			$tab_donnees = $tab_donnees . ",['" .  addslashes($ligne['intitule']) . "'";
			foreach ($colonnes as $colonne) {
				$maQuery = $conn->prepare($s_maQuery);
				$maQuery->bindValue($param1, $ligne['id'], 'integer');
				$maQuery->bindValue($param2, $colonne['id'], 'integer');
				$maQuery->execute();
				$res = $maQuery->fetch('assoc');
				$tab_donnees = $tab_donnees .  "," . $res['nombre'] ;
			}
			$tab_donnees .= "]";
		}
		// Fermeture du tableau
		$tab_donnees .= "]";
		
		return ([$tab_donnees, $tab_couleurs]);
	}		
	/**
     * stat_VolumetrieCroiseeSankey method
     * Cette méthode calcule la volumétrie totale des fonds par thématiques et par aires culturelles
	 * La différence avec la méthode stat_VolumetrieCroisee est dans le tableau de sortie
     * @param $type : le mode de construction du tableau 
	 *        $methode : calcul du nombre de fonds ("nb") ou la somme de mètres linéaires ("ml")
     * @return $tab_donnees contenant les données pour construire le graphique
     * @throws nothing
     */    
	public function stat_VolumetrieCroiseeSankey ($type, $methode) {
		
		if ($type == 'thematiques') {
			
			$query = $this->Fonds->Thematiques->find('all', ['order' => ['intitule' => 'ASC']])->where(['intitule !=' => 'A renseigner']); //->where(['intitule !=' => 'Sans objet']) ;
		
		}
		else {
			$query = $this->Fonds->AireCulturelles->find('all', ['order' => ['intitule' => 'ASC']])->where(['intitule !=' => 'A renseigner']) ; //->where(['intitule !=' => 'Sans objet']);
		}
		
		$lg = $query->count();
		$i = 0;
		$tab_couleurs = "[";
		foreach($query as $row) {
			if ($i < ($lg - 1 ) ) {
				$tab_couleurs .= "'" . $row['couleur'] . "',";
			}
			else {
				$tab_couleurs .= "'" . $row['couleur'] . "'";
			}
			$i++;
		}
		$tab_couleurs .= "]";			
		
		// Requête standard pour les deux cas, c'est la constitution du tableau de données qui fera la différence
		$conn = ConnectionManager::get('default');	
		
		//dump($methode);
		
		if ($methode == "ml") {
			$s_maQuery = "select thematiques.intitule as thematique, aire_culturelles.intitule as aire, count(fonds.id) as nombre
						from   fonds, fonds_thematiques, aire_culturelles_fonds, thematiques, aire_culturelles
						where  fonds.id = fonds_thematiques.fond_id
						 and fonds.id = aire_culturelles_fonds.fond_id
						 and aire_culturelles_fonds.aire_culturelle_id = aire_culturelles.id 
						 and fonds_thematiques.thematique_id = thematiques.id 
						 and fonds.ind_suppr <> 1
						 and thematiques.intitule not in ('A renseigner', 'sans objet')
						 and aire_culturelles.intitule not in ('A renseigner', 'sans objet')
						group by thematiques.intitule, aire_culturelles.intitule";
		}
		else {
			$s_maQuery = "select ifnull(sum(round(fonds.nb_ml, 2)), 0) as nombre
						from   fonds, fonds_thematiques, aire_culturelles_fonds
						where  fonds.id = fonds_thematiques.fond_id
						 and fonds.id = aire_culturelles_fonds.fond_id
						 and fonds_thematiques.thematique_id = :thematique_id  
						 and aire_culturelles_fonds.aire_culturelle_id = :aire_id 
						 and fonds.ind_suppr <> 1";
		}


		
		// Constitution des lignes suivantes : le paramétrage ligne / colonne permet de différencier
		// les deux types de graphiques possibles
		$i = 0;
		$j = 0;
		
		$maQuery = $conn->execute($s_maQuery);
		$count = count($maQuery);
		$i = 0;
		
		$tab_donnees = "[";		
		
		foreach ($maQuery as $ligne) {
			$tab_donnees = $tab_donnees . "['" .  addslashes($ligne['thematique']) . "',";
			$tab_donnees = $tab_donnees .  "'" . addslashes($ligne['aire']).  "'," . $ligne['nombre'] . "]" ;

			if ($i < ( $count - 1) ){
				$tab_donnees .= ",";
			}
		}

		// Fermeture du tableau
		$tab_donnees .= "]";
		
		return ([$tab_donnees, $tab_couleurs]);
	}		


    /**
     * Recherche method
     * Cette méthode effectue une recherche sur les fonds selon les critères renvoyés
     * dans la query string
     * @return void
     */
    public function recherche()
    {
		$operande = "";
		$critere = "";
		$valeur = "";
		$dateDeb = "";
		$dateFin = "";
		$perimetre = "";
		$limite = "";
		//$tri = "";
		$clauseWhere = "";
		$query = "";
		$querySumMl = "";
		$queryFondsId = "";
		
		//dump($this->request);
		$monUser = $this->request->session()->read('Auth');
		$monEntiteDoc = $monUser['User']['entite_doc_id'];
		
		// 22/04/2016 : Anomalie n°4 : on passe les données en GET dans tous les cas (donc dans l'URL) pour 
		// éviter le message de resoumission du formulaire lors d'un "back" du navigateur :
		// Si la pagination a été demandé, on aura changepage == '1' dans l'URL, sinon c'est qu'on est passé par le formulaire en POST
		//if ( $this->request->query('changepage') == '1' ) {
			$critere = $this->request->query('critere');
			$operande = $this->request->query('operande');
			$valeur = $this->request->query('valeur');
			$dateDeb = $this->request->query('dateDeb');
			$dateFin = $this->request->query('dateFin');
			$perimetre = $this->request->query('perimetre');
			$limite = $this->request->query('limite');

			if ($limite == "") {
				// Limite permet de gérer le nombre de résultat par page : par défaut, 30
				$limite = 30;
			}
			//$tri = $this->request->query('tri');
			$hi_valeur = $this->request->query('hi_valeur');			
		/*}
		else {
			/*$critere = $this->request->data('critere');
			$operande = $this->request->data('operande');
			$valeur = $this->request->data('valeur');
			$dateDeb = $this->request->data('dateDeb');
			$dateFin = $this->request->data('dateFin');			
			$perimetre = $this->request->data('perimetre');
			//$tri = $this->request->data('tri');
			$hi_valeur = $this->request->data('hi_valeur');
		}*/
		
		//dump($critere);
		//dump($operande);
		//dump($hi_valeur);

		// Début d'écriture de la requête de recherche
		
		// Pour l'affichage, il nous faut seulement le fonds, l'entité documentaire, l'établissement et le type de fonds
		$query = $this->Fonds->find('all', [
			'contain' => [ 
				'EntiteDocs' => ['Etablissements'], 
				'TypeFonds'
				]
			]);
		// Cette deuxième requête sera nécessaire pour faire des sommes de volumétrie 
		$queryFondsId = $this->Fonds->find('all', [
			'contain' => [ 
				'EntiteDocs' => ['Etablissements'], 
				'TypeFonds'
				]
			]);
		$queryFondsId->select(['Fondsid' => 'Fonds.Id']);
				
		// On écrit la condition de recherche
		switch ($critere) {
			case 1 :
				// Nom du fonds
				$clauseWhere = "Fonds.nom";
				break;
			case 2 :
				// Entité documentaire
				$clauseWhere = "EntiteDocs.code";
				break;			
			case 3 :
				// Etablissement
				// Pour ce cas, je dois ruser en faisant une sous-requête qui me ramène les entités doc de l'établissement demandé dans le critère
				$clauseWhere = "EntiteDocs.etablissement_id IN (SELECT etablissements.id FROM etablissements WHERE etablissements.code";
				break;		
			case 4 :
				// Lieu de conservation
				// Pour ce cas, je dois ruser en faisant une sous-requête qui me ramène les lieux de conservation demandé dans le critère
				$clauseWhere = "FondsLieuConservations.lieu_conservation_id IN (SELECT lieu_conservations.id FROM lieu_conservations WHERE lieu_conservations.nom";
				
				// Ce qui est ci-dessous n'est pas très joli mais je n'arrive pas à faire passer dedans ma condition... et j'ai besoin de cette ligne pour faire apparaître la jointure
				$query->innerJoinWith('LieuConservations', function ($q) {
						return $q->where(['1 = 1']);
					});
				$queryFondsId->innerJoinWith('LieuConservations', function ($q) {
						return $q->where(['1 = 1']);
					});					
				break;
			case 5 :
				// Producteur
				$clauseWhere = "Fonds.producteur";
				break;	
			case 6 :
				// Type de statuts juridiques
				$clauseWhere = "Fonds.type_stat_jurid_id";
				break;		
			case 7 :
				// Type de mode d'entrée
				$clauseWhere = "Fonds.type_entree_id";
				break;	
			case 8 :
				// Documents afférents
				// Pour ce cas, je dois ruser en faisant une sous-requête qui me ramène les documents afférents demandés dans le critère
				$clauseWhere = "FondsTypeDocAfferents.type_doc_afferent_id IN (SELECT type_doc_afferents.id FROM type_doc_afferents WHERE type_doc_afferents.id";
				
				// Ce qui est ci-dessous n'est pas très joli mais je n'arrive pas à faire passer dedans ma condition... et j'ai besoin de cette ligne pour faire apparaître la jointure
				$query->innerJoinWith('TypeDocAfferents', function ($q) {
						return $q->where(['1 = 1']);
					});
				$queryFondsId->innerJoinWith('TypeDocAfferents', function ($q) {
						return $q->where(['1 = 1']);
					});					
				break;				
			case 9 :
				// Type de fonds
				$clauseWhere = "Fonds.type_fond_id";
				break;		
			case 10 :
				// Couplé à une collection d'imprimé
				$clauseWhere = "Fonds.ind_bib";
				break;					
			case 11 :
				// Type de documents
				// Pour ce cas, je dois ruser en faisant une sous-requête qui me ramène les type de documents demandés dans le critère
				$clauseWhere = "FondsTypeDocs.type_doc_id IN (SELECT type_docs.id FROM type_docs WHERE type_docs.id ";
				
				// Ce qui est ci-dessous n'est pas très joli mais je n'arrive pas à faire passer dedans ma condition... et j'ai besoin de cette ligne pour faire apparaître la jointure
				$query->innerJoinWith('TypeDocs', function ($q) {
						return $q->where(['1 = 1']);
					});
				$queryFondsId->innerJoinWith('TypeDocs', function ($q) {
						return $q->where(['1 = 1']);
					});					
				break;	
			case 12 :
				// Type de support : 
				// Pour ce cas, je dois ruser en faisant une sous-requête qui me ramène les type de support demandés dans le critère
				$clauseWhere = "FondsTypeSupports.type_support_id IN (SELECT type_supports.id FROM type_supports WHERE type_supports.id ";
				
				// Ce qui est ci-dessous n'est pas très joli mais je n'arrive pas à faire passer dedans ma condition... et j'ai besoin de cette ligne pour faire apparaître la jointure
				$query->innerJoinWith('TypeSupports', function ($q) {
						return $q->where(['1 = 1']);
					});		
				$queryFondsId->innerJoinWith('TypeSupports', function ($q) {
						return $q->where(['1 = 1']);
					});						
				break;
			case 13 :
				// Support numérique
				$clauseWhere = "Fonds.ind_nb_go";
				break;		
			case 14 :
				// Support physique
				$clauseWhere = "Fonds.ind_nb_ml";
				break;					
			case 15 :
				// Type d'accroissement
				$clauseWhere = "Fonds.type_accroissement_id";
				break;	
			case 16 :
				// Type d'instrument de recherche
				$clauseWhere = "Fonds.type_instr_rech_id";
				break;	
			case 17 :
				// Thématiques
				// Pour ce cas, je dois ruser en faisant une sous-requête qui me ramène les thématiques demandées dans le critère
				$clauseWhere = "FondsThematiques.thematique_id ";
				
				// Ce qui est ci-dessous n'est pas très joli mais je n'arrive pas à faire passer dedans ma condition... et j'ai besoin de cette ligne pour faire apparaître la jointure
				$query->innerJoinWith('Thematiques', function ($q) {
						return $q->where(['1 = 1']);
					});
				$queryFondsId->innerJoinWith('Thematiques', function ($q) {
						return $q->where(['1 = 1']);
					});					
				break;			
			case 18 :
				// Aires culturelles
				// Pour ce cas, je dois ruser en faisant une sous-requête qui me ramène les aires culturelles demandées dans le critère
				$clauseWhere = "AireCulturellesFonds.aire_culturelle_id ";
				
				// Ce qui est ci-dessous n'est pas très joli mais je n'arrive pas à faire passer dedans ma condition... et j'ai besoin de cette ligne pour faire apparaître la jointure
				$query->innerJoinWith('AireCulturelles', function ($q) {
						return $q->where(['1 = 1']);
					});
				$queryFondsId->innerJoinWith('AireCulturelles', function ($q) {
						return $q->where(['1 = 1']);
					});					
				break;		
			case 19 :
				// Type conditionnements
				// Pour ce cas, je dois ruser en faisant une sous-requête qui me ramène les types de conditionnement demandées dans le critère
				$clauseWhere = "FondsTypeConditionnements.type_conditionnement_id ";
				
				// Ce qui est ci-dessous n'est pas très joli mais je n'arrive pas à faire passer dedans ma condition... et j'ai besoin de cette ligne pour faire apparaître la jointure
				$query->innerJoinWith('TypeConditionnements', function ($q) {
						return $q->where(['1 = 1']);
					});
				$queryFondsId->innerJoinWith('TypeConditionnements', function ($q) {
						return $q->where(['1 = 1']);
					});					
				break;	
			case 20 :
				// Type de traitements
				$clauseWhere = "Fonds.type_traitement_id";
				break;		
			case 21 :
				// Type de numérisations
				$clauseWhere = "Fonds.type_numerisation_id";
				break;	
			case 22 :
				// Volumétrie linéaire
				$clauseWhere = "Fonds.nb_ml";
				break;		
			case 23 :
				// Volumétrie Go
				$clauseWhere = "Fonds.nb_go";
				break;			
			case 24 :
				// Cote
				$clauseWhere = "Fonds.cote";
				break;					
			case 25 :
				// date extreme
				$dateFin == '' ? $dateFin = 'NULL' : true ;
				
				$dateDeb == '' ? $dateDeb = 'NULL' : true ;
				
				//$clauseWhere = " ( Fonds.annee_deb <= COALESCE( " . $dateFin . " , 4712) AND Fonds.annee_fin >= COALESCE( " . $dateDeb . " , 1850 ) ) " ;
				$clauseWhere = " ( Fonds.annee_deb >= COALESCE( " . $dateDeb . " , 1700) AND Fonds.annee_fin <= COALESCE( " . $dateFin . " , 4712 ) ) " ;
				
				break;		
			case 26 :
				// URL de l'instrument de recherche
				$clauseWhere = "TRIM(Fonds.url_instr_rech)";
				break;

			case 27 :
				// URL de l'instrument de recherche
				$clauseWhere = "Fonds.dt_der_modif";
				switch ($valeur) {
					case 0 :
						// Oui, le fonds a été modifié quel que soit la date de modification
						$clauseWhere .= " is not null ";
						break;
					case 1 :
						// Oui, le fonds a été modifié dans le mois écoulé
						$clauseWhere .= " is not null and Fonds.dt_der_modif >= DATE_SUB(CURRENT_DATE, interval 1 month) ";
						break;
					case 2 :
						// Oui, le fonds a été modifié dans le trimestre écoulé
						$clauseWhere .= " is not null and Fonds.dt_der_modif >= DATE_SUB(CURRENT_DATE, INTERVAL 3 MONTH) ";					
						break;
					case 3 :
						// Non, le fonds n'a pas été modifié
						$clauseWhere .= " is null ";						
						break;
					default :
				}
				break;
			case 28 :
				// Indicateur "Fiche de fonds modifiée"
				$clauseWhere = "Fonds.ind_maj";
				break;
			case 29 :
				// Indicateur "Prise en charge"
				$clauseWhere = "Fonds.type_prise_en_charge_id";
				break;
			case 30 :
				// Indicateur "Realisation de traitement envisage"
				$clauseWhere = "Fonds.type_realisation_traitement_id";
				break;
			case 31 :
				// Indicateur "Stockage cible"
				$clauseWhere = "Fonds.stockage";
				break;
			case 32 :
				// Indicateur de communicabilité
				$clauseWhere = "Fonds.communication";
			default :
		}
		
		// Etablissement de l'opérande : 
		// - pour "compris entre" (cas 6), c'est réservé aux dates extrêmes et on l'a géré ci-dessus
		// - pour "renseigné" (cas 7), c'est réservé à l'url instrument de recherche
		switch ($operande) {
			case 1 :
				// Egal
				$clauseWhere = $clauseWhere." = '".addslashes($valeur)."'";
				break;				
			case 2 :
				// Commence par
				$clauseWhere = $clauseWhere." LIKE '".addslashes($valeur)."%'";				
				break;		
			case 3 :
				// Différent de
				$clauseWhere = $clauseWhere." != '".addslashes($valeur)."'";				
				break;		
			case 4 :
				// Supérieur ou égal
				$clauseWhere = $clauseWhere." >= '".addslashes($valeur)."'";				
				break;		
			case 5 :
				// Inférieur ou égal
				$clauseWhere = $clauseWhere." <= '".addslashes($valeur)."'";				
				break;		
			case 7 :
				// Renseigné ou pas ?
				if ($valeur == '1') {
					// renseigné 
					$clauseWhere = $clauseWhere . "<> ''";
				}
				else {
					// non renseigné
					$clauseWhere = $clauseWhere . "= ''";
				}		
				break;					
			default :			
		}
		
		
		// Si je travaille sur les établissements, les lieux de conservation, les documents afférents, je dois fermer ma sous-requête		
		if ( ($critere == '3') || ($critere == '4') || ($critere == '8') || ($critere == '11') || ($critere == '12')) {
			$clauseWhere = $clauseWhere.")";
		}
		
		// On ne ramène pas les fonds supprimés
		if (!empty($clauseWhere)) {
			$clauseWhere = $clauseWhere . " and Fonds.ind_suppr <> 1 ";
		}
		else {
			$clauseWhere = $clauseWhere . "  Fonds.ind_suppr <> 1 ";
		}
		
		// On limite éventuellement le périmètre de recherche

		if (!empty($monEntiteDoc)) {
			switch ($perimetre) {
				case 1 :
					// Fonds de l'utilisateur en cours
					$clauseWhere = $clauseWhere . " and Fonds.entite_doc_id = " . $monEntiteDoc;
					break;
				case 2 : 
					// Autres fonds
					$clauseWhere = $clauseWhere . " and Fonds.entite_doc_id <> " . $monEntiteDoc;
					break;
				default :
			}
		}
		//dump($clauseWhere)	;
		// Attribution de la clause where à la requête
		$query->where($clauseWhere);
		$queryFondsId->where($clauseWhere);
		
		if ( empty($this->request->query('sort')) ) {
			$query->order(["Fonds.nom" => "ASC"]);
		}
		
		// J'utilise le group by pour éviter la remonter de plusieurs fois le même fonds lors des jointures avec un critère !=
		// Sinon on a la même valeur qui remonte plusieurs fois
		$query->group(['Fonds.id']);
		$queryFondsId->group(['Fonds.id']);
		
		//dump($query);
		
		// A partir de la requête queryFondsId, je peux construire une autre requête qui fait le calcul des volumétries retournées par la recherche 
		$querySumMl = $this->Fonds->find();
		$querySumMl->select(['somme' => $query->func()->sum('nb_ml')]);
		$querySumMl->where(['id IN' => $queryFondsId]);
		$querySumGo = $this->Fonds->find();
		$querySumGo->select(['somme' => $query->func()->sum('nb_go')]);
		$querySumGo->where(['id IN' => $queryFondsId]);
		
		
		// Pour l'affichage, il nous faut seulement le fonds mais aussi le nombre de résultats
		$count = $query->count();		
		
		// Paramètre de pagination :	
		$this->paginate = [
			'Fonds' =>  [
				'limit' => $limite,
				'sortWhitelist' => [
					'Etablissements.code',
					'EntiteDocs.code',
					'TypeFonds.type',
					'nom', 
					'nb_ml',
					'nb_go'
				]
			]
		];

		if (!empty($critere)) {
			
			//dump( $this->paginate($query));
			$this->set('fonds', $this->paginate($query));	
			$this->set('sumMl', $querySumMl);
			$this->set('sumGo', $querySumGo);			
			$this->set('count', $count);	
			$this->set('volumetrie', $this->volumetrieTotale());
		}
		else{
			$this->set('fonds', null);	
			$this->set('count', null);	
			$this->set('sumMl', null);
			$this->set('sumGo', null);			
			$this->set('volumetrie', null);			
		}
		//$this->set('_serialize', ['fonds']);
		
		// Données nécessaires aux listes de valeur de critères :
		$this->set ('typeFonds',$this->Fonds->TypeFonds->find('all', ['order' => ['type' => 'ASC']]));
		$this->set ('typeStatJurids',$this->Fonds->TypeStatJurids->find('all', ['order' => ['type' => 'ASC']]));
		$this->set ('typeTraitements', $this->Fonds->TypeTraitements->find('all', ['order' => ['type' => 'ASC']]));
		$this->set ('typeNumerisations', $this->Fonds->TypeNumerisations->find('all', ['order' => ['type' => 'ASC']]));
		$this->set ('typeInstrRechs', $this->Fonds->TypeInstrRechs->find('all', ['order' => ['type' => 'ASC']]));
		$this->set ('typeEntrees', $this->Fonds->TypeEntrees->find('all', ['order' => ['type' => 'ASC']]));
		$this->set ('typeDocAfferents', $this->Fonds->TypeDocAfferents->find('all', ['order' => ['type' => 'ASC']]));
		$this->set ('typeAccroissements', $this->Fonds->TypeAccroissements->find('all', ['order' => ['type' => 'ASC']]));
		$this->set ('raisonSuppressions', $this->Fonds->RaisonSuppressions->find('all', ['order' => ['raison' => 'ASC']]));
		$this->set ('aireCulturelles', $this->Fonds->AireCulturelles->find('all', ['order' => ['intitule' => 'ASC']]));
		$this->set ('thematiques', $this->Fonds->Thematiques->find('all', ['order' => ['intitule' => 'ASC']]));
		$this->set ('typeConditionnements', $this->Fonds->TypeConditionnements->find('all', ['order' => ['type' => 'ASC']]));
		$this->set ('typeDocs', $this->Fonds->TypeDocs->find('all', ['order' => ['type' => 'ASC']]));	
		$this->set ('typeSupports', $this->Fonds->TypeSupports->find('all', ['order' => ['type' => 'ASC']]));	
		$this->set ('typePriseEnCharges', $this->Fonds->TypePriseEnCharges->find('all', ['order' => ['type' => 'ASC']]));	
		$this->set ('typeRealisationTraitements', $this->Fonds->TypeRealisationTraitements->find('all', ['order' => ['type' => 'ASC']]));	
		$this->set ('rappelCritere', $critere);
		$this->set ('rappelOperande', $operande);
		$this->set ('rappelValeur', $valeur);
		$this->set ('rappelDateDeb', $dateDeb == 'NULL' ? '' : $dateDeb) ;		
		$this->set ('rappelDateFin', $dateFin == 'NULL' ? '' : $dateFin);		
		$this->set ('rappelPerimetre', $perimetre == '' ? 0 : $perimetre);
		$this->set ('rappelLimite', $limite);		
		$this->set ('rappelHi_valeur', $hi_valeur);		
    }	

    /**
     * volumetrieTotale method
     * Calcul des volumétries totales
     * @return requête de calcul des volumetries
     */	
    public function volumetrieTotale() {
		
		$volumetrie = null;

		$volumetrie = $this->Fonds->find('all', [
			'fields' => [ 
				'sommeMl' => 'SUM(nb_ml)',
				'sommeGo' => 'SUM(nb_go)'],
				'conditions' => ['ind_suppr <> ' => '1'],
		]);

		return $volumetrie;
	}	 
    /**
     * generatepdf method
     * Gestion de la production des rapports sur les fonds
     * @return void
     */	
    public function generatepdf() {
		
		$view = new View();
		$title = "";
		$filename = "";
		$query = "";
		$template = "";
		
		// Etablissement du modèle de génération à utiliser par défaut
		$modele = 'pdf/default';
		
		$mode = $this->request->query('mode');		
		$user = $this->request->session()->read('Auth');
		$monEntiteDoc = $user['User']['entite_doc_id'];
		$monTypeUser = $user['User']['type_user_id'];
		$monIdUser = $user['User']['id'];
		//dump($mode);
		
		// Détermination de l'état à produire :
		switch ($mode) {
			// Liste des fonds : cas "vos fonds" (user), cas "autres fonds" (nuser)
			// C'est l'état accessible par l'écran des listes de fonds
			// Pour le cas "supprime", c'est un sous-cas du profil CC avec tous les fonds
			// --------------------------------------------------------------------
			case "supprime":
			case "user":
			case "nuser":
				///////
				$modele = 'pdf/listefonds';
				$template = 'Fonds/pdf/generatepdf';			
				$view->set('typeUserEnSession', $monTypeUser) ;
				///////
				//$template = 'Fonds/pdf/listefonds';
				if ($mode == "user") {
					$title = "Liste des fonds " ;
					$filename = "ListeVosFonds_".$monIdUser.mt_rand();
				}
				else {
					$title = "Liste des fonds rejoignant le Grand Equipement Documentaire" ;
					$filename = "ListeAutresFonds_".$monIdUser.mt_rand();					
				}
				
				// On construit $query qui va contenir le détail de la condition de notre recherche
				switch ($monTypeUser) {		
					case PROFIL_CO:
						// On gère le profil de consultation comme le CC
					case PROFIL_CC:
						if ($mode != "supprime") {
                        					$query = $this->Fonds->find('all', [
									'contain' => [ 'EntiteDocs' => ['Etablissements'], 'TypeFonds'],
									'order' => ['Fonds.nom' => 'asc'],
									'conditions' => ['ind_suppr != ' => 1]
								]);
                        			}
                        			else {
                            				$title = "Liste des fonds supprimés " ;
                            				$query = $this->Fonds->find('all', [
								'contain' => [ 'EntiteDocs' => ['Etablissements'], 'TypeFonds'],
								'order' => ['Fonds.nom' => 'asc'],
								'conditions' => ['ind_suppr = ' => 1]
                                				]);                            
                        			}
						break;
					case PROFIL_CA:			
						if ($mode == "user") {
							$query = $this->Fonds->find('all', [
									'conditions' => [
										'entite_doc_id ' => $monEntiteDoc,
										'ind_suppr != ' => 1],
									'contain' => [ 'EntiteDocs' => ['Etablissements'], 'TypeFonds'],
									'order' => ['Fonds.nom' => 'asc']
								]);				
						}
						else {
							$query = $this->Fonds->find('all', [
									'conditions' => [
										'ind_suppr != ' => 1],
									'contain' => [ 'EntiteDocs' => ['Etablissements'], 'TypeFonds'],
									'order' => ['Fonds.nom' => 'asc']
								]);						
						}
						break;
					default:
						break;
				}
				
				$view->set('totalMlRecherche', $this->request->query('totalMlRecherche'));
				$view->set('totalMl', $this->request->query('totalMl'));
				$view->set('totalGoRecherche', $this->request->query('totalGoRecherche'));
				$view->set('totalGo', $this->request->query('totalGo'));
				$view->set('totalFonds', $this->request->query('totalFonds'));
				
				$view->set('fonds',$query);
				$view->set('mode',$mode);
				$view->set('_serialize', ['fonds']);
				break;
				
			// Fiche de fonds : c'est l'état accessible depuis la page de
            		// consultation de fonds.
			// --------------------------------------------------------------------			
			case "fiche":
				$modele = 'pdf/fichefonds';
				$template = 'Fonds/pdf/generatepdf';	                
				$filename = "fichefonds".$monIdUser.'-'.mt_rand();
				$title = "Fiche de fonds" ;
				$id = $this->request->query('id');	
				$query = $this->Fonds->get($id, [
						'contain' => ['EntiteDocs', 'TypeFonds', 'TypeTraitements', 'TypeNumerisations', 'TypeInstrRechs', 'TypeStatJurids', 'TypeEntrees', 
								'TypeAccroissements', 'RaisonSuppressions', 'TypeDocAfferents', 'AireCulturelles', 'LieuConservations', 'Thematiques', 
								'TypeConditionnements', 'TypeDocs', 'TypeSupports', 'TypePriseEnCharges', 'TypeRealisationTraitements', 'Adresses',
								'TypePriseEnCharges', 'TypeRealisationTraitements']
					]);		
				$view->set('profil',$monTypeUser);					
				$view->set('fond',$query);
				$view->set('_serialize', ['fond']);
				break;
				
			// Rapport de liste détaillées des fonds
			// --------------------------------------------------------------------				
			case "ListeDetailleeFonds":
				$modele = 'pdf/listedetailleefonds';
				$template = 'Fonds/pdf/generatepdf';
				$title = "Liste détaillées des fonds" ;	
				//$colonneTri1 = $this->request->query('colonneTri1');			
				//$colonneTri2 = $this->request->query('colonneTri2');
				//$ordreTri1 = $this->request->query('ordreTri1');			
				//$ordreTri2 = $this->request->query('ordreTri2');				
				
				$view->set('profil',$monTypeUser);				
				
				switch ($monTypeUser) {		
					case PROFIL_CA:					
						$filename = "ListeDetailleeFonds".$monIdUser.'-'.mt_rand();
						$query = $this->Fonds->find('all', [
										'conditions' => ['ind_suppr != ' => 1, 
												 'entite_doc_id ' => $monEntiteDoc
												],
										'contain' => [ 'EntiteDocs' => ['Etablissements'], 'TypeFonds', 'TypeStatJurids' ],
										'order' => [ 
											 'Etablissements.nom' => 'asc',
											 'TypeFonds.num_seq' => 'asc',
											 'EntiteDocs.nom' => 'asc',
											 'Fonds.nom' => 'asc'
											]
										]);							
						$view->set('fonds',$query);
						$view->set('_serialize', ['fonds']);
						break;
					case PROFIL_CO:
					case PROFIL_CC:	
						$filename = "ListeDetailleeFonds".$monIdUser.'-'.mt_rand();
						$query = $this->Fonds->find('all', [
										'conditions' => ['ind_suppr != ' => 1],
										'contain' => [ 'EntiteDocs' => ['Etablissements'], 'TypeFonds', 'TypeStatJurids'],
										'order' => [	
											'Etablissements.nom' => 'asc',
											'TypeFonds.num_seq' => 'asc',
											'EntiteDocs.nom' => 'asc',
											'Fonds.nom' => 'asc'
											]
										]);							
						$view->set('fonds',$query);
						$view->set('_serialize', ['fonds']);
						break;					
					default:
						break;
				}
				break;
			// Rapport de liste détaillées des fonds
			// --------------------------------------------------------------------				
			case "VolumetrieParLieuxEtablissementsEntites":	
				$modele = 'pdf/volumetrieparlieuxetablissementsentites';
				$template = 'Fonds/pdf/generatepdf';
				$title = "Volumétrie (ml) par lieux de conservation, établissements et entités documentaires" ;
				$view->set('profil',$monTypeUser);	
				$filename = "VolumetrieParLieuxEtablissementsEntites".$monIdUser.'-'.mt_rand();
				$conn = ConnectionManager::get('default');
				$requete = "select LieuConservations.adresse_ville as 'ville', LieuConservations.nom as 'nomLieu', 
					LieuConservations.adresse_1 as 'adresse1', LieuConservations.adresse_2 as 'adresse2', LieuConservations.adresse_3 as 'adresse3', 
					LieuConservations.adresse_cp as 'cp', LieuConservations.adresse_pays  as 'pays',
					Etablissements.code  as 'etablissement',EntiteDocs.code as 'bib', sum(Fonds.nb_ml) as 'volume'
					from fonds Fonds, entite_docs EntiteDocs, etablissements Etablissements, fonds_lieu_conservations flc, lieu_conservations LieuConservations
					where	Fonds.ind_suppr <> 1 and Fonds.entite_doc_id = EntiteDocs.id and Fonds.id = flc.fond_id and LieuConservations.id = flc.lieu_conservation_id and EntiteDocs.etablissement_id = Etablissements.id
					group by LieuConservations.id, Etablissements.id, EntiteDocs.id
					order by cp, ville, adresse1, etablissement, bib" ;
					
				$query = $conn->execute($requete);
	
				$view->set('fonds',$query);
				$view->set('_serialize', ['fonds']);
				break;				
                        // Rapport de liste des fonds par entité documentaire et par lieu de
			// stockage cible
                        // --------------------------------------------------------------------
                        case "ListeFondsParEntiteDocsEtLieuxStockageCible":
                                $modele = 'pdf/listeFondsEntiteStockage';
                                $template = 'Fonds/pdf/generatepdf';
                                $title = "Liste détaillées des fonds par entité documentaire et par lieu de stockage cible" ;

                                $view->set('profil',$monTypeUser);

                                switch ($monTypeUser) {
                                        case PROFIL_CA:
                                                $filename = "ListeFondsEntiteStockage".$monIdUser.'-'.mt_rand();
                                                $query = $this->Fonds->find('all', [
                                                                                'conditions' => ['ind_suppr != ' => 1,
                                                                                                 'entite_doc_id ' => $monEntiteDoc
                                                                                                ],
                                                                                'contain' => [ 'EntiteDocs' => ['Etablissements'] ],
                                                                                'order' => [
                                                                                         'Etablissements.nom' => 'asc',
                                                                                         'EntiteDocs.nom' => 'asc',
											 'Fonds.stockage' => 'asc',
                                                                                         'Fonds.nom' => 'asc'
                                                                                        ]
                                                                                ]);
                                                $view->set('fonds',$query);
                                                $view->set('_serialize', ['fonds']);
                                                break;
                                        case PROFIL_CO:
                                        case PROFIL_CC:
                                                $filename = "ListeFondsEntiteStockage".$monIdUser.'-'.mt_rand();
                                                $query = $this->Fonds->find('all', [
                                                                                'conditions' => ['ind_suppr != ' => 1],
                                                                                'contain' => [ 'EntiteDocs' => ['Etablissements'] ],
                                                                                'order' => [
                                                                                        'Etablissements.nom' => 'asc',
                                                                                        'EntiteDocs.nom' => 'asc',
                                                                                        'Fonds.stockage' => 'asc',
                                                                                        'Fonds.nom' => 'asc'
                                                                                        ]
                                                                                ]);
                                                $view->set('fonds',$query);
                                                $view->set('_serialize', ['fonds']);
                                                break;
                                        default:
                                                break;
                                }
                                break;
                        // Rapport de liste des fonds par entité documentaire et par lieu de
                        // stockage cible HORS OPTION SUR  LE LIEU D'ORIGINE : pour l'AMO
                        // déménagement
                        // --------------------------------------------------------------------
                        case "ListeFondsParEntiteDocsEtLieuxStockageCibleAMO":
                        case "ListeFondsParLieuxStockageCibleEtEntiteDocsAMO":
                                $modele = 'pdf/listeFondsEntiteStockageAMO';
                                $template = 'Fonds/pdf/generatepdf';
                                if ($mode == "ListeFondsParEntiteDocsEtLieuxStockageCibleAMO") {
                                   $title = "Liste détaillées des fonds par entités documentaires et par lieux de stockage cible" ;
                                   $this->set('mode', "ES"); // entite / stockage
                                } else {
                                   $title = "Liste détaillées des fonds par lieux de stockage cible et par entités documentaire" ;
                                   $this->set('mode', "SE"); // stockage / entite
                                }
                                $view->set('profil',$monTypeUser);
                                $filename = "ListeFondsEntiteStockageAMO".$monIdUser.'-'.mt_rand();
                                switch ($monTypeUser) {
                                        case PROFIL_CA:
                                                $query = $this->Fonds->find('all', [
                                                                                'conditions' => ['ind_suppr != ' => 1,
                                                                                                 'stockage != ' => 0, // on exclut le stockage en site d'origine
                                                                                                 'entite_doc_id ' => $monEntiteDoc
                                                                                                ],
                                                                                'contain' => [ 'EntiteDocs' => ['Etablissements'] ]
                                                                                ]);
                                        case PROFIL_CO:
                                        case PROFIL_CC:
                                                $query = $this->Fonds->find('all', [
                                                                                'conditions' => ['ind_suppr != ' => 1,
                                                                                                 'stockage != ' => 0 // on exclut le stockage en site d'origine
                                                                                                ], 
                                                                                'contain' => [ 'EntiteDocs' => ['Etablissements'] ]
                                                                                ]);
                                        default:
                                                break;
                                }

                                if ($mode == "ListeFondsParEntiteDocsEtLieuxStockageCibleAMO") {
                                    $query->order([ 'Etablissements.nom' => 'asc',
                                                    'EntiteDocs.nom' => 'asc',
                                                    'Fonds.stockage' => 'asc',
                                                    'Fonds.nom' => 'asc'
                                                  ]);
                                } else {
                                    $query->order([ 'Etablissements.nom' => 'asc',
                                                    'Fonds.stockage' => 'asc',
                                                    'EntiteDocs.nom' => 'asc',
                                                    'Fonds.nom' => 'asc'
                                                  ]);
                                }
                                $view->set('fonds',$query);
                                $view->set('_serialize', ['fonds']);
                                break;

                        // Rapport détaillée du contenu des magasins
                        // --------------------------------------------------------------------
                        case "ListeMagasin":
                                $modele = 'pdf/listeMagasins';
                                $template = 'Fonds/pdf/generatepdf';
                                $title = "Inventaire du contenu des magasins";

                                $view->set('profil',$monTypeUser);

                                switch ($monTypeUser) {
                                        case PROFIL_CA:
                                                $filename = "ListeMagasins".$monIdUser.'-'.mt_rand();
                                                $query = $this->Fonds->Adresses->find('all', [
                                                                                'conditions' => ['ind_suppr != ' => 1,
                                                                                                 'entite_doc_id ' => $monEntiteDoc,
                                                                                                 'Adresses.magasin <> ' => ''
                                                                                                ],
                                                                                'contain' => ['Fonds' => ['EntiteDocs' => function ($q) {return $q->select(['fondnom' => 'Fonds.nom', 'fondml' => 'Fonds.nb_ml', 'entite' => 'EntiteDocs.code']);}]
                                                                                             ],

                                                                                'order' => [
                                                                                         'Adresses.magasin' => 'asc',
                                                                                         'Adresses.epi_deb' => 'asc',
                                                                                         'Adresses.epi_fin' => 'asc',
                                                                                         'Adresses.travee_deb' => 'asc',
                                                                                         'Adresses.travee_fin' => 'asc',
                                                                                         'Fonds.nom' => 'asc'
                                                                                        ]
                                                                                ]);
                                                $view->set('fonds',$query);
                                                $view->set('_serialize', ['fonds']);
                                                break;
                                        case PROFIL_CO:
                                        case PROFIL_CC:
                                                $filename = "ListeMagasins".$monIdUser.'-'.mt_rand();
                                                $query = $this->Fonds->Adresses->find('all', [
                                                                                'conditions' => ['ind_suppr != ' => 1,
                                                                                                 'Adresses.magasin <> ' => '' 
                                                                                                ],
                                                                                'contain' => ['Fonds' => ['EntiteDocs' => function ($q) {return $q->select(['fondnom' => 'Fonds.nom', 'fondml' => 'Fonds.nb_ml', 'entite' => 'EntiteDocs.code']);}]
                                                                                             ],
                                                                                'order' => [
                                                                                         'Adresses.magasin' => 'asc',
                                                                                         'Adresses.epi_deb' => 'asc',
                                                                                         'Adresses.epi_fin' => 'asc',
                                                                                         'Adresses.travee_deb' => 'asc',
                                                                                         'Adresses.travee_fin' => 'asc',
                                                                                         'Fonds.nom' => 'asc'
                                                                                        ]
                                                                                ]);
                                                $view->set('adresses',$query);
                                                $view->set('_serialize', ['adresses']);
                                                break;
                                        default:
                                                break;
                                }
                                break;

			default:
				break;
		}
		
		// Positionnement des variables communes et retour.
		$this->set('filename',$filename);
		$this->set('title',$title);
		$view->set(compact('title', 'filename'));
		$view->render($template, $modele );
		$this->viewBuilder()->autoLayout(false);
    }	
	
	/**
    * generatecsv method
	* Gestion de la production des rapports sur les fonds
    * @return void
    */	
    public function generatecsv() {
		$monUser = $this->request->session()->read('Auth');
		$monEntiteDoc = $monUser['User']['entite_doc_id'];
		$monTypeUser = $monUser['User']['type_user_id'];
        $mode = $this->request->query('mode');
		
		$fonds = $this->Fonds->find('all', [
            'contain' => [
				'EntiteDocs' => function ($q) {return $q->select(['entnom' => 'EntiteDocs.nom']);},
				'TypeFonds' => function ($q) {return $q->select(['fondnom' => 'TypeFonds.type']);},
				'TypeTraitements' => function ($q) {return $q->select(['traitementnom' => 'TypeTraitements.type']);}, 
				'TypeNumerisations' => function ($q) {return $q->select(['numerisationnom' => 'TypeNumerisations.type']);}, 
				'TypeInstrRechs' => function ($q) {return $q->select(['instrrechnom' => 'TypeInstrRechs.type']);}, 
				'TypeStatJurids' => function ($q) {return $q->select(['statjuridnom' => 'TypeFonds.type']);}, 
				'TypeEntrees' => function ($q) {return $q->select(['entreenom' => 'TypeStatJurids.type']);}, 
				'TypeAccroissements' => function ($q) {return $q->select(['accroissementnom' => 'TypeAccroissements.type']);},
				'TypeDocAfferents' => function ($q) {return $q->select(['docafferentnom' => 'TypeDocAfferents.type']);}, 
				'AireCulturelles' => function ($q) {return $q->select(['aireculturellenom' => 'AireCulturelles.intitule']);}, 
				'LieuConservations' => function ($q) {return $q->select(['lieuconsnom' => 'LieuConservations.nom']);}, 
				'Thematiques' => function ($q) {return $q->select(['thematiquenom' => 'Thematiques.intitule']);}, 
				'TypeConditionnements' => function ($q) {return $q->select(['conditionnementnom' => 'TypeConditionnements.type']);}, 
				'TypeDocs' => function ($q) {return $q->select(['typedocnom' => 'TypeDocs.type']);}, 
				'TypeSupports' => function ($q) {return $q->select(['typesupportnom' => 'TypeSupports.type']);},
				'TypePriseEnCharges' => function ($q) {return $q->select(['priseenchargenom' => 'TypePriseEnCharges.type']);},
				'TypeRealisationTraitements' => function ($q) {return $q->select(['realisationtraitementsnom' => 'TypeRealisationTraitements.type']);}
				]
			]);
		//dump($monTypeUser);
		if ($monTypeUser == PROFIL_CA) {
			$fonds->where([
				'entite_doc_id' => $monEntiteDoc,
				'ind_suppr != ' => 1
				]);
		}
		else {
            if ($mode != 'supprime') {
                $fonds->where([
                    'ind_suppr != ' => 1
                    ]);	
            }
            else {
                $fonds->where([
                    'ind_suppr = ' => 1
                    ]);	                
            }
		}
		
		//dump($fonds);
		
		$_serialize = 'fonds';
		
		$_header = ['Nom', 
					'Année de début',
					'Année de fin',
					'Dates extrêmes à renseigner',		
					'Cote',
					'Producteur', 
					'Historique', 
					'Couplage à une collection d\'imprimé', 
					'URL d\'inventaire de la collection',
					'Précision sur la collection', 
					'Volumétrie mètre-linéaire', 
					'Volumétrie mètre-linéaire inconnue',
					'Volumétrie gigaoctets', 
					'Volumétrie gigaoctets inconnue',
					'Observations', 
					'Entité documentaire', 
					'Type de fonds',
					'Type de traitement', 
					'Type de numérisation', 
					'Type d\'instrument de recherche',
					'URL de l\'instrument de recherche',
					'Type de statut juridique', 
					'Type d\'entrée', 
					'Type d\'accroissement', 
					'Type de document afférent', 
					'Aire culturelle', 
					'Lieu de conservation',
					'Disciplines', 
					'Type de conditionnement', 
					'Type de documents',
					'Type de support',
					'Type de prise en charges',
					'Traitement envisagé / réalisé' ,
					'Site d\'intervention', 
					'Dates envisagées / effectives (début ; MM/JJ/AA)',
					'Dates envisagées / effectives (fin ; MM/JJ/AA)',
					'Responsable d\'opérations'
					];
					
		$_extract = ['nom', 
					'annee_deb',
					'annee_fin',
					['ind_annee', 'boolean', 'A renseigner'],
					'cote', 
					'producteur', 
					'historique', 
					['ind_bib', 'boolean', 'Oui'], 
					'url_collection',
					'precision_bib', 
					'nb_ml', 
					['ind_nb_ml_inconnu', 'boolean', 'Inconnue'],
					'nb_go', 
					['ind_nb_go_inconnu', 'boolean', 'Inconnue'],
					'observations', 
					'entnom', 
					'fondnom', 
					'traitementnom', 
					'numerisationnom', 
					'instrrechnom', 
					'url_instr_rech',
					'statjuridnom', 
					'entreenom', 
					'accroissementnom', 
					['type_doc_afferents', 'array', 'docafferentnom'], 
					['aire_culturelles', 'array', 'aireculturellenom'],  
					['lieu_conservations', 'array', 'lieuconsnom'], 
					['thematiques', 'array', 'thematiquenom'],
					['type_conditionnements', 'array', 'conditionnementnom'],
					['type_docs', 'array', 'typedocnom'], 
					['type_supports', 'array', 'typesupportnom'],
					'priseenchargenom',
					'realisationtraitementsnom',
					'dt_deb_prestation',
					'dt_fin_prestation',
					'responsable_operation'
					];

		$_delimiter = chr(9); //tabulation en séparateur
		$_enclosure = '';
		$_newline = "\r\n";
		$_eol = "\r\n";
		$_null = '';
		
		$filename = "exportFonds".'-'.mt_rand().'.txt';
		$this->response->download($filename); 
		$this->viewClass = 'CsvView.Csv';
		
		$this->set(compact('fonds', '_serialize', '_delimiter', '_enclosure', '_newline', '_eol','_header', '_extract', '_null'));
		//$this->set(compact('fonds', '_serialize', '_delimiter', '_enclosure', '_newline', '_eol','_header', '_extract'));
		//$this->set(compact('posts', 'users', 'stuff'));
		$this->set('_serialize', array('fonds'));		
	}

    /**
     * Reactivate method
     *
     * @param string|null $id Fond id.
     * @return void Renvoi vers la page de consultation du fonds réactivé.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function reactivate ($id = null)
    {
		$time = Time::now('Europe/Paris')->i18nFormat('YYYY-MM-dd HH:mm:ss');
		
		// Notre suppression était logique !
		// Réactiver ne revient qu'à retirer l'indicateur de suppression. 
		$fond = $this->Fonds->get($id, [
            'contain' => ['TypeDocAfferents', 'AireCulturelles', 'LieuConservations', 'Thematiques', 'TypeConditionnements', 'TypeDocs']
        ]);		
		
		$fond->ind_suppr = false;
		$fond->dt_suppr = null;
		$fond->raison_suppression_id = null;
		
		if ($this->Fonds->save($fond)) {
			$this->Flash->success(__('Le fonds a été réactivé.'));
			return $this->redirect(['action' => 'view', $id]);
		} else {
			$this->Flash->error(__('Le fonds n\'a pas pu être réactivé.'));
		}		
		
    }

	/**
    * GenerateRapports method
    * Affichage et gestion de la page de génération des rapports
    * La production du rapport proprement dite sera gérer dans la
    * méthode generatepdf
    * @return void
    */	
    public function generaterapports()
    {
	$entiteDocs=$this->Fonds->EntiteDocs->find('list', ['limit' => 200]);
	$this->set('entiteDocs',$entiteDocs);
    }
	
    /**
    * implantation method
    * @return void
    */	
    public function implantation()
    {
        $fond = $this->Fonds->find('all', [
            'contain' => [
                                'EntiteDocs',
                                'Adresses',
                                'TypeSupports' => function($q) {return $q->order(['TypeSupports.type' => 'ASC']);}
                        ]
        ]);
        $this->set('fond', $fond);
        $this->set('_serialize', ['fond']);

    }	
}
