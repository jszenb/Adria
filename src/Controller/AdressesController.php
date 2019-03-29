<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Adresses Controller
 *
 * @property \App\Model\Table\AdressesTable $Adresses
 *
 * @method \App\Model\Entity\Adress[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AdressesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Fonds']
        ];
        $adresses = $this->paginate($this->Adresses);

        $this->set(compact('adresses'));
    }

    /**
     * View method
     *
     * @param string|null $id Adress id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $adress = $this->Adresses->get($id, [
            'contain' => ['Fonds']
        ]);

        $this->set('adress', $adress);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $adress = $this->Adresses->newEntity();
        if ($this->request->is('post')) {
            $adress = $this->Adresses->patchEntity($adress, $this->request->getData());
            if ($this->Adresses->save($adress)) {
                $this->Flash->success(__('The adress has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The adress could not be saved. Please, try again.'));
        }
        $fonds = $this->Adresses->Fonds->find('list', ['limit' => 200]);
        $this->set(compact('adress', 'fonds'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Adress id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $adress = $this->Adresses->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $adress = $this->Adresses->patchEntity($adress, $this->request->getData());
            if ($this->Adresses->save($adress)) {
                $this->Flash->success(__('The adress has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The adress could not be saved. Please, try again.'));
        }
        $fonds = $this->Adresses->Fonds->find('list', ['limit' => 200]);
        $this->set(compact('adress', 'fonds'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Adress id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $adress = $this->Adresses->get($id);
        if ($this->Adresses->delete($adress)) {
            $this->Flash->success(__('The adress has been deleted.'));
        } else {
            $this->Flash->error(__('The adress could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    /** Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function implantation()
    {
        // Récupération des adresses par magasin, épi, travée, tablette.
        $adresses = $this->Adresses->find('all', 
                                          [
                                           'contain' => ['Fonds' => ['EntiteDocs' => function ($q) {return $q->select(['fondnom' => 'Fonds.nom', 'fondml' => 'Fonds.nb_ml', 'entite' => 'EntiteDocs.code']);}]
                                                        ],
                                           'conditions' => [ 'Fonds.ind_suppr <> ' => '1',
                                                             'Adresses.magasin <> ' => ''
                                                           ],
                                           'order' => ['magasin' => 'ASC', 'epi_deb' =>  'ASC', 'epi_fin' => 'ASC', 'travee_deb' => 'ASC', 'travee_fin' => 'ASC', 'tablette_deb' => 'ASC', 'tablette_fin' => 'ASC', 'fondnom' => 'ASC']
                                          ]);

        $this->set(compact('adresses'));
    }

}
