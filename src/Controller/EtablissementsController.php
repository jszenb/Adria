<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Etablissements Controller
 *
 * @property \App\Model\Table\EtablissementsTable $Etablissements
 */
class EtablissementsController extends AppController
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
				'contain' => ['EntiteDocs'],
				'limit' => 25,
				'order' => ['nom' => 'asc']
			];
		}
		else {
			$this->paginate = [
				'contain' => ['EntiteDocs'],
				'limit' => 25
			];			
		}
		$this->set('etablissements', $this->paginate($this->Etablissements));
        $this->set('_serialize', ['etablissements']);
    }

    /**
     * View method
     *
     * @param string|null $id Etablissement id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $etablissement = $this->Etablissements->get($id, [
            'contain' => [
				'EntiteDocs' => function($q) {return $q->order(['EntiteDocs.nom' => 'ASC']);}
			]
        ]);
		
        $this->set('etablissement', $etablissement);
        $this->set('_serialize', ['etablissement']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $etablissement = $this->Etablissements->newEntity();
        if ($this->request->is('post')) {
            $etablissement = $this->Etablissements->patchEntity($etablissement, $this->request->data);
            if ($this->Etablissements->save($etablissement)) {
                $this->Flash->success(__('Etablissement créé.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('L\'établissement n\'a pas pu être créé.'));
            }
        }
        $this->set(compact('etablissement'));
        $this->set('_serialize', ['etablissement']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Etablissement id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $etablissement = $this->Etablissements->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $etablissement = $this->Etablissements->patchEntity($etablissement, $this->request->data);
            if ($this->Etablissements->save($etablissement)) {
                $this->Flash->success(__('Etablissement modifié.'));
                return $this->redirect(['action' => 'view/'.$etablissement['id']]);
            } else {
                $this->Flash->error(__('L\'établissement n\'a pas pu être modifié.'));
            }
        }
        $this->set(compact('etablissement'));
        $this->set('_serialize', ['etablissement']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Etablissement id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $etablissement = $this->Etablissements->get($id);
        if ($this->Etablissements->delete($etablissement)) {
            $this->Flash->success(__('Etablissement supprimé.'));
        } else {
            $this->Flash->error(__('L\'établissement n\'a pas pu être supprimé.'));
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
		*     - dresser la liste des établissements (index)
		*     - consulter tous les établissements (view)
		*     - les autres actions lui sont interdites
		**********************************************************************/

		if (isset($user['type_user_id']) && $user['type_user_id'] == PROFIL_CA) {

			
			if (isset($this->request->params['action'])) {
				$action = $this->request->params['action'];
				
				// Action consultation ou lister les données : 
				// actions possibles sans condition
				if(in_array($action, ['index','view'])) {

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
}
