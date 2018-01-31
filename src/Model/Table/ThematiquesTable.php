<?php
namespace App\Model\Table;

use App\Model\Entity\Thematique;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Thematiques Model
 *
 * @property \Cake\ORM\Association\BelongsToMany $Fonds
 */
class ThematiquesTable extends Table
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

        $this->table('thematiques');
        $this->displayField('intitule');
        $this->primaryKey('id');

        $this->belongsToMany('Fonds', [
            'foreignKey' => 'thematique_id',
            'targetForeignKey' => 'fond_id',
            'joinTable' => 'fonds_thematiques'
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
