<?php
namespace App\Model\Table;

use App\Model\Entity\RaisonSuppression;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * RaisonSuppressions Model
 *
 * @property \Cake\ORM\Association\HasMany $Fonds
 */
class RaisonSuppressionsTable extends Table
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

        $this->table('raison_suppressions');
        $this->displayField('raison');
        $this->primaryKey('id');

        $this->hasMany('Fonds', [
            'foreignKey' => 'raison_suppression_id'
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
            ->requirePresence('raison', 'create')
            ->notEmpty('raison');

        $validator
            ->allowEmpty('description');

        return $validator;
    }
}
