<?php
/********************************************************************************************/
// Application ADRIA
// TypePriseEnChargesController.php
// Contrôleur pour la classe des types de prise en charge (table de référence)
//
// Campus Condorcet (2017)
/********************************************************************************************/
namespace App\Controller;

use App\Controller\AppController;

/**
 * TypePriseEnCharges Controller
 *
 * @property \App\Model\Table\TypePriseEnChargesTable $TypePriseEnCharges
 */
class TypePriseEnChargesController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('typePriseEnCharges', $this->paginate($this->TypePriseEnCharges));
        $this->set('_serialize', ['TypePriseEnCharges']);
    }

    /**
     * View method
     *
     * @param string|null $id Type PriseEnCharge id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $typePriseEnCharge = $this->TypePriseEnCharges->get($id, [
            'contain' => [
				'Fonds' => function($q) {
							return $q
								->where('ind_suppr <> 1')
								->order(['Fonds.nom' => 'ASC']);
						}
			]
        ]); 
		$typeFonds = $this->TypePriseEnCharges->Fonds->TypeFonds->find('all');
		$this->set('typeFonds', $typeFonds);		
        $this->set('typePriseEnCharge', $typePriseEnCharge);
        $this->set('_serialize', ['typePriseEnCharge']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $typePriseEnCharge = $this->TypePriseEnCharges->newEntity();
        if ($this->request->is('post')) {
            $typePriseEnCharge = $this->TypePriseEnCharges->patchEntity($typePriseEnCharge, $this->request->data);
            if ($this->TypePriseEnCharges->save($typePriseEnCharge)) {
                $this->Flash->success(__('Le type de prise en charge a été créé.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Le type de prise en charge n\'a pas pu être créé.'));
            }
        }
        $this->set(compact('typePriseEnCharge'));
        $this->set('_serialize', ['typePriseEnCharge']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Type PriseEnCharge id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $typePriseEnCharge = $this->TypePriseEnCharges->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $typePriseEnCharge = $this->TypePriseEnCharges->patchEntity($typePriseEnCharge, $this->request->data);
            if ($this->TypePriseEnCharges->save($typePriseEnCharge)) {
                $this->Flash->success(__('Le type de prise en charge a été modifié.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Le type de prise en charge n\'a pas pu être modifié.'));
			}
        }
        $this->set(compact('typePriseEnCharge'));
        $this->set('_serialize', ['typePriseEnCharge']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Type PriseEnCharge id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $typePriseEnCharge = $this->TypePriseEnCharges->get($id);
        if ($this->TypePriseEnCharges->delete($typePriseEnCharge)) {
            $this->Flash->success(__('Le type de prise en charge a été supprimé.'));
        } else {
            $this->Flash->error(__('Le type de prise en charge n\'a pas pu être supprimé.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
