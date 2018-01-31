<?php
namespace App\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Entity;

/**
 * User Entity.
 *
 * @property int $id
 * @property string $login
 * @property string $password
 * @property string $nom
 * @property string $prenom
 * @property string $mail
 * @property string $num_tel 
 * @property int $entite_doc_id
 * @property \App\Model\Entity\EntiteDoc $entite_doc
 * @property int $type_user_id
 * @property \App\Model\Entity\TypeUser $type_user
 */
class User extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
	
    protected function _setPassword($value)
    {
        $hasher = new DefaultPasswordHasher();
        return $hasher->hash($value);
    }	
}
