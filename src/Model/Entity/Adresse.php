<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Adresse Entity.
 *
 * @property int $id
 * @property int $num_seq
 * @property float $volume
 * @property string $magasin
 * @property int $epi_deb
 * @property int $epi_fin
 * @property int $travee_deb
 * @property int $travee_fin
 * @property int $tablette_deb
 * @property int $tablette_fin
 */
class Adresse extends Entity
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
