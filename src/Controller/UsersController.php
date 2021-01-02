<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;


/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 *
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
        $this->Auth->allow(['add', 'login']);
    }

    public function isAuthorized($user = null){
        if($user['role'] === 'admin'){
           return true;
        }
        if($user['role'] === 'user'){
           return true;
        }
        return false;
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */

    public function add() {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success('登録しました');
                $user = $this->Auth->identify();
                $this->Auth->setUser($user);

                return $this->redirect([
                    'controller'=>'checks',
                    'action' => 'index'
                    ]);
            }
            $this->Flash->error('登録できませんでした。もう一度実行してください');
        }
        $this->set(compact('user'));
    }

    public function login() {
        $user = $this->Users->newEntity();
        if($this->request->isPost()) {
            $user = $this->Auth->identify();   
            if(!empty($user) && $user['role'] !== 'admin'){
                $this->Auth->setUser($user);
                return $this->redirect('/checks/index');
            } else if(!empty($user) && $user['role'] === 'admin') {
                $this->Auth->setUser($user);
                return $this->redirect('/admin/users');
            }
            $this->Flash->error('ユーザー名かパスワードに誤りがあります。');
        }
        $this->set(compact('user'));
    }

    public function logout() 
    {
        $this->request->session()->destroy();
        return $this->redirect('/users/login');
    }

  


}
