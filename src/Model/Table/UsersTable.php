<?php
namespace App\Model\Table;

use App\Model\Entity\User;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property \Cake\ORM\Association\BelongsTo $EntiteDocs
 * @property \Cake\ORM\Association\BelongsTo $TypeUsers
 */
class UsersTable extends Table
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

        $this->table('users');
        $this->displayField('nom');
        $this->primaryKey('id');

        $this->belongsTo('EntiteDocs', [
            'foreignKey' => 'entite_doc_id'
        ]);
        $this->belongsTo('TypeUsers', [
            'foreignKey' => 'type_user_id',
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
            ->requirePresence('login', 'create')
            ->notEmpty('login')
            ->add('login', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->requirePresence('password', 'create')
            ->notEmpty('password')
			->add('password', 'minLength', ['rule' => ['minLength', 6],'message' => 'Le mot de passe doit comporter au moins 6 caractères.']);

        $validator
            ->requirePresence('nom', 'create')
            ->notEmpty('nom');

        $validator
            ->requirePresence('prenom', 'create')
            ->notEmpty('prenom');

        $validator
            ->requirePresence('mail', 'create')
            ->notEmpty('mail')
			->add('mail', 'validFormat', [
				'rule' => 'email',
				'message' => 'Saisissez une adresse électronique valide'
			]);		

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
            ->requirePresence('type_user_id', 'create');

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
        $rules->add($rules->isUnique(['login']));
        $rules->add($rules->existsIn(['entite_doc_id'], 'EntiteDocs'));
        $rules->add($rules->existsIn(['type_user_id'], 'TypeUsers'));
        return $rules;
    }
}
