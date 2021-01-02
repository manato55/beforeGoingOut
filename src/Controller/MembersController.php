<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Members Controller
 *
 * @property \App\Model\Table\MembersTable $Members
 *
 * @method \App\Model\Entity\Member[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MembersController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function isAuthorized($user = null){
        if($user['role'] === 'admin'){
           return true;
        }
        if($user['role'] === 'user'){
           return true;
        }
        return false;
    }

    public function join($id) {
        $user = $this->Auth->user();

        if($this->Members->joinToGroup($id, $user['id'])) {
            $this->Flash->success('参加しました');
            $this->redirect([
                'controller'=>'groups',
                'action' => 'index'
            ]);
        } else {
            $this->Flash->error('失敗しました。再度参加してください');
            $this->redirect([
                'controller'=>'groups',
                'action' => 'index'
            ]);
        }
       
    }
    
}
