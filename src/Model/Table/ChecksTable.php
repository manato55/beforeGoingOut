<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Routing\Router;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;
use ArrayObject;
use Cake\Controller\Component\TrimComponent;



/**
 * Checks Model
 *
 * @method \App\Model\Entity\Check get($primaryKey, $options = [])
 * @method \App\Model\Entity\Check newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Check[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Check|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Check saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Check patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Check[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Check findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ChecksTable extends Table
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

        $this->setTable('checks');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Registers',[
            'dependent'=>true,
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
        $validator->provider('Custom', 'App\Model\Validation\CustomValidation');
        // dd($validator);

        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('check_1')
            ->maxLength('check_1', 255)
            ->allowEmptyString('check_1');

        $validator
            ->scalar('check_2')
            ->maxLength('check_2', 255)
            ->allowEmptyString('check_2');

        $validator
            ->scalar('check_3')
            ->maxLength('check_3', 255)
            ->allowEmptyString('check_3');

        $validator
            ->scalar('check_4')
            ->maxLength('check_4', 255)
            ->allowEmptyString('check_4');

        $validator
            ->scalar('check_5')
            ->maxLength('check_5', 255)
            ->allowEmptyString('check_5');

        $validator
            ->scalar('check_6')
            ->maxLength('check_6', 255)
            ->allowEmptyString('check_6');

        $validator
            ->scalar('check_7')
            ->maxLength('check_7', 255)
            ->allowEmptyString('check_7');

        $validator
            ->scalar('check_8')
            ->maxLength('check_8', 255)
            ->allowEmptyString('check_8');

        $validator
            ->scalar('check_9')
            ->maxLength('check_9', 255)
            ->allowEmptyString('check_9');


        return $validator;
    }
        
    public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options)
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = rtrim($value);
            }
        }
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['title'],[
            'message' => '既に使用されているアドレスです'
        ]));
        
        return $rules;

    }

    public function setting($login_user_id) {
        $checks = $this->newEntity();
        $checks = $this->patchEntity($checks, Router::getRequest()->getData());
        $data = Router::getRequest()->getData();
       
        if(Router::getRequest()->is('post')) {
            for($j=1;$j<count($data);$j++) {
                $check = 'check_'.$j;
                $checks->{$check} = $data['num_'.$j];
            }
            $checks->user_id = $login_user_id;
            $checks->title = $data['title'];

            return $this->save($checks);
        }

    }

    public function getOwnIndex($login_user_id) {
        $index = $this->find()->where([
            'user_id' => $login_user_id
        ]);

        return $index;
    }

    public function getChoice() {
        if(Router::getRequest()->is('post')) {
            $id = Router::getRequest()->getData('title');
            $choice = $this->find()->where([
                'id' => $id
            ]);

            return $choice;
        } 
    }

    public function getChecks($id) {
       $checks_data = $this->find()->where([
           'id' => $id
        ])->select([
            'title',
            'check_1',
            'check_2',
            'check_3',
            'check_4',
            'check_5',
            'check_6',
            'check_7',
            'check_8',
            'check_9',
        ])->toArray();

        return $checks_data;
             
    }

    public function deleteChecksAndRegisters() {
        Router::getRequest()->allowMethod(['post', 'delete']);
        $id = Router::getRequest()->getData('title');
        $del_row = $this->get($id);
        
        return $this->delete($del_row);
    }

    public function getSelectedTopicToDelete() {
        $id = Router::getRequest()->getData('title');
        $selectedTopic = $this->get($id)->toArray();

        $data = array();
        for($i=1;$i<10;$i++) {
            if($selectedTopic['check_'.$i] !== NULL) {
                $data[] = $selectedTopic['check_'.$i];
            }
        }

        return array($data,$id);

    }

    public function getIndividualChecksByAdmin($user_id) {
        $data = $this->find()
            ->where([
                'user_id' => $user_id
            ])->toArray();

        return $data;
    }

    public function getRegistersByAdmin($check_id) {
        $data = $this->find()
            ->where([
                'id' => $check_id
            ])->distinct('id')
            ->toArray();
           
    
       
        if(!empty($data)) {
            for($i=1;$i<10;$i++) {
                if($data[0]['check_'.$i] !== NULL) {
                    $unset_data[] = $data[0]['check_'.$i];
                }
            }
        } else {
            $unset_data = array();
        }
       
           

        return $unset_data;
    }

    
   
}
