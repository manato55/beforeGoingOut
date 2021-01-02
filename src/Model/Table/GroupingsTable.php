<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;




/**
 * Groups Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\HasMany $Users
 *
 * @method \App\Model\Entity\Group get($primaryKey, $options = [])
 * @method \App\Model\Entity\Group newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Group[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Group|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Group saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Group patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Group[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Group findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class GroupingsTable extends Table
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

        $this->setTable('groupings');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Members', [
            'dependent' => true,
            
        ]);


        $this->Members = TableRegistry::get('members');

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
            ->scalar('groupname')
            ->maxLength('groupname', 255)
            ->notEmptyString('groupname')
            ->add(
                'groupname', 
                [
                    'unique' => [
                        'rule' => 'validateUnique', 
                        'provider' => 'table', 
                        'message' => 'グループ名が既に登録されています'
                    ]
                ]
            );

        $validator
            ->scalar('password')
            ->notEmptyString('password')
            ->add('password', 'length', [
                'rule' => ['minLength', 8],
                'message' => '８文字以上の設定が必要です。',
            ]);

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['groupname'],[
            'message' => '既に使用されているアドレスです'
        ]));

        return $rules;
    }

    public function insertGroup($name, $pass, $login_user_id) {
        $group = $this->newEntity();
        $pass = (new DefaultPasswordHasher)->hash($pass);

        $group->groupname = $name;
        $group->password = $pass;

        //グループテーブルに登録して、登録したROWのIDを取得
        if($this->save($group)) {
            $id = $group->id;
        }

        //メンバテーブルにグループテーブル登録時に取得したIDと登録したユーザーを登録
        $member = $this->Members->newEntity();

        $member->grouping_id = $id;
        $member->user_id = $login_user_id;
        //登録したユーザーを「HOST」とする
        $member->role = 'host';

        return $this->Members->save($member);

    }

    public function checkDupicateGroupName($groupname) {
        $data = $this->find()->where([
            'groupname' => $groupname
        ])->first();
        
        return $data;
    }

    public function deleteGroup($id) {
        Router::getRequest()->allowMethod(['get', 'delete']);
        $data = $this->get($id);
       

        return $this->delete($data);
    }
}
