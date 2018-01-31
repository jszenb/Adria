<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * TypeFonds Controller
 *
 * @property \App\Model\Table\TypeFondsTable $TypeFonds
 */
class TypeFondsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('typeFonds', $this->paginate($this->TypeFonds));
        $this->set('_serialize', ['typeFonds']);
    }

    /**
     * View method
     *
     * @param string|null $id Type Fond id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $typeFond = $this->TypeFonds->get($id, [
            'contain' => [
				'Fonds' => function($q) {
							return $q
								->where('ind_suppr <> 1')
								->order(['Fonds.nom' => 'ASC']);
						}
			]
        ]);
		$typeFonds = $this->TypeFonds->Fonds->TypeFonds->find('all');
		$this->set('typeFonds', $typeFonds);		
        $this->set('typeFond', $typeFond);
        $this->set('_serialize', ['typeFond']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $typeFond = $this->TypeFonds->newEntity();
        if ($this->request->is('post')) {
            $typeFond = $this->TypeFonds->patchEntity($typeFond, $this->request->data);
            if ($this->TypeFonds->save($typeFond)) {
                $this->Flash->success(__('Ce type de fonds a été ajouté.'));
                return $this->redirect(['action' => 'index', $id]);
            } else {
                $this->Flash->error(__('Ce type de fonds n\'a pas pu être ajouté.'));
            }
        }
        $this->set(compact('typeFond'));
        $this->set('_serialize', ['typeFond']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Type Fond id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $typeFond = $this->TypeFonds->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $typeFond = $this->TypeFonds->patchEntity($typeFond, $this->request->data);
            if ($this->TypeFonds->save($typeFond)) {
                $this->Flash->success(__('Le type de fonds a été modifié.'));
                return $this->redirect(['action' => 'view', $id]);
            } else {
                $this->Flash->error(__('Ce type de fonds n\'a pas pu être modifié.'));
            }
        }
        $this->set(compact('typeFond'));
        $this->set('_serialize', ['typeFond']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Type Fond id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $typeFond = $this->TypeFonds->get($id);
        if ($this->TypeFonds->delete($typeFond)) {
            $this->Flash->success(__('The type fond has been deleted.'));
        } else {
            $this->Flash->error(__('The type fond could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
