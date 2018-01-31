<?php
namespace App\Model\Table;

use App\Model\Entity\AireCulturelle;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AireCulturelles Model
 *
 * @property \Cake\ORM\Association\BelongsToMany $Fonds
 */
class AireCulturellesTable extends Table
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

        $this->table('aire_culturelles');
        $this->displayField('intitule');
        $this->primaryKey('id');

        $this->belongsToMany('Fonds', [
            'foreignKey' => 'aire_culturelle_id',
            'targetForeignKey' => 'fond_id',
            'joinTable' => 'aire_culturelles_fonds'
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
            ->requirePresence('intitule', 'create')
            ->notEmpty('intitule');

        $validator
            ->allowEmpty('description');

        $validator
            ->allowEmpty('couleur');			
			
        return $validator;
    }
}
