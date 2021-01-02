<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Registers Controller
 *
 *
 * @method \App\Model\Entity\Register[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class RegistersController extends AppController
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

    public function submitchecks() {
        if($this->Registers->registerChecksToRegisters()) {
            $this->Flash->success('登録しました');
        } else {
            $this->Flash->error('登録失敗');
        }
        $this->redirect([
            'controller' => 'checks',
            'action' => 'index'
        ]);
    }

    
}
