<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * TypeConditionnements Controller
 *
 * @property \App\Model\Table\TypeConditionnementsTable $TypeConditionnements
 */
class TypeConditionnementsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('typeConditionnements', $this->paginate($this->TypeConditionnements));
        $this->set('_serialize', ['typeConditionnements']);
    }

    /**
     * View method
     *
     * @param string|null $id Type Conditionnement id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $typeConditionnement = $this->TypeConditionnements->get($id, [
            'contain' => [
				'Fonds' => function($q) {
							return $q
								->where('ind_suppr <> 1')
								->order(['Fonds.nom' => 'ASC']);
						}
			]
        ]);
		$typeFonds = $this->TypeConditionnements->Fonds->TypeFonds->find('all');
		$this->set('typeFonds', $typeFonds);		
        $this->set('typeConditionnement', $typeConditionnement);
        $this->set('_serialize', ['typeConditionnement']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $typeConditionnement = $this->TypeConditionnements->newEntity();
        if ($this->request->is('post')) {
            $typeConditionnement = $this->TypeConditionnements->patchEntity($typeConditionnement, $this->request->data);
            if ($this->TypeConditionnements->save($typeConditionnement)) {
                $this->Flash->success(__('Le type de conditionnement a été créé.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Le type de conditionnement n\'a pu être créé.'));
            }
        }
        $fonds = $this->TypeConditionnements->Fonds->find('list', ['limit' => 200]);
        $this->set(compact('typeConditionnement', 'fonds'));
        $this->set('_serialize', ['typeConditionnement']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Type Conditionnement id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $typeConditionnement = $this->TypeConditionnements->get($id, [
            'contain' => ['Fonds']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $typeConditionnement = $this->TypeConditionnements->patchEntity($typeConditionnement, $this->request->data);
            if ($this->TypeConditionnements->save($typeConditionnement)) {
                $this->Flash->success(__('Le type de conditionnement a été modifié.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Le type de conditionnement n\'a pu être modifié.'));
            }
        }
        $fonds = $this->TypeConditionnements->Fonds->find('list', ['limit' => 200]);
        $this->set(compact('typeConditionnement', 'fonds'));
        $this->set('_serialize', ['typeConditionnement']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Type Conditionnement id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $typeConditionnement = $this->TypeConditionnements->get($id);
        if ($this->TypeConditionnements->delete($typeConditionnement)) {
            $this->Flash->success(__('Le type de conditionnement a été supprimé.'));
        } else {
            $this->Flash->error(__('Le type de conditionnement n\'a pas pu être supprimé.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
