<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * TypeEntrees Controller
 *
 * @property \App\Model\Table\TypeEntreesTable $TypeEntrees
 */
class TypeEntreesController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('typeEntrees', $this->paginate($this->TypeEntrees));
        $this->set('_serialize', ['typeEntrees']);
    }

    /**
     * View method
     *
     * @param string|null $id Type Entree id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $typeEntree = $this->TypeEntrees->get($id, [
            'contain' => [
				'Fonds' => function($q) {
							return $q
								->where('ind_suppr <> 1')
								->order(['Fonds.nom' => 'ASC']);
						}
			]
        ]);
		$typeFonds = $this->TypeEntrees->Fonds->TypeFonds->find('all');
		$this->set('typeFonds', $typeFonds);		
        $this->set('typeEntree', $typeEntree);
        $this->set('_serialize', ['typeEntree']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $typeEntree = $this->TypeEntrees->newEntity();
        if ($this->request->is('post')) {
            $typeEntree = $this->TypeEntrees->patchEntity($typeEntree, $this->request->data);
            if ($this->TypeEntrees->save($typeEntree)) {
                $this->Flash->success(__('Le type d\'entrée a été ajouté.'));
                return $this->redirect(['action' => 'view', $typeEntree->id]);
            } else {
                $this->Flash->error(__('Le type d\'entrée n\'a pas pu être ajouté.'));
            }
        }
        $this->set(compact('typeEntree'));
        $this->set('_serialize', ['typeEntree']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Type Entree id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $typeEntree = $this->TypeEntrees->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $typeEntree = $this->TypeEntrees->patchEntity($typeEntree, $this->request->data);
            if ($this->TypeEntrees->save($typeEntree)) {
                $this->Flash->success(__('Le type d\'entrée a été modifié.'));
                return $this->redirect(['action' => 'view', $id]);
            } else {
                $this->Flash->error(__('Le type d\'entrée n\'a pas pu être modifié.'));
            }
        }
        $this->set(compact('typeEntree'));
        $this->set('_serialize', ['typeEntree']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Type Entree id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $typeEntree = $this->TypeEntrees->get($id);
        if ($this->TypeEntrees->delete($typeEntree)) {
            $this->Flash->success(__('The type entree has been deleted.'));
        } else {
            $this->Flash->error(__('The type entree could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
