<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
		$this->loadComponent('Auth', [
			'authorize' => ['Controller'],
            'loginRedirect' => [
                'controller' => 'Fonds',
                'action' => 'index'
            ],		
            'authenticate' => [
                'Form' => [
                    'fields' => [
                        'username' => 'login',
                        'password' => 'password'
                    ]
                ]
            ],
            'loginAction' => [
                'controller' => 'Users',
                'action' => 'login'
            ],
			'authError' => 'Vous ne disposez pas des droits suffisants pour cette action.'
        ]);		
        // Autorise l'action display pour que notre controller de pages
        // continue de fonctionner.
        $this->Auth->allow(['display']);	
		$this->Auth->allow(['logout']);

    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return void
     */
    public function beforeRender(Event $event)
    {
        if (!array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->type(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }
    }
	
    /**
     * isAuthorized
     * Gestion des droits sur l'application et ses actions
     * @param $user l'utilisateur en cours
     * @return true/false
     */	
	public function isAuthorized($user)
	{
		// L'utilisateur CC peut tout faire. 
		if (isset($user['type_user_id']) && $user['type_user_id'] == PROFIL_CC) {
			return true;
		}

		// L'utilisateur CO ne peut que consulter.
		if (isset($user['type_user_id']) && $user['type_user_id'] == PROFIL_CO) {
			
			if (isset($this->request->params['action'])) {
				if (in_array($this->request->params['action'], ['index','view','recherche','statistiques', 'generatepdf', 'generatecsv', 'generaterapports'])) {
					return true;
				}
				else {
					return false;
				}
			}
		}
		
		// Default deny
		return false;
	}	

}
