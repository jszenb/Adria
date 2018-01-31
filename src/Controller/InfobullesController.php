<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;


/**
 * Infobulles Controller
 *
 */
class InfobullesController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        

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
		$rubrique = $this->request->query('rubrique');
		$titre = $this->request->query('titre');
		$table = TableRegistry::get($rubrique);
		
		if ($titre == 'Disciplines' || $titre == 'Aires culturelles') {
			$resultats = $table->find('all', ['order' => ['intitule' => 'ASC']]);
		}
		else {
			$resultats = $table->find('all', ['order' => ['type' => 'ASC']]);
		}
		
		$this->set(compact('resultats', 'titre'));
		$this->viewBuilder()->autoLayout(false);
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