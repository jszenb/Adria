<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * TypeDocAfferents Controller
 *
 * @property \App\Model\Table\TypeDocAfferentsTable $TypeDocAfferents
 */
class TypeDocAfferentsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('typeDocAfferents', $this->paginate($this->TypeDocAfferents));
        $this->set('_serialize', ['typeDocAfferents']);
    }

    /**
     * View method
     *
     * @param string|null $id Type Doc Afferent id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $typeDocAfferent = $this->TypeDocAfferents->get($id, [
            'contain' => [
				'Fonds' => function($q) {
							return $q
								->where('ind_suppr <> 1')
								->order(['Fonds.nom' => 'ASC']);
						}
			]
        ]);
		$typeFonds = $this->TypeDocAfferents->Fonds->TypeFonds->find('all');
		$this->set('typeFonds', $typeFonds);		
        $this->set('typeDocAfferent', $typeDocAfferent);
        $this->set('_serialize', ['typeDocAfferent']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $typeDocAfferent = $this->TypeDocAfferents->newEntity();
        if ($this->request->is('post')) {
            $typeDocAfferent = $this->TypeDocAfferents->patchEntity($typeDocAfferent, $this->request->data);
            if ($this->TypeDocAfferents->save($typeDocAfferent)) {
                $this->Flash->success(__('The type doc afferent has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The type doc afferent could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('typeDocAfferent'));
        $this->set('_serialize', ['typeDocAfferent']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Type Doc Afferent id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $typeDocAfferent = $this->TypeDocAfferents->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $typeDocAfferent = $this->TypeDocAfferents->patchEntity($typeDocAfferent, $this->request->data);
            if ($this->TypeDocAfferents->save($typeDocAfferent)) {
                $this->Flash->success(__('The type doc afferent has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The type doc afferent could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('typeDocAfferent'));
        $this->set('_serialize', ['typeDocAfferent']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Type Doc Afferent id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $typeDocAfferent = $this->TypeDocAfferents->get($id);
        if ($this->TypeDocAfferents->delete($typeDocAfferent)) {
            $this->Flash->success(__('The type doc afferent has been deleted.'));
        } else {
            $this->Flash->error(__('The type doc afferent could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
