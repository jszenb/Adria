<?php
namespace App\Model\Table;

use App\Model\Entity\LieuConservation;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * LieuConservations Model
 *
 * @property \Cake\ORM\Association\BelongsToMany $EntiteDocs
 * @property \Cake\ORM\Association\BelongsToMany $Fonds
 */
class LieuConservationsTable extends Table
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

        $this->table('lieu_conservations');
        $this->displayField('nom');
        $this->primaryKey('id');

        $this->belongsToMany('EntiteDocs', [
            'foreignKey' => 'lieu_conservation_id',
            'targetForeignKey' => 'entite_doc_id',
            'joinTable' => 'entite_docs_lieu_conservations'
        ]);
        $this->belongsToMany('Fonds', [
            'foreignKey' => 'lieu_conservation_id',
            'targetForeignKey' => 'fond_id',
            'joinTable' => 'fonds_lieu_conservations'
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

        return $validator;
    }
}
