<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Fond Entity.
 *
 * @property int $id
 * @property string $nom
 * @property string $cote
 * @property int $annee_deb 
 * @property int $annee_fin
 * @property int $ind_annee
 * @property string $producteur
 * @property string $historique
 * @property bool $ind_bib
 * @property string $url_collection
 * @property string $precision_bib
 * @property bool $ind_nb_ml
 * @property bool $ind_nb_ml_inconnu 
 * @property float $nb_ml
 * @property bool $ind_nb_go
 * @property bool $ind_nb_go_inconnu  
 * @property float $nb_go
 * @property string $observations
 * @property int $entite_doc_id
 * @property \App\Model\Entity\EntiteDoc $entite_doc
 * @property int $type_fond_id
 * @property \App\Model\Entity\TypeFond $type_fond
 * @property int $type_traitement_id
 * @property \App\Model\Entity\TypeTraitement $type_traitement
 * @property int $type_numerisation_id
 * @property \App\Model\Entity\TypeNumerisation $type_numerisation
 * @property int $type_instr_rech_id
 * @property \App\Model\Entity\TypeInstrRech $type_instr_rech
 * @property string $url_instr_rech 
 * @property int $type_stat_jurid_id
 * @property \App\Model\Entity\TypeStatJurid $type_stat_jurid
 * @property int $type_entree_id
 * @property \App\Model\Entity\TypeEntree $type_entree
 * @property int $type_accroissement_id
 * @property \App\Model\Entity\TypeAccroissement $type_accroissement 
 * @property int $type_prise_en_charge_id
 * @property \App\Model\Entity\TypePriseEnCharge $type_prise_en_charge 
 * @property int $type_realisation_traitement_id
 * @property \App\Model\Entity\TypeRealisationTraitement $type_realisation_traitement
 * @property string $site_intervention
 * @property \Cake\I18n\Time $dt_deb_prestation
 * @property \Cake\I18n\Time $dt_fin_prestation
 * @property string $responsable_operation
 * @property \Cake\I18n\Time $dt_creation
 * @property \Cake\I18n\Time $dt_der_modif
 * @property \Cake\I18n\Time $dt_suppr
 * @property bool $ind_suppr
 * @property int $raison_suppression_id
 * @property int $ind_maj
 * @property int $stockage
 * @property int $communication
 * @property \App\Model\Entity\RaisonSuppression $raison_suppression
 * @property \App\Model\Entity\TypeDocAfferent[] $type_doc_afferents
 * @property \App\Model\Entity\AireCulturelle[] $aire_culturelles
 * @property \App\Model\Entity\LieuConservation[] $lieu_conservations
 * @property \App\Model\Entity\Thematique[] $thematiques
 * @property \App\Model\Entity\TypeConditionnement[] $type_conditionnements
 * @property \App\Model\Entity\TypeDoc[] $type_docs
 * @property \App\Model\Entity\TypeSupport[] $type_supports 
 */
class Fond extends Entity
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
