<?php
/********************************************************************************************/
// Application ADRIA
// TypeRealisationTraitementsController.php
// Contrôleur pour la classe des types de réalisation de traitement (table de référence)
//
// Campus Condorcet (2017)
/********************************************************************************************/
namespace App\Controller;

use App\Controller\AppController;

/**
 * TypeRealisationTraitements Controller
 *
 * @property \App\Model\Table\TypeRealisationTraitementsTable $TypeRealisationTraitements
 */
class TypeRealisationTraitementsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('typeRealisationTraitements', $this->paginate($this->TypeRealisationTraitements));
        $this->set('_serialize', ['TypeRealisationTraitements']);
    }

    /**
     * View method
     *
     * @param string|null $id TypeRealisationTraitements id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $typeRealisationTraitement = $this->TypeRealisationTraitements->get($id, [
            'contain' => [
				'Fonds' => function($q) {
							return $q
								->where('ind_suppr <> 1')
								->order(['Fonds.nom' => 'ASC']);
						}
			]
        ]); 
		$typeFonds = $this->TypeRealisationTraitements->Fonds->TypeFonds->find('all');
		$this->set('typeFonds', $typeFonds);		
        $this->set('typeRealisationTraitement', $typeRealisationTraitement);
        $this->set('_serialize', ['typeRealisationTraitement']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $typeRealisationTraitement = $this->TypeRealisationTraitements->newEntity();
        if ($this->request->is('post')) {
            $typeRealisationTraitement = $this->TypeRealisationTraitements->patchEntity($typeRealisationTraitement, $this->request->data);
            if ($this->TypeRealisationTraitements->save($typeRealisationTraitement)) {
                $this->Flash->success(__('Le type de réalisation de traitement a été créé.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Le type de réalisation de traitement n\'a pas pu être créé.'));
            }
        }
        $this->set(compact('typeRealisationTraitement'));
        $this->set('_serialize', ['typeRealisationTraitement']);
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
        $typeRealisationTraitement = $this->TypeRealisationTraitements->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $typeRealisationTraitement = $this->TypeRealisationTraitements->patchEntity($typeRealisationTraitement, $this->request->data);
            if ($this->TypeRealisationTraitements->save($typeRealisationTraitement)) {
                $this->Flash->success(__('Le type de réalisation de traitement a été modifié.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Le type de réalisation de traitement n\'a pas pu être modifié.'));
			}
        }
        $this->set(compact('typeRealisationTraitement'));
        $this->set('_serialize', ['typeRealisationTraitement']);
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
        $typeRealisationTraitement = $this->TypeRealisationTraitements->get($id);
        if ($this->TypeRealisationTraitements->delete($typeRealisationTraitement)) {
            $this->Flash->success(__('Le type de réalisation de traitement a été supprimé.'));
        } else {
            $this->Flash->error(__('Le type de réalisation de traitement n\'a pas pu être supprimé.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
