<?php
namespace App\Model\Table;

use App\Model\Entity\TypeSupport;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TypeSupports Model
 *
 * @property \Cake\ORM\Association\BelongsToMany $Fonds
 */
class TypeSupportsTable extends Table
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

        $this->table('type_supports');
        $this->displayField('type');
        $this->primaryKey('id');

        $this->belongsToMany('Fonds', [
            'foreignKey' => 'type_support_id',
            'targetForeignKey' => 'fond_id',
            'joinTable' => 'fonds_type_supports'
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

        $validator
            ->allowEmpty('couleur');				

        return $validator;
    }
}
