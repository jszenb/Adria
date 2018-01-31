<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * TypeNumerisations Controller
 *
 * @property \App\Model\Table\TypeNumerisationsTable $TypeNumerisations
 */
class TypeNumerisationsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('typeNumerisations', $this->paginate($this->TypeNumerisations));
        $this->set('_serialize', ['typeNumerisations']);
    }

    /**
     * View method
     *
     * @param string|null $id Type Numerisation id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $typeNumerisation = $this->TypeNumerisations->get($id, [
            'contain' => [
				'Fonds' => function($q) {
							return $q
								->where('ind_suppr <> 1')
								->order(['Fonds.nom' => 'ASC']);
						}
			]
        ]);
		$typeFonds = $this->TypeNumerisations->Fonds->TypeFonds->find('all');
		$this->set('typeFonds', $typeFonds);		
        $this->set('typeNumerisation', $typeNumerisation);
        $this->set('_serialize', ['typeNumerisation']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $typeNumerisation = $this->TypeNumerisations->newEntity();
        if ($this->request->is('post')) {
            $typeNumerisation = $this->TypeNumerisations->patchEntity($typeNumerisation, $this->request->data);
            if ($this->TypeNumerisations->save($typeNumerisation)) {
                $this->Flash->success(__('The type numerisation has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The type numerisation could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('typeNumerisation'));
        $this->set('_serialize', ['typeNumerisation']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Type Numerisation id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $typeNumerisation = $this->TypeNumerisations->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $typeNumerisation = $this->TypeNumerisations->patchEntity($typeNumerisation, $this->request->data);
            if ($this->TypeNumerisations->save($typeNumerisation)) {
                $this->Flash->success(__('The type numerisation has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The type numerisation could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('typeNumerisation'));
        $this->set('_serialize', ['typeNumerisation']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Type Numerisation id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $typeNumerisation = $this->TypeNumerisations->get($id);
        if ($this->TypeNumerisations->delete($typeNumerisation)) {
            $this->Flash->success(__('The type numerisation has been deleted.'));
        } else {
            $this->Flash->error(__('The type numerisation could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
