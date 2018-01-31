<?php
namespace App\Controller;

use App\Controller\AppController;


/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
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
				'contain' => ['EntiteDocs', 'TypeUsers'],		
				'limit' => 25,
				'sortWhitelist' => [
					'nom',	
					'prenom',
					'EntiteDocs.code',
					'TypeUsers.type', 
					'mail'
				],
				'order' => ['nom' => 'asc', 'prenom' => 'asc']
			];
		}
		else {
			$this->paginate = [
				'contain' => ['EntiteDocs', 'TypeUsers'],
				'limit' => 25,
				'sortWhitelist' => [
					'nom',	
					'prenom',
					'EntiteDocs.code',
					'TypeUsers.type', 
					'mail'
				]			
			];			
		}
        $this->set('users', $this->paginate($this->Users));
        $this->set('_serialize', ['users']);
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['EntiteDocs', 'TypeUsers']
        ]);
        $this->set('user', $user);
        $this->set('_serialize', ['user']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEntity();
		
		
        if ($this->request->is('post')) {
			$user = $this->Users->patchEntity($user, $this->request->data);
			if ( ($user['type_user_id'] == PROFIL_CA && !empty($user['entite_doc_id']) ) || ($user['type_user_id'] == PROFIL_CC) || ($user['type_user_id'] == PROFIL_CO) ) {
				if ($this->Users->save($user)) {
						$this->Flash->success(__('Utilisateur créé.'));
						return $this->redirect(['action' => 'index']);
					} else {
						$this->Flash->error(__('L\'utilisateur n\'a pas pu être créé.'));
					}
			}
			else {
				$this->Flash->error(__('Un utilisateur chargé d\'archives doit être associé à une entité documentaire.'));
			}
        }
		$entiteDocs = $this->Users->EntiteDocs->find('list', ['limit' => 200]);
        $typeUsers = $this->Users->TypeUsers->find('list', ['limit' => 200]);
        $this->set(compact('user', 'entiteDocs', 'typeUsers'));
        $this->set('_serialize', ['user']);
		
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->data);
			if ( ($user['type_user_id'] == PROFIL_CA && !empty($user['entite_doc_id']) ) || ($user['type_user_id'] == PROFIL_CC) || ($user['type_user_id'] == PROFIL_CO) ) {
				if ($this->Users->save($user)) {
					$this->Flash->success(__('Utilisateur modifié.'));
					return $this->redirect(['action' => 'view/'.$user['id']]);
				} else {
					$this->Flash->error(__('L\'utilisateur n\'a pas pu être modifié.'));
				}
			}
			else {
				$this->Flash->error(__('Un utilisateur chargé d\'archives doit être associé à une entité documentaire.'));
			}				
        }
        $entiteDocs = $this->Users->EntiteDocs->find('list', ['limit' => 200]);
        $typeUsers = $this->Users->TypeUsers->find('list', ['limit' => 200]);
        $this->set(compact('user', 'entiteDocs', 'typeUsers'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('Utilisateur supprimé.'));
        } else {
            $this->Flash->error(__('L\'utilisateur a été supprimé.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Login method
     *
     * @param void.
     * @return void Redirects to index.
     * @throws customized error message When record not found.
     */
	public function login()
	{
		if ($this->request->is('post')) {

			$user = $this->Auth->identify();
			
			if ($user) {
				$this->Auth->setUser($user);
				return $this->redirect($this->Auth->redirectUrl());
			}
			$this->Flash->error('Votre identifiant ou votre mot de passe est incorrect.');
		}
	}

	public function logout() {
		
		$this->Flash->success("Vous avez bien été déconnecté-e.");
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
		*     - dresser la liste des users (index)
		*     - consulter tous les users (view)
		*     - modifier son propre user (edit)
		*     - ne peut pas ajouter un user (add)
		*     - ne peut pas supprimer un user
		**********************************************************************/

		if ( 
				isset( $user['type_user_id'] ) 
				&& 
				( 
					( $user['type_user_id'] == PROFIL_CA ) 
					|| 
					( $user['type_user_id'] == PROFIL_CO ) 
				) 
			) 
			
			{

			if (isset($this->request->params['action'])) {
				$action = $this->request->params['action'];
				
				// Action consultation ou lister les données : 
				// actions possibles sans condition
				if(in_array($action, ['index','view'])) {

					return true;
					
				}
				else {
					//debug('action non index et non view');
					
					// Autres actions :
					// à évaluer selon les cas d'action :
					//   - action d'ajouter : KO
					//   - action d'éditer : OK pour son propre user, sinon KO
					//   - action de supprimer : KO
					if (in_array($action, ['add', 'delete'])) {
						
						return false;
						
					}
					else {
						// L'identifiant du user pour laquelle on veut l'action
						// est dans les paramètres reçus de la page avec l'action. 
						if (isset($this->request->params['pass'][0])) {
							
							$userId = $this->request->params['pass'][0];
							
							//debug('user du paramètre : '.$userId);
							//debug('user du user : '.$user['id']);
							
							// Si le user pour lequel l'action est demandé est
							// celui du user, c'est OK. Sinon c'est KO.
							if ($user['id'] == $userId) {
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
