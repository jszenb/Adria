<?php
namespace App\Model\Table;

use App\Model\Entity\Adresse;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use
    Cake\Event\Event,
    ArrayObject;

/**
 * Adresse Model
 *
 * @property \Cake\ORM\Association\belongsTo $Fonds
 */
class AdressesTable extends Table
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

        $this->table('adresses');
        $this->displayField('type');
        $this->primaryKey('id');

		$this->belongsTo('Fonds', [
            'foreignKey' => 'fond_id',
			'joinType' => 'INNER'
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
			->allowEmpty('volume')
            ->add('volume', 'valid', [
				'rule' => 'numeric',
				'message' => 'Saisissez une valeur numérique (le séparateur décimale est le point ; deux décimales maximum après le point. Vérifiez que vous n\'avez pas saisi d\'espace avant ou après les chiffres)'])		
			
            ->add('volume', [
					'checkVolumetrieMagasin' => [
						'rule' => 'checkVolumetrieMagasin',	
						'provider' => 'table',
						'message' => '' ]
					]);

        $validator
            ->allowEmpty('epi_deb')
			->range('epi_deb', [1, MAX_EPI], 'Le numéro d\'épi doit être compris entre 1 et ' . MAX_EPI);

        $validator
            ->allowEmpty('epi_fin')
			->range('epi_fin', [1, MAX_EPI], 'Le numéro d\'épi doit être compris entre 1 et ' . MAX_EPI)
			->add('epi_fin', 'epifaux', [
                'rule' => function($value, $context) {
                    return isset($context['data']['epi_fin']) &&
                     $context['data']['epi_deb'] <= $value;      
                },
                'message' => 'Erreur dans vos numéros d\'épis : vérifier que le premier numéro est inférieur au second.']);
			
        $validator
            ->allowEmpty('travee_deb')
			->range('travee_deb', [1, MAX_TRAVEE], 'Le numéro de travée doit être compris entre 1 et ' . MAX_TRAVEE);

        $validator
            ->allowEmpty('travee_fin')
			->range('travee_fin', [1, MAX_TRAVEE], 'Le numéro de travée doit être compris entre 1 et ' . MAX_TRAVEE)	
			->add('travee_fin', 'traveefaux', [
                'rule' => function($value, $context) {
                    return isset($context['data']['travee_fin']) &&
                     $context['data']['travee_deb'] <= $value;      
                },
                'message' => 'Erreur dans vos numéros de travée : vérifier que le premier numéro est inférieur au second.']);			

		$validator
            ->allowEmpty('tablette_deb')
			->range('tablette_deb', [1, MAX_TABLETTE], 'Le numéro de tablette doit être compris entre 1 et ' . MAX_TABLETTE);

        $validator
            ->allowEmpty('tablette_fin')
			->range('tablette_fin', [1, MAX_TABLETTE], 'Le numéro de tablette doit être compris entre 1 et ' . MAX_TABLETTE)
			->add('tablette_fin', 'tablettefaux', [
                'rule' => function($value, $context) {
                    return isset($context['data']['tablette_fin']) &&
                     $context['data']['tablette_deb'] <= $value;      
                },
                'message' => 'Erreur dans vos numéros de tablette : vérifier que le premier numéro est inférieur au second.']);			

		
        return $validator;
    }
	public function checkVolumetrieMagasin($value, $context) {
		
		$monMag = $context['data']['magasin'];
		
		if (isset($context['data']['volume']) && is_numeric($context['data']['volume'])) {
			$maVolumetrie = $context['data']['volume'];
		}
		else {
			$maVolumetrie = 0 ;
		}
		
		// Quelle est la volumétrie déjà stockée dans le magasin en cours ?
		$toutesLesVolumetries = $this->find();
		$toutesLesVolumetries->select(['sum' => $toutesLesVolumetries->func()->sum('volume')]);
		$toutesLesVolumetries->where(['magasin' => $monMag]);
		foreach($toutesLesVolumetries as $uneVolumetrie) {
			continue;
		}
		
		if (!isset($uneVolumetrie['sum']) || !is_numeric($uneVolumetrie['sum'])){
			$volumetrieExistanteMagasin = 0 ;
		}
		else {
			$volumetrieExistanteMagasin = $uneVolumetrie['sum'] ;
		}
		
		// On vérifie que l'ajout de la volumétrie saisie à la volumétrie déjà existante n'excède pas la capacité totale du magasin.
		$somme = $volumetrieExistanteMagasin + $maVolumetrie ;
		
		$restantDisponible = VOLUMETRIE_MAX_MAGASINS[$monMag] - $volumetrieExistanteMagasin;
		
		if ( $somme  > VOLUMETRIE_MAX_MAGASINS[$monMag]) {			
			$message = 'La volumétrie indiquée ajoutée à la volumétrie déjà prévue pour le magasin ' . $monMag . ' excède la volumétrie maximale autorisée pour ce magasin qui est de '. VOLUMETRIE_MAX_MAGASINS[$monMag] . ' mètres linéaires. Vous pouvez encore allouer ' . $restantDisponible . ' mètres linéaires dans ce magasin.' ;
			return $message;
		} else {
			return true;
		}
	}

	public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options) {
		// Si le volume est nul alors les autres valeurs sont vidées 
		if ($data['num_seq'] != '0'){
			if ($data['volume'] == ""){
				$data['magasin'] = "";
				$data['epi_deb'] = "";
				$data['epi_fin'] = "";
				$data['travee_deb'] = "";
				$data['travee_fin'] = "";
				$data['tablette_deb'] = "";
				$data['tablette_fin'] = "";
			}
		};
		
	}	
}
