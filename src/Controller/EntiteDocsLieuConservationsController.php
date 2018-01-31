<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * EntiteDocsLieuConservations Controller
 *
 * @property \App\Model\Table\EntiteDocsLieuConservationsTable $EntiteDocsLieuConservations
 */
class EntiteDocsLieuConservationsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['EntiteDocs', 'LieuConservations']
        ];
        $this->set('entiteDocsLieuConservations', $this->paginate($this->EntiteDocsLieuConservations));
        $this->set('_serialize', ['entiteDocsLieuConservations']);
    }

    /**
     * View method
     *
     * @param string|null $id Entite Docs Lieu Conservation id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $entiteDocsLieuConservation = $this->EntiteDocsLieuConservations->get($id, [
            'contain' => ['EntiteDocs', 'LieuConservations']
        ]);
        $this->set('entiteDocsLieuConservation', $entiteDocsLieuConservation);
        $this->set('_serialize', ['entiteDocsLieuConservation']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $entiteDocsLieuConservation = $this->EntiteDocsLieuConservations->newEntity();
        if ($this->request->is('post')) {
            $entiteDocsLieuConservation = $this->EntiteDocsLieuConservations->patchEntity($entiteDocsLieuConservation, $this->request->data);
            if ($this->EntiteDocsLieuConservations->save($entiteDocsLieuConservation)) {
                $this->Flash->success(__('The entite docs lieu conservation has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The entite docs lieu conservation could not be saved. Please, try again.'));
            }
        }
        $entiteDocs = $this->EntiteDocsLieuConservations->EntiteDocs->find('list', ['limit' => 200]);
        $lieuConservations = $this->EntiteDocsLieuConservations->LieuConservations->find('list', ['limit' => 200]);
        $this->set(compact('entiteDocsLieuConservation', 'entiteDocs', 'lieuConservations'));
        $this->set('_serialize', ['entiteDocsLieuConservation']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Entite Docs Lieu Conservation id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $entiteDocsLieuConservation = $this->EntiteDocsLieuConservations->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $entiteDocsLieuConservation = $this->EntiteDocsLieuConservations->patchEntity($entiteDocsLieuConservation, $this->request->data);
            if ($this->EntiteDocsLieuConservations->save($entiteDocsLieuConservation)) {
                $this->Flash->success(__('The entite docs lieu conservation has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The entite docs lieu conservation could not be saved. Please, try again.'));
            }
        }
        $entiteDocs = $this->EntiteDocsLieuConservations->EntiteDocs->find('list', ['limit' => 200]);
        $lieuConservations = $this->EntiteDocsLieuConservations->LieuConservations->find('list', ['limit' => 200]);
        $this->set(compact('entiteDocsLieuConservation', 'entiteDocs', 'lieuConservations'));
        $this->set('_serialize', ['entiteDocsLieuConservation']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Entite Docs Lieu Conservation id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $entiteDocsLieuConservation = $this->EntiteDocsLieuConservations->get($id);
        if ($this->EntiteDocsLieuConservations->delete($entiteDocsLieuConservation)) {
            $this->Flash->success(__('The entite docs lieu conservation has been deleted.'));
        } else {
            $this->Flash->error(__('The entite docs lieu conservation could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
