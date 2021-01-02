<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Rule\IsUnique;
use Cake\ORM\TableRegistry;

/**
 * Members Model
 *
 * @property \App\Model\Table\GroupsTable&\Cake\ORM\Association\BelongsTo $Groups
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\Member get($primaryKey, $options = [])
 * @method \App\Model\Entity\Member newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Member[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Member|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Member saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Member patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Member[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Member findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MembersTable extends Table
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

        $this->setTable('members');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Groupings')
            ->setForeignKey('grouping_id');
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('role')
            ->allowEmptyString('role');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    // public function buildRules(RulesChecker $rules)
    // {
    //     $rules->add($rules->existsIn(['group_id'], 'Groups'));
    //     $rules->add($rules->existsIn(['user_id'], 'Users'));

    //     return $rules;
    // }

    public function searchGroup($name,$pass) {
        $row = $this->find()
            ->join([
                'table'=>'Groupings',
                'type'=> 'INNER',
                'conditions' => 'Groupings.id = Members.grouping_id'
            ])->where([
                'groupname' => $name
            ])->select([
                'groupings.id',
                'groupings.groupname',
                'groupings.password',
                'members.user_id',
                'members.role'
            ])
            ->first();

        // if($row !== NULL) {
        //     if((new DefaultPasswordHasher())->check($pass, $row['groups']['password'])) {
        //         $result = $this->find()  
        //             ->join([
        //                 'Groups' => [
        //                     'table' => 'groups',
        //                     'type' => 'INNER',
        //                     'conditions' => 'groups.id = members.group_id'
        //                 ],
        //                 'Users' => [
        //                     'table' => 'users',
        //                     'type' => 'INNER',
        //                     'conditions' => 'users.id = members.user_id'
        //                 ]
        //             ])->where([
        //                 'groups.id' => $row['groups']['id']
        //             ])->select([
        //                 'groups.id',
        //                 'groups.groupname',
        //                 'members.user_id',
        //                 'members.role',
        //                 'users.username'
        //             ])->toArray();
        //     } else {
        //         $result = NULL;
        //     }
        // } else {
        //     $result = NULL;
        // }
        
        //上記のIF文を三項演算子で書くと
        $result = ($row !== NULL) 
            ? (new DefaultPasswordHasher())->check($pass, $row['groupings']['password']) 
            ? $this->find()  
                ->join([
                    'Groupings' => [
                        'table' => 'groupings',
                        'type' => 'INNER',
                        'conditions' => 'groupings.id = members.grouping_id'
                    ],
                    'Users' => [
                        'table' => 'users',
                        'type' => 'INNER',
                        'conditions' => 'users.id = members.user_id'
                    ]
                ])->where([
                    'groupings.id' => $row['groupings']['id']
                ])->select([
                    'groupings.id',
                    'groupings.groupname',
                    'members.user_id',
                    'members.role',
                    'users.username'
                ])->toArray()
            : NULL
            : NULL;

        return $result;
    }

    public function joinToGroup($id, $login_user_id) {
        $member = $this->newEntity();

        $member->group_id = $id;
        $member->user_id = $login_user_id;
        //後から参加したメンバーのroleは「member」とする
        $member->role = 'member';

        return $this->save($member);

    }

    public function getEnrolledIndex($login_user_id) {
        $data = $this->find()
            ->join([
                'Groupings'=> [
                    'table' => 'groupings',
                    'type' => 'INNER',
                    'conditions' => 'groupings.id = members.grouping_id'
                ],
            ])->where([
                'members.user_id' => $login_user_id
            ])->select([
                'groupings.groupname',
                'groupings.id',
                'members.role'
            ])->toArray();
            

        $user = array();
        for($i=0;$i<count($data);$i++) {
            $user[] = $this->find()
                ->join([
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => 'users.id = members.user_id'
                ])->select([
                    'users.username',
                    'users.id',
                ])->where([
                    'members.grouping_id' => $data[$i]['groupings']['id']
                ])->toArray();
        }

        return array($data,$user);
    }

    public function showAllGroups() {
        $query = $this->find()->func();
        $data = $this->find()
            ->join([
                'Groupings'=> [
                    'table' => 'groupings',
                    'type' => 'INNER',
                    'conditions' => 'groupings.id = members.grouping_id'
                ],
                'Users' => [
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => 'users.id = members.user_id'
                ]
            ])->select([
                'groupname'=> 'groupings.groupname',
                'username' => $this->find()->func()->group_concat(['users.username' => 'identifier']),
                'user_id' => $query->cast($query->group_concat(['users.id' => 'identifier']), 'NCHAR'),
            ])->group([
                'groupings.id'
            ])->toArray();

        return $data;

    }

   
}
