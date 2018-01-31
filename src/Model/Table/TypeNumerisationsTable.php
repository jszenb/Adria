<?php
namespace App\Model\Table;

use App\Model\Entity\TypeNumerisation;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TypeNumerisations Model
 *
 * @property \Cake\ORM\Association\HasMany $Fonds
 */
class TypeNumerisationsTable extends Table
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

        $this->table('type_numerisations');
        $this->displayField('type');
        $this->primaryKey('id');

        $this->hasMany('Fonds', [
            'foreignKey' => 'type_numerisation_id'
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
            ->requirePresence('type', 'create')
            ->notEmpty('type');

        $validator
            ->allowEmpty('description');

        return $validator;
    }
}
