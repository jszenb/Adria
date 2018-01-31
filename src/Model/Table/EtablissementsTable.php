<?php
namespace App\Model\Table;

use App\Model\Entity\Etablissement;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Etablissements Model
 *
 * @property \Cake\ORM\Association\HasMany $EntiteDocs
 */
class EtablissementsTable extends Table
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

        $this->table('etablissements');
        $this->displayField('code');
        $this->primaryKey('id');

        $this->hasMany('EntiteDocs', [
            'foreignKey' => 'etablissement_id'
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
            ->requirePresence('code', 'create')
            ->notEmpty('code')
            ->add('code', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->requirePresence('adresse_1', 'create')
            ->notEmpty('adresse_1');

        $validator
            ->allowEmpty('adresse_2');

        $validator
            ->allowEmpty('adresse_3');

        $validator
            ->requirePresence('adresse_cp', 'create')
            ->notEmpty('adresse_cp');

        $validator
            ->requirePresence('adresse_ville', 'create')
            ->notEmpty('adresse_ville');

        $validator
            ->allowEmpty('adresse_pays');

        $validator
            ->allowEmpty('num_tel')
			->add('num_tel', 'formatFR', [
					'rule' => function ($value, $context)  {
						if (!preg_match('/^0[1-9]([.][0-9]{2}){4}$/', $value)) {
							return false;
						}
						else {
							return true;
						}
					},
					'message' => 'Le numéro de téléphone doit respecter le format xx.xx.xx.xx.xx',
				]);			

        $validator
            ->allowEmpty('mail')
			->add('mail', 'validFormat', [
				'rule' => 'email',
				'message' => 'Saisissez une adresse électronique valide'
			]);

        return $validator;
    }
}
