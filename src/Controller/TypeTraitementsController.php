<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * TypeTraitements Controller
 *
 * @property \App\Model\Table\TypeTraitementsTable $TypeTraitements
 */
class TypeTraitementsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('typeTraitements', $this->paginate($this->TypeTraitements));
        $this->set('_serialize', ['typeTraitements']);
    }

    /**
     * View method
     *
     * @param string|null $id Type Traitement id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $typeTraitement = $this->TypeTraitements->get($id, [
            'contain' => [
				'Fonds' => function($q) {
							return $q
								->where('ind_suppr <> 1')
								->order(['Fonds.nom' => 'ASC']);
						}
			]
        ]);
		$typeFonds = $this->TypeTraitements->Fonds->TypeFonds->find('all');
		$this->set('typeFonds', $typeFonds);			
        $this->set('typeTraitement', $typeTraitement);
        $this->set('_serialize', ['typeTraitement']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $typeTraitement = $this->TypeTraitements->newEntity();
        if ($this->request->is('post')) {
            $typeTraitement = $this->TypeTraitements->patchEntity($typeTraitement, $this->request->data);
            if ($this->TypeTraitements->save($typeTraitement)) {
                $this->Flash->success(__('The type traitement has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The type traitement could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('typeTraitement'));
        $this->set('_serialize', ['typeTraitement']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Type Traitement id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $typeTraitement = $this->TypeTraitements->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $typeTraitement = $this->TypeTraitements->patchEntity($typeTraitement, $this->request->data);
            if ($this->TypeTraitements->save($typeTraitement)) {
                $this->Flash->success(__('The type traitement has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The type traitement could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('typeTraitement'));
        $this->set('_serialize', ['typeTraitement']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Type Traitement id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $typeTraitement = $this->TypeTraitements->get($id);
        if ($this->TypeTraitements->delete($typeTraitement)) {
            $this->Flash->success(__('The type traitement has been deleted.'));
        } else {
            $this->Flash->error(__('The type traitement could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
