<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * TypeInstrRechs Controller
 *
 * @property \App\Model\Table\TypeInstrRechsTable $TypeInstrRechs
 */
class TypeInstrRechsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('typeInstrRechs', $this->paginate($this->TypeInstrRechs));
        $this->set('_serialize', ['typeInstrRechs']);
    }

    /**
     * View method
     *
     * @param string|null $id Type Instr Rech id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $typeInstrRech = $this->TypeInstrRechs->get($id, [
            'contain' => [
				'Fonds' => function($q) {
							return $q
								->where('ind_suppr <> 1')
								->order(['Fonds.nom' => 'ASC']);
						}
			]
        ]);
		$typeFonds = $this->TypeInstrRechs->Fonds->TypeFonds->find('all');
		$this->set('typeFonds', $typeFonds);		
        $this->set('typeInstrRech', $typeInstrRech);
        $this->set('_serialize', ['typeInstrRech']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $typeInstrRech = $this->TypeInstrRechs->newEntity();
        if ($this->request->is('post')) {
            $typeInstrRech = $this->TypeInstrRechs->patchEntity($typeInstrRech, $this->request->data);
            if ($this->TypeInstrRechs->save($typeInstrRech)) {
                $this->Flash->success(__('The type instr rech has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The type instr rech could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('typeInstrRech'));
        $this->set('_serialize', ['typeInstrRech']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Type Instr Rech id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $typeInstrRech = $this->TypeInstrRechs->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $typeInstrRech = $this->TypeInstrRechs->patchEntity($typeInstrRech, $this->request->data);
            if ($this->TypeInstrRechs->save($typeInstrRech)) {
                $this->Flash->success(__('The type instr rech has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The type instr rech could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('typeInstrRech'));
        $this->set('_serialize', ['typeInstrRech']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Type Instr Rech id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $typeInstrRech = $this->TypeInstrRechs->get($id);
        if ($this->TypeInstrRechs->delete($typeInstrRech)) {
            $this->Flash->success(__('The type instr rech has been deleted.'));
        } else {
            $this->Flash->error(__('The type instr rech could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
