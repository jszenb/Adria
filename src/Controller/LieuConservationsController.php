<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * LieuConservations Controller
 *
 * @property \App\Model\Table\LieuConservationsTable $LieuConservations
 */
class LieuConservationsController extends AppController
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
				//'contain' => ['EntiteDocs', 'Fonds'],
				'contain' => ['EntiteDocs'],
				'limit' => 25, 
				'order' => ['LieuConservations.nom' => 'asc']
			];
		}
		else {
			$this->paginate = [
				//'contain' => ['EntiteDocs', 'Fonds'],
				'contain' => ['EntiteDocs'],
				'limit' => 25
			];			
		}
		
        $this->set('lieuConservations', $this->paginate($this->LieuConservations));
        $this->set('_serialize', ['lieuConservations']);

    }

    /**
     * View method
     *
     * @param string|null $id Lieu Conservation id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $lieuConservation = $this->LieuConservations->get($id, [
            'contain' => [
				'EntiteDocs', 
				'Fonds' => [
					'TypeFonds' => function ($q) {
						return $q 
								->where('ind_suppr <> 1')
								->order(['Fonds.nom' => 'ASC']);
					}
				]
			]
        ]);
		
        $this->set('lieuConservation', $lieuConservation);
        $this->set('_serialize', ['lieuConservation']);
	
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$monUser = "";
		$monEntiteDoc = "";
        $lieuConservation = $this->LieuConservations->newEntity();
        if ($this->request->is('post')) {
            $lieuConservation = $this->LieuConservations->patchEntity($lieuConservation, $this->request->data);
            if ($this->LieuConservations->save($lieuConservation)) {
                $this->Flash->success(__('Lieu de conservation ajouté.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The lieu conservation could not be saved. Please, try again.'));
            }
        }
		// Le profil CC peut créer les données comme il le souhaite
		// Le profil CA peut créer des données uniquement pour son entité documentaire et ses lieux de conservation
		// Données nécessaires sur l'utilisateur pour savoir qui est l'utilisateur (son profil, son entité documentaire)
		//dump($this->request->session()->read('Auth'));
			
		$monUser = $this->request->session()->read('Auth');
		$monEntiteDoc = $monUser['User']['entite_doc_id'];				
		$monTypeUser = $monUser['User']['type_user_id'];
		if ($monTypeUser == PROFIL_CC) {		
			$entiteDocs = $this->LieuConservations->EntiteDocs->find('list', ['limit' => 200, 'order' => 'nom']);
			$fonds = $this->LieuConservations->Fonds->find('list', ['limit' => 200, 'conditions' => 'Fonds.ind_suppr <> 1', 'order' => 'nom']);
		}
		else{
			$entiteDocs = $this->LieuConservations->EntiteDocs->find('list')
					->where(['EntiteDocs.id' => $monEntiteDoc]);
			$fonds = $this->LieuConservations->Fonds->find('list', [
				'limit' => 200,
				'conditions' => ['entite_doc_id' => $monEntiteDoc, 'ind_suppr <> ' => 1],
				'order' => 'nom'
				]);
			
		}

		//$entiteDocs = $this->LieuConservations->EntiteDocs->find('all', array('fields' => array('EntiteDocs.id','EntiteDocs.code'), 'limit' => 200));
	
        
        $this->set(compact('lieuConservation', 'entiteDocs', 'fonds'));
        $this->set('_serialize', ['lieuConservation']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Lieu Conservation id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$idEntiteDocEnSession = $this->request->query('entiteDoc');
		$typeUserEnSession =  $this->request->query('typeUser');
		
        $lieuConservation = $this->LieuConservations->get($id, [
            'contain' => [
				'EntiteDocs', 
				'Fonds' => [
					'TypeFonds' => function ($q) {
						return $q 
								->where('ind_suppr <> 1')
								->order(['Fonds.nom' => 'ASC']);
					}
				]
			]
        ]);
		
        if ($this->request->is(['patch', 'post', 'put'])) {
            $lieuConservation = $this->LieuConservations->patchEntity($lieuConservation, $this->request->data);
            if ($this->LieuConservations->save($lieuConservation)) {
                $this->Flash->success(__('Lieu de conservation modifié.'));
                return $this->redirect(['action' => 'view/'.$lieuConservation['id']]);
            } else {
                $this->Flash->error(__('Lieu de conservation n\'a pas été modifié.'));
            }
        }
        $entiteDocs = $this->LieuConservations->EntiteDocs->find('list', ['limit' => 200, 'order' => 'code']);
		
		// Si l'utilisateur est CA il a une entité documentaire : on retrouve les fonds qui sont les siens seulement
		if ( $typeUserEnSession == PROFIL_CA )  {
			$fonds = $this->LieuConservations->Fonds->find('list', [
			'limit' => 200,
			'conditions' => ['entite_doc_id' => $idEntiteDocEnSession, 'ind_suppr <> ' => 1],
			'order' => 'nom'
			]);
		}
		else {
			// Pour l'utilisateur CC, on récupère tout
			$fonds = $this->LieuConservations->Fonds->find('list', ['limit' => 200, 'conditions' => 'Fonds.ind_suppr <> 1', 'order' => 'nom']);
		}
        
        //$fonds = $this->LieuConservations->Fonds->find('list', ['limit' => 200]);
		$this->set(compact('lieuConservation', 'entiteDocs', 'fonds'));
        $this->set('_serialize', ['lieuConservation']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Lieu Conservation id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $lieuConservation = $this->LieuConservations->get($id);
        if ($this->LieuConservations->delete($lieuConservation)) {
            $this->Flash->success(__('The lieu conservation has been deleted.'));
        } else {
            $this->Flash->error(__('The lieu conservation could not be deleted. Please, try again.'));
        }
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
		*     - dresser la liste des lieux de conservation (index)
		*     - consulter toutes les lieux de conservation (view)
		*     - modifier ses lieux de conservation (edit)
		*     - ajouter un lieu de conservation (add)
		*     - ne peut pas supprimer un lieu de conservation
		**********************************************************************/

		if (isset($user['type_user_id']) && $user['type_user_id'] == PROFIL_CA) {

			if (isset($this->request->params['action'])) {
				$action = $this->request->params['action'];
				
				// Action consultation ou lister les lieux de conservation : 
				// actions possibles sans condition
				if(in_array($action, ['index','view', 'add'])) {

					return true;
					
				}
				else {
					//debug('action non index et non view');
					
					// Autres actions :
					// à évaluer selon les cas d'action :
					//   - action de supprimer : KO
					if ($action == 'delete') {
						
						return false;
						
					}
					else {
						
						// l'utilisateur peut modifier un lieu de conservation qui dépend de son entité documentaire
						// c'est dans les paramètres reçus de la page avec l'action. 
						// A-t-on un identifiant indiqué ?
						if (isset($this->request->params['pass'][0])) {
							
							$entiteDoc = $this->request->query('entiteDoc');
							
							//dump('entiteDoc du paramètre : '.$entiteDoc);
							//dump('entiteDoc du user : '.$user['entite_doc_id']);
							
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

	
}
