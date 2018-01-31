<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Thematiques Controller
 *
 * @property \App\Model\Table\ThematiquesTable $Thematiques
 */
class ThematiquesController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('thematiques', $this->paginate($this->Thematiques));
        $this->set('_serialize', ['thematiques']);
    }

    /**
     * View method
     *
     * @param string|null $id Thematique id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $thematique = $this->Thematiques->get($id, [
            'contain' => [
				'Fonds' => function($q) {
							return $q
								->where('ind_suppr <> 1')
								->order(['Fonds.nom' => 'ASC']);
						}
			]
        ]);
		$typeFonds = $this->Thematiques->Fonds->TypeFonds->find('all');
		$this->set('typeFonds', $typeFonds);		
        $this->set('thematique', $thematique);
        $this->set('_serialize', ['thematique']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $thematique = $this->Thematiques->newEntity();
        if ($this->request->is('post')) {
            $thematique = $this->Thematiques->patchEntity($thematique, $this->request->data);
            if ($this->Thematiques->save($thematique)) {
                $this->Flash->success(__('La thématique a été créée.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('La thématique n\'a pas pu être créée.'));
            }
        }
        $fonds = $this->Thematiques->Fonds->find('list', ['limit' => 200]);
        $this->set(compact('thematique', 'fonds'));
        $this->set('_serialize', ['thematique']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Thematique id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $thematique = $this->Thematiques->get($id, [
            'contain' => ['Fonds']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $thematique = $this->Thematiques->patchEntity($thematique, $this->request->data);
            if ($this->Thematiques->save($thematique)) {
                $this->Flash->success(__('La thématique a été modifiée.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('La thématique n\'a pas pu être modifiée.'));
            }
        }
        $fonds = $this->Thematiques->Fonds->find('list', ['limit' => 200]);
        $this->set(compact('thematique', 'fonds'));
        $this->set('_serialize', ['thematique']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Thematique id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $thematique = $this->Thematiques->get($id);
        if ($this->Thematiques->delete($thematique)) {
            $this->Flash->success(__('La thématique a été supprimée.'));
        } else {
            $this->Flash->error(__('La thématique n\'a pas pu être supprimée.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
