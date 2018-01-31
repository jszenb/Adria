<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * TypeUsers Controller
 *
 * @property \App\Model\Table\TypeUsersTable $TypeUsers
 */
class TypeUsersController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('typeUsers', $this->paginate($this->TypeUsers));
        $this->set('_serialize', ['typeUsers']);
    }

    /**
     * View method
     *
     * @param string|null $id Type User id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $typeUser = $this->TypeUsers->get($id, [
            'contain' => [
				'Users' => function($q) {return $q->order(['Users.nom' => 'ASC']);}
			]
        ]);
        $this->set('typeUser', $typeUser);
        $this->set('_serialize', ['typeUser']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $typeUser = $this->TypeUsers->newEntity();
        if ($this->request->is('post')) {
            $typeUser = $this->TypeUsers->patchEntity($typeUser, $this->request->data);
            if ($this->TypeUsers->save($typeUser)) {
                $this->Flash->success(__('The type user has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The type user could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('typeUser'));
        $this->set('_serialize', ['typeUser']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Type User id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $typeUser = $this->TypeUsers->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $typeUser = $this->TypeUsers->patchEntity($typeUser, $this->request->data);
            if ($this->TypeUsers->save($typeUser)) {
                $this->Flash->success(__('The type user has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The type user could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('typeUser'));
        $this->set('_serialize', ['typeUser']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Type User id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $typeUser = $this->TypeUsers->get($id);
        if ($this->TypeUsers->delete($typeUser)) {
            $this->Flash->success(__('The type user has been deleted.'));
        } else {
            $this->Flash->error(__('The type user could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
