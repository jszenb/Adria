<?php
/********************************************************************************************/
// Application ADRIA
// TypeStatJuridsController.php
// Contrôleur pour la classe des types de statuts juridiques (table de référence)
//
// Campus Condorcet (2016)
/********************************************************************************************/
namespace App\Controller;

use App\Controller\AppController;

/**
 * TypeStatJurids Controller
 *
 * @property \App\Model\Table\TypeStatJuridsTable $TypeStatJurids
 */
class TypeStatJuridsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('typeStatJurids', $this->paginate($this->TypeStatJurids));
        $this->set('_serialize', ['typeStatJurids']);
    }

    /**
     * View method
     *
     * @param string|null $id Type Stat Jurid id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $typeStatJurid = $this->TypeStatJurids->get($id, [
            'contain' => [
				'Fonds' => function($q) {
							return $q
								->where('ind_suppr <> 1')
								->order(['Fonds.nom' => 'ASC']);
						}
			]
        ]);
		$typeFonds = $this->TypeStatJurids->Fonds->TypeFonds->find('all');
		$this->set('typeFonds', $typeFonds);		
        $this->set('typeStatJurid', $typeStatJurid);
        $this->set('_serialize', ['typeStatJurid']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $typeStatJurid = $this->TypeStatJurids->newEntity();
        if ($this->request->is('post')) {
            $typeStatJurid = $this->TypeStatJurids->patchEntity($typeStatJurid, $this->request->data);
            if ($this->TypeStatJurids->save($typeStatJurid)) {
                $this->Flash->success(__('Le type de statut juridique a été créé.'));
                return $this->redirect(['action' => 'view', $typeStatJurid->id ]);
            } else {
                $this->Flash->error(__('Le type de statut juridique n\'a pas pu être créé.'));
            }
        }
        $this->set(compact('typeStatJurid'));
        $this->set('_serialize', ['typeStatJurid']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Type Stat Jurid id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $typeStatJurid = $this->TypeStatJurids->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $typeStatJurid = $this->TypeStatJurids->patchEntity($typeStatJurid, $this->request->data);
            if ($this->TypeStatJurids->save($typeStatJurid)) {
                $this->Flash->success(__('Le type de statut juridique a été modifié.'));
                return $this->redirect(['action' => 'view', $id]);
            } else {
                $this->Flash->error(__('Le type de statut juridique n\'a pas pu être modifié.'));
            }
        }
        $this->set(compact('typeStatJurid'));
        $this->set('_serialize', ['typeStatJurid']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Type Stat Jurid id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $typeStatJurid = $this->TypeStatJurids->get($id);
        if ($this->TypeStatJurids->delete($typeStatJurid)) {
            $this->Flash->success(__('The type stat jurid has been deleted.'));
        } else {
            $this->Flash->error(__('The type stat jurid could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
