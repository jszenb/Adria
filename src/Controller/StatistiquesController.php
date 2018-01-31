<?php
namespace App\Controller;

use App\Controller\AppController;


/**
 * Statistiques Controller
 *
 * @property \App\Model\Table\Fonds $Fonds
 */
class StatistiquesController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        
        $this->set('fonds', $this->Fonds->find('all'));
        $query = $this->Fonds->find();
$query->select(['Fonds.id', $query->func()->count('Fonds.id')])
    ->group(['type_fond_id']);
$total = $query->count();
$this->set('total', total);

    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {

    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {

    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {

    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {

    }

	/**
     * isAuthorized method
     *
     * @param string|null $user user en cours dans la session
     * @return true or false
     * @throws nothing
     */
	public function isAuthorized($user)
	{
		/**********************************************************************
		* Règles d'autorisation :
		* Le user CC a tout les droits : c'est le contrôleur App général qui 
		* règle son cas (appel en fin de fonction).
		* Le user CA peut :
		*     - dresser la liste des users (index)
		*     - consulter tous les users (view)
		*     - modifier son propre user (edit)
		*     - ne peut pas ajouter un user (add)
		*     - ne peut pas supprimer un user
		**********************************************************************/

		if (isset($user['type_user_id']) && $user['type_user_id'] == PROFIL_CA) {

            return true; 
        }
		return parent::isAuthorized($user);	
    }
}