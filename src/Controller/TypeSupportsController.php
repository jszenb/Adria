<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * TypeSupports Controller
 *
 * @property \App\Model\Table\TypeSupportsTable $TypeSupports
 */
class TypeSupportsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('typeSupports', $this->paginate($this->TypeSupports));
        $this->set('_serialize', ['typeSupports']);
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
        $typeSupport = $this->TypeSupports->get($id, [
            'contain' => [
				'Fonds' => function($q) {
							return $q
								->contain('TypeFonds')
								->where('ind_suppr <> 1')
								->order(['Fonds.nom' => 'ASC']);
						}
			]
        ]);		
        $this->set('typeSupport', $typeSupport);
        $this->set('_serialize', ['typeSupport']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $typeSupport = $this->TypeSupports->newEntity();
        if ($this->request->is('post')) {
            $typeSupport = $this->TypeSupports->patchEntity($typeSupport, $this->request->data);
            if ($this->TypeSupports->save($typeSupport)) {
                $this->Flash->success(__('Le type de supports a été ajouté.'));
                return $this->redirect(['action' => 'view', $typeSupport->id]);
            } else {
                $this->Flash->error(__('Le type de supports n\'a pas pu être ajouté.'));
            }
        }
        $this->set(compact('typeSupport'));
        $this->set('_serialize', ['typeSupport']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Type Support id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $typeSupport = $this->TypeSupports->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $typeSupport = $this->TypeSupports->patchEntity($typeSupport, $this->request->data);
            if ($this->TypeSupports->save($typeSupport)) {
                $this->Flash->success(__('Le type de supports a été modifié.'));
                return $this->redirect(['action' => 'view', $id]);
            } else {
                $this->Flash->error(__('Le type de supports n\'a pas pu être modifié.'));
            }
        }
        $this->set(compact('typeSupport'));
        $this->set('_serialize', ['typeSupport']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Type Support id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $typeSupport = $this->TypeSupports->get($id);
        if ($this->TypeSupports->delete($typeSupport)) {
            $this->Flash->success(__('Le type de supports a été supprimé.'));
        } else {
            $this->Flash->error(__('Le type de supports n\'a pas pu être supprimé.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
