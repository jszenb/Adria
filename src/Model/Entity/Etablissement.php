<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Etablissement Entity.
 *
 * @property int $id
 * @property string $nom
 * @property string $code
 * @property string $adresse_1
 * @property string $adresse_2
 * @property string $adresse_3
 * @property string $adresse_cp
 * @property string $adresse_ville
 * @property string $adresse_pays
 * @property string $num_tel
 * @property string $mail
 * @property \App\Model\Entity\EntiteDoc[] $entite_docs
 */
class Etablissement extends Entity
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
