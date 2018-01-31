<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * AireCulturelles Controller
 *
 * @property \App\Model\Table\AireCulturellesTable $AireCulturelles
 */
class AireCulturellesController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('aireCulturelles', $this->paginate($this->AireCulturelles));
        $this->set('_serialize', ['aireCulturelles']);
    }

    /**
     * View method
     *
     * @param string|null $id Aire Culturelle id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $aireCulturelle = $this->AireCulturelles->get($id, [
            'contain' => [
				'Fonds' => function($q) {
							return $q
								->where('ind_suppr <> 1')
								->order(['Fonds.nom' => 'ASC']);
						}
			]
        ]);
		$typeFonds = $this->AireCulturelles->Fonds->TypeFonds->find('all');
		$this->set('typeFonds', $typeFonds);
        $this->set('aireCulturelle', $aireCulturelle);
        $this->set('_serialize', ['aireCulturelle']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $aireCulturelle = $this->AireCulturelles->newEntity();
        if ($this->request->is('post')) {
            $aireCulturelle = $this->AireCulturelles->patchEntity($aireCulturelle, $this->request->data);
            if ($this->AireCulturelles->save($aireCulturelle)) {
                $this->Flash->success(__('L\'aire culturelle a été créée.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('L\'aire culturelle n\'a pas pu être créée.'));
            }
        }
        $fonds = $this->AireCulturelles->Fonds->find('list', ['limit' => 200]);
        $this->set(compact('aireCulturelle', 'fonds'));
        $this->set('_serialize', ['aireCulturelle']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Aire Culturelle id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $aireCulturelle = $this->AireCulturelles->get($id, [
            'contain' => ['Fonds']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $aireCulturelle = $this->AireCulturelles->patchEntity($aireCulturelle, $this->request->data);
            if ($this->AireCulturelles->save($aireCulturelle)) {
                $this->Flash->success(__('L\'aire culturelle a été modifiée.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('L\'aire culturelle n\'a pas pu être modifiée.'));
            }
        }
        $fonds = $this->AireCulturelles->Fonds->find('list', ['limit' => 200]);
        $this->set(compact('aireCulturelle', 'fonds'));
        $this->set('_serialize', ['aireCulturelle']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Aire Culturelle id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $aireCulturelle = $this->AireCulturelles->get($id);
        if ($this->AireCulturelles->delete($aireCulturelle)) {
            $this->Flash->success(__('L\'aire culturelle a été supprimée.'));
        } else {
            $this->Flash->error(__('L\'aire culturelle n\'a pas pu être supprimée.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
