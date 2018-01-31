<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * TypeAccroissements Controller
 *
 * @property \App\Model\Table\TypeAccroissementsTable $TypeAccroissements
 */
class TypeAccroissementsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('typeAccroissements', $this->paginate($this->TypeAccroissements));
        $this->set('_serialize', ['typeAccroissements']);
    }

    /**
     * View method
     *
     * @param string|null $id Type Accroissement id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $typeAccroissement = $this->TypeAccroissements->get($id, [
            'contain' => [
				'Fonds' => function($q) {
							return $q
								->where('ind_suppr <> 1')
								->order(['Fonds.nom' => 'ASC']);
						}
			]
        ]);
		$typeFonds = $this->TypeAccroissements->Fonds->TypeFonds->find('all');
		$this->set('typeFonds', $typeFonds);		
        $this->set('typeAccroissement', $typeAccroissement);
        $this->set('_serialize', ['typeAccroissement']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $typeAccroissement = $this->TypeAccroissements->newEntity();
        if ($this->request->is('post')) {
            $typeAccroissement = $this->TypeAccroissements->patchEntity($typeAccroissement, $this->request->data);
            if ($this->TypeAccroissements->save($typeAccroissement)) {
                $this->Flash->success(__('Le type d\'accroissement a été créé.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Le type d\'accroissement n\'a pas pu être créé.'));
            }
        }
        $this->set(compact('typeAccroissement'));
        $this->set('_serialize', ['typeAccroissement']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Type Accroissement id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $typeAccroissement = $this->TypeAccroissements->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $typeAccroissement = $this->TypeAccroissements->patchEntity($typeAccroissement, $this->request->data);
            if ($this->TypeAccroissements->save($typeAccroissement)) {
                $this->Flash->success(__('Le type d\'accroissement a été modifié.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Le type d\'accroissement n\'a pas pu être modifié.'));
			}
        }
        $this->set(compact('typeAccroissement'));
        $this->set('_serialize', ['typeAccroissement']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Type Accroissement id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $typeAccroissement = $this->TypeAccroissements->get($id);
        if ($this->TypeAccroissements->delete($typeAccroissement)) {
            $this->Flash->success(__('Le type d\'accroissement a été supprimé.'));
        } else {
            $this->Flash->error(__('Le type d\'accroissement n\'a pas pu être supprimé.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
