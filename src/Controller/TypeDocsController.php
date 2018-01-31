<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Validation\Validator;

/**
 * TypeDocs Controller
 *
 * @property \App\Model\Table\TypeDocsTable $TypeDocs
 */
class TypeDocsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('typeDocs', $this->paginate($this->TypeDocs));
        $this->set('_serialize', ['typeDocs']);
    }

    /**
     * View method
     *
     * @param string|null $id Type Doc id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $typeDoc = $this->TypeDocs->get($id, [
            'contain' => [
				'Fonds' => function($q) {
							return $q
								->where('ind_suppr <> 1')
								->order(['Fonds.nom' => 'ASC']);
						}
			]
        ]);
		$typeFonds = $this->TypeDocs->Fonds->TypeFonds->find('all');
		$this->set('typeFonds', $typeFonds);		
        $this->set('typeDoc', $typeDoc);
        $this->set('_serialize', ['typeDoc']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		
		
        $typeDoc = $this->TypeDocs->newEntity();
        if ($this->request->is('post')) {
            $typeDoc = $this->TypeDocs->patchEntity($typeDoc, $this->request->data);
			
			// Contrôle d'intégrité
			//if ($this->validateSupport($typeDoc)){
				if ($this->TypeDocs->save($typeDoc)) {
					$this->Flash->success(__('Type de document créé.'));
					return $this->redirect(['action' => 'view', $typeDoc->id]);
				} else {
					$this->Flash->error(__('Ce type de document n\'a pas pu être créé.'));
				}
			//}
        }
        $fonds = $this->TypeDocs->Fonds->find('list', ['limit' => 200]);
        $this->set(compact('typeDoc', 'fonds'));
        $this->set('_serialize', ['typeDoc']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Type Doc id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $typeDoc = $this->TypeDocs->get($id, [
            'contain' => ['Fonds']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $typeDoc = $this->TypeDocs->patchEntity($typeDoc, $this->request->data);
			
			//if ($this->validateSupport($typeDoc)){
				if ($this->TypeDocs->save($typeDoc)) {
					$this->Flash->success(__('Type de document modifié.'));
					return $this->redirect(['action' => 'view', $id]);
				} else {
					$this->Flash->error(__('Ce type de document n\'a pas pu être modifié.'));
				}
			//}
        }
        $fonds = $this->TypeDocs->Fonds->find('list', ['limit' => 200]);
        $this->set(compact('typeDoc', 'fonds'));
        $this->set('_serialize', ['typeDoc']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Type Doc id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $typeDoc = $this->TypeDocs->get($id);
        if ($this->TypeDocs->delete($typeDoc)) {
            $this->Flash->success(__('Type de document supprimé.'));
        } else {
            $this->Flash->error(__('Ce type de document n\'a pas pu être supprimé.'));
        }
        return $this->redirect(['action' => 'index']);
    }
	
    /**
     * validateSupport method
     *
     * @param $typeDoc le type de document en cours de vérification
     * @return true/false.
	 * La méthode renvoie aussi un message d'erreur adéquat
     */
	 public function validateSupport($typeDoc){
		// le type de document doit avoir au moins une forme (écrit, graphique, etc...)
		/*if ( !$typeDoc['ind_ecrit'] && !$typeDoc['ind_graphique'] && !$typeDoc['ind_audio'] && !$typeDoc['ind_video'] && !$typeDoc['ind_objet']) {
			$this->Flash->error(__('Sélectionner au moins une forme pour ce type de documents.'));
			return false;
		}
		
		// Le type de document doit avoir un support au moins 
		if ( !$typeDoc['ind_physique'] && !$typeDoc['ind_numerique']) {
			$this->Flash->error(__('Sélectionner au moins un support pour ce type de documents.'));
			return false;
		}
		
		// Dans tous les autres cas, tout va bien : */
		
		// Fonction désactivée suite à évolution sur les types de supports
		return true;
	}

}
