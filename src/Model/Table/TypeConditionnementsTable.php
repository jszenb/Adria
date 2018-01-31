<?php
namespace App\Model\Table;

use App\Model\Entity\TypeConditionnement;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TypeConditionnements Model
 *
 * @property \Cake\ORM\Association\BelongsToMany $Fonds
 */
class TypeConditionnementsTable extends Table
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

        $this->table('type_conditionnements');
        $this->displayField('type');
        $this->primaryKey('id');

        $this->belongsToMany('Fonds', [
            'foreignKey' => 'type_conditionnement_id',
            'targetForeignKey' => 'fond_id',
            'joinTable' => 'fonds_type_conditionnements'
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
