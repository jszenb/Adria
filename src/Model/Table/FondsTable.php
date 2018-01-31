<?php
namespace App\Model\Table;

use App\Model\Entity\Fond;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use
    Cake\Event\Event,
    ArrayObject;

/**
 * Fonds Model
 *
 * @property \Cake\ORM\Association\BelongsTo $EntiteDocs
 * @property \Cake\ORM\Association\BelongsTo $TypeFonds
 * @property \Cake\ORM\Association\BelongsTo $TypeTraitements
 * @property \Cake\ORM\Association\BelongsTo $TypeNumerisations
 * @property \Cake\ORM\Association\BelongsTo $TypeInstrRechs
 * @property \Cake\ORM\Association\BelongsTo $TypeStatJurids
 * @property \Cake\ORM\Association\BelongsTo $TypeEntrees
 * @property \Cake\ORM\Association\BelongsTo $TypeDocAfferents
 * @property \Cake\ORM\Association\BelongsTo $TypeAccroissements
 * @property \Cake\ORM\Association\BelongsTo $TypePriseEnCharges
 * @property \Cake\ORM\Association\BelongsTo $TypeRealisationTraitements
 * @property \Cake\ORM\Association\BelongsTo $RaisonSuppressions
 * @property \Cake\ORM\Association\BelongsToMany $AireCulturelles
 * @property \Cake\ORM\Association\BelongsToMany $LieuConservations
 * @property \Cake\ORM\Association\BelongsToMany $Thematiques
 * @property \Cake\ORM\Association\BelongsToMany $TypeConditionnements
 * @property \Cake\ORM\Association\BelongsToMany $TypeDocAfferents
 * @property \Cake\ORM\Association\BelongsToMany $TypeDocs
 * @property \Cake\ORM\Association\BelongsToMany $TypeSupports
 * @property \Cake\ORM\Association\HasMany $Adresses 
 
 */
class FondsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('fonds');
        $this->displayField('nom');
        $this->primaryKey('id');

        $this->belongsTo('EntiteDocs', [
            'foreignKey' => 'entite_doc_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('TypeFonds', [
            'foreignKey' => 'type_fond_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('TypeTraitements', [
            'foreignKey' => 'type_traitement_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('TypeNumerisations', [
            'foreignKey' => 'type_numerisation_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('TypeInstrRechs', [
            'foreignKey' => 'type_instr_rech_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('TypeStatJurids', [
            'foreignKey' => 'type_stat_jurid_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('TypeEntrees', [
            'foreignKey' => 'type_entree_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('TypeDocAfferents', [
            'foreignKey' => 'type_doc_afferent_id'
        ]);
        $this->belongsTo('TypeAccroissements', [
            'foreignKey' => 'type_accroissement_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('TypePriseEnCharges', [
            'foreignKey' => 'type_prise_en_charge_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('TypeRealisationTraitements', [
            'foreignKey' => 'type_realisation_traitement_id',
            'joinType' => 'INNER'
        ]);		
        $this->belongsTo('RaisonSuppressions', [
            'foreignKey' => 'raison_suppression_id'
        ]);
        $this->belongsToMany('AireCulturelles', [
            'foreignKey' => 'fond_id',
            'targetForeignKey' => 'aire_culturelle_id',
            'joinTable' => 'aire_culturelles_fonds'
        ]);
        $this->belongsToMany('LieuConservations', [
            'foreignKey' => 'fond_id',
            'targetForeignKey' => 'lieu_conservation_id',
            'joinTable' => 'fonds_lieu_conservations'
        ]);
        $this->belongsToMany('Thematiques', [
            'foreignKey' => 'fond_id',
            'targetForeignKey' => 'thematique_id',
            'joinTable' => 'fonds_thematiques'
        ]);
        $this->belongsToMany('TypeConditionnements', [
            'foreignKey' => 'fond_id',
            'targetForeignKey' => 'type_conditionnement_id',
            'joinTable' => 'fonds_type_conditionnements'
        ]);
        $this->belongsToMany('TypeDocAfferents', [
            'foreignKey' => 'fond_id',
            'targetForeignKey' => 'type_doc_afferent_id',
            'joinTable' => 'fonds_type_doc_afferents'
        ]);
        $this->belongsToMany('TypeDocs', [
            'foreignKey' => 'fond_id',
            'targetForeignKey' => 'type_doc_id',
            'joinTable' => 'fonds_type_docs'
        ]);
        $this->belongsToMany('TypeSupports', [
            'foreignKey' => 'fond_id',
            'targetForeignKey' => 'type_support_id',
            'joinTable' => 'fonds_type_supports'
        ]);		

        $this->hasMany('Adresses', [
			 'setForeignKey' => 'fond_id',
			 'sort' => 'num_seq'
		]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('nom', 'create')
            ->notEmpty('nom');
			
        $validator
            ->add('annee_deb', [
					'valideYear' => [
						'rule' => 'validateYear',	
						'provider' => 'table',
						'message' => 'Saisissez une année valide (entre 1800 et l\'année en cours plus un siècle maximum)']
					])
            ->allowEmpty('annee_deb');	
			
					
        $validator
            ->add('annee_fin', [
					'valideYear' => [
						'rule' => 'validateYear',	
						'provider' => 'table',
						'message' => 'Saisissez une année valide (entre 1800 et l\'année en cours plus un siècle maximum)']
					])
            ->allowEmpty('annee_fin');				

        $validator
            ->allowEmpty('cote');

        $validator
            ->requirePresence('producteur', 'create')
            ->notEmpty('producteur');

        $validator
            ->allowEmpty('historique');

        $validator
            ->add('ind_bib', 'valid', ['rule' => 'boolean'])
            ->allowEmpty('ind_bib');
			
        $validator
            ->allowEmpty('url_collection');			

        $validator
            ->allowEmpty('precision_bib');

        $validator
            ->add('ind_nb_ml', 'valid', ['rule' => 'boolean'])
            ->allowEmpty('ind_nb_ml');

        $validator
            ->add('nb_ml', 'valid', [
				'rule' => 'numeric',
				'message' => 'Saisissez une valeur numérique (le séparateur décimale est le point ; deux décimales maximum après le point. Vérifiez que vous n\'avez pas saisi d\'espace avant ou après les chiffres)'])
            ->allowEmpty('nb_ml');

        $validator
            ->add('ind_nb_go', 'valid', ['rule' => 'boolean'])
            ->allowEmpty('ind_nb_go');

        $validator
            ->add('nb_go', 'valid', [
				'rule' => 'numeric',
				'message' => 'Saisissez une valeur numérique (le séparateur décimale est le point ; deux décimales maximum après le point. Vérifiez que vous n\'avez pas saisi d\'espace avant ou après les chiffres)'])
            ->allowEmpty('nb_go');

        $validator
            ->allowEmpty('observations');

        $validator
            ->add('dt_creation', 'valid', ['rule' => 'date'])
            ->allowEmpty('dt_creation');

        $validator
            ->add('dt_der_modif', 'valid', ['rule' => 'date'])
            ->allowEmpty('dt_der_modif');

        $validator
            ->add('dt_suppr', 'valid', ['rule' => 'date'])
            ->allowEmpty('dt_suppr');

        $validator
            ->add('ind_suppr', 'valid', ['rule' => 'boolean'])
            ->allowEmpty('ind_suppr');
			
        $validator
            ->add('dt_deb_prestation', 'valid', ['rule' => 'date'])
            ->allowEmpty('dt_deb_prestation');	

        $validator
            ->add('dt_fin_prestation', 'valid', ['rule' => 'date'])
            ->allowEmpty('dt_fin_prestation')
			->add('dt_fin_prestation', ['valideDateFinPrestation' => [
						'rule' => 'valideDateFinPrestation',	
						'provider' => 'table',
						'message' => 'La date de fin de la prestation ne peut pas être antérieure à sa date de début.']
					]);
			
        $validator
            ->requirePresence('entite_doc_id', ['message' => 'Ce champ est obligatoire']);
			
        $validator
            ->requirePresence('type_fond_id');

        $validator
            ->requirePresence('type_stat_jurid_id');			

        $validator
            ->requirePresence('type_entree_id');					

        $validator
            ->requirePresence('type_accroissement_id');	
			
        $validator
            ->requirePresence('type_prise_en_charge_id');	

        $validator
            ->requirePresence('type_realisation_traitement_id');				
			
        $validator
            ->requirePresence('type_traitement_id');				
		
        $validator
            ->requirePresence('type_numerisation_id');		

        $validator
            ->requirePresence('type_instr_rech_id');	

        $validator
            ->allowEmpty('url_instr_rech');		

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['entite_doc_id'], 'EntiteDocs'));
        $rules->add($rules->existsIn(['type_fond_id'], 'TypeFonds'));
        $rules->add($rules->existsIn(['type_traitement_id'], 'TypeTraitements'));
        $rules->add($rules->existsIn(['type_numerisation_id'], 'TypeNumerisations'));
        $rules->add($rules->existsIn(['type_instr_rech_id'], 'TypeInstrRechs'));
        $rules->add($rules->existsIn(['type_stat_jurid_id'], 'TypeStatJurids'));
        $rules->add($rules->existsIn(['type_entree_id'], 'TypeEntrees'));
        $rules->add($rules->existsIn(['type_doc_afferent_id'], 'TypeDocAfferents'));
        $rules->add($rules->existsIn(['type_accroissement_id'], 'TypeAccroissements'));
        $rules->add($rules->existsIn(['raison_suppression_id'], 'RaisonSuppressions'));
        return $rules;
    }
	
	public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options) {
		// On met tout au format deux décimales après la virgule
		if (is_numeric($data['nb_ml'])) {
			$data['nb_ml'] = number_format($data['nb_ml'], 2);
		}
		if (is_numeric($data['nb_go'])) {
			$data['nb_go'] = number_format($data['nb_go'], 2);
		}
		//dump($data['nb_ml']);		
		//dump($data['nb_go']);	
	}
	
	public function validateYear($value, $context) {
		if (is_numeric($value) && ($value >= 1800) && ($value <= (date('Y') + 100))) 
			return true;
		else
			return false;
	}
	
	public function valideDateFinPrestation($value, $context) {
		// Ju'utilise - pour la converion strtotime : en principe il comprend alors que c'est la format européen DD-MM-YYYY 
		$dateDeb = strtotime($context['data']['dt_deb_prestation']['day'] . '-' . $context['data']['dt_deb_prestation']['month'] . '-' .$context['data']['dt_deb_prestation']['year']) ;
		$dateFin = strtotime($context['data']['dt_fin_prestation']['day'] . '-' . $context['data']['dt_fin_prestation']['month'] . '-' .$context['data']['dt_fin_prestation']['year']) ;
		//dump($dateDeb);
		//dump($dateFin);

		if (($dateFin-$dateDeb) < 0)
			// La date de fin est inférieur à la date de début : c'est impossible
			return false;
		else
			return true;
	}	
	
}
