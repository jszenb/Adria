<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TypeFond Entity.
 *
 * @property int $id
 * @property string $type
 * @property int $num_seq
 * @property string $description
 * @property string $couleur 
 * @property \App\Model\Entity\Fond[] $fonds
 */
class TypeFond extends Entity
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
}
