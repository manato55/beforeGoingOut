<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Routing\Router;
use Cake\ORM\TableRegistry;

/**
 * Registers Model
 *
 * @property \App\Model\Table\ChecksTable&\Cake\ORM\Association\BelongsTo $Checks
 *
 * @method \App\Model\Entity\Register get($primaryKey, $options = [])
 * @method \App\Model\Entity\Register newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Register[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Register|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Register saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Register patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Register[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Register findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class RegistersTable extends Table
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

        $this->setTable('registers');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        //リレーション設定する場合、子テーブルのフォーリンキーは「親テーブル名＋アンダースコア＋id」にしなければいけない
        $this->belongsTo('Checks')
            ->setForeignKey('check_id');

        // parent::initialize();
        $this->Checks = TableRegistry::get('checks');
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

        // $validator
        //     ->scalar('check_id')
        //     ->allowEmptyString('check_id');

        $validator
            ->scalar('register_1')
            ->allowEmptyString('register_1');

        $validator
            ->scalar('register_2')
            ->allowEmptyString('register_2');

        $validator
            ->scalar('register_3')
            ->allowEmptyString('register_3');

        $validator
            ->scalar('register_4')
            ->allowEmptyString('register_4');

        $validator
            ->scalar('register_5')
            ->allowEmptyString('register_5');

        $validator
            ->scalar('register_6')
            ->allowEmptyString('register_6');

        $validator
            ->scalar('register_7')
            ->allowEmptyString('register_7');

        $validator
            ->scalar('register_8')
            ->allowEmptyString('register_8');

        $validator
            ->scalar('register_9')
            ->allowEmptyString('register_9');

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
        $rules->add($rules->existsIn(['check_id'], 'Checks'));

        return $rules;
    }

    public function registerChecksToRegisters() {
        $data = Router::getRequest()->getData();
        $registers = $this->newEntity();

        //$dataにはidが含まれているため、その分を−１してループ処理
        for($i=0;$i<count($data)-1;$i++) {
            //カラム「register」は１〜９のため０は含まない。そのため$iに＋１している
            $register = 'register_'.($i+1);
            $registers->{$register} = $data['check_'.$i];
        }
        $registers->check_id = $data['id'];

        return $this->save($registers);

    }

    public function getHistories($login_user_id) {
        $data = $this->find()
            ->join([
                'table' => 'Checks',
                'type' => 'INNER',
                'conditions' => 'Checks.id = Registers.check_id'
            ])->where([
                'Checks.user_id' => $login_user_id
            ])->select([
                'Checks.title',
                'Registers.created',
                'Checks.id',
                'regist_id' => 'Registers.id',
                'Checks.check_1',
                'Checks.check_2',
                'Checks.check_3',
                'Checks.check_4',
                'Checks.check_5',
                'Checks.check_6',
                'Checks.check_7',
                'Checks.check_8',
                'Checks.check_9',
                'register_1',
                'register_2',
                'register_3',
                'register_4',
                'register_5',
                'register_6',
                'register_7',
                'register_8',
                'register_9',
            ])->order([
                'Registers.created'=> 'DESC'
            ])->toArray();

            return $data;
    }

    public function getRegisters($id) {
        $registers_data = $this->find()->where([
            'registers.id' => $id
         ])->join([
             'table' => 'Checks',
             'type' => 'INNER',
             'conditions' => 'Checks.id = Registers.check_id' 
         ])
         ->select([
            'checks.id',
            'checks.title',
            'register_1',
            'register_2',
            'register_3',
            'register_4',
            'register_5',
            'register_6',
            'register_7',
            'register_8',
            'register_9',
            'created'
         ])->toArray();
 
         return $registers_data;
              
     }

     public function deleteAllRegistry($login_user_id) {
        $regist_id = $this->Checks->find()
            ->join([
                'Registers'=> [
                    'table' => 'Registers',
                    'type' => 'INNER',
                    'conditions' => 'Checks.id = Registers.check_id'
                ],
                'users'=> [
                    'table' => 'Users',
                    'type' => 'INNER',
                    'conditions' => 'Checks.user_id = Users.id'
                ]
            ])->where([
                'users.id' => $login_user_id
            ])->select([
                'Registers.id'
            ])->toArray();
        
        for($i=0;$i<count($regist_id);$i++) {
            $entity = $this->get($regist_id[$i]['Registers']['id']);
            $result[] = $this->delete($entity);
        }

        return $result;

    }

}