<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * EntiteDocsLieuConservation Entity.
 *
 * @property int $id
 * @property int $entite_doc_id
 * @property \App\Model\Entity\EntiteDoc $entite_doc
 * @property int $lieu_conservation_id
 * @property \App\Model\Entity\LieuConservation $lieu_conservation
 */
class EntiteDocsLieuConservation extends Entity
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
