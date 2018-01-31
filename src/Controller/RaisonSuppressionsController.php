<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * RaisonSuppressions Controller
 *
 * @property \App\Model\Table\RaisonSuppressionsTable $RaisonSuppressions
 */
class RaisonSuppressionsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('raisonSuppressions', $this->paginate($this->RaisonSuppressions));
        $this->set('_serialize', ['raisonSuppressions']);
    }

    /**
     * View method
     *
     * @param string|null $id Raison Suppression id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $raisonSuppression = $this->RaisonSuppressions->get($id, [
            'contain' => [
				'Fonds' => function($q) {
							return $q
								->where('ind_suppr = 1')
								->order(['Fonds.nom' => 'ASC']);
						}
			]
        ]);
		$typeFonds = $this->RaisonSuppressions->Fonds->TypeFonds->find('all');
		$this->set('typeFonds', $typeFonds);
        $this->set('raisonSuppression', $raisonSuppression);
        $this->set('_serialize', ['raisonSuppression']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $raisonSuppression = $this->RaisonSuppressions->newEntity();
        if ($this->request->is('post')) {
            $raisonSuppression = $this->RaisonSuppressions->patchEntity($raisonSuppression, $this->request->data);
            if ($this->RaisonSuppressions->save($raisonSuppression)) {
                $this->Flash->success(__('La raison de suppression a été créée.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('La raison de suppression n\'a pas pu être créée.'));
            }
        }
        $this->set(compact('raisonSuppression'));
        $this->set('_serialize', ['raisonSuppression']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Raison Suppression id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $raisonSuppression = $this->RaisonSuppressions->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $raisonSuppression = $this->RaisonSuppressions->patchEntity($raisonSuppression, $this->request->data);
            if ($this->RaisonSuppressions->save($raisonSuppression)) {
                $this->Flash->success(__('La raison de suppression a été modifiée.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('La raison de suppression n\'a pas pu être modifiée.'));
            }
        }
        $this->set(compact('raisonSuppression'));
        $this->set('_serialize', ['raisonSuppression']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Raison Suppression id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $raisonSuppression = $this->RaisonSuppressions->get($id);
        if ($this->RaisonSuppressions->delete($raisonSuppression)) {
            $this->Flash->success(__('La raison de suppression a été supprimée.'));
        } else {
            $this->Flash->error(__('La raison de suppression n\'a pas pu être supprimée.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
