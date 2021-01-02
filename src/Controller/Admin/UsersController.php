<?php
namespace App\Controller\Admin;

use App\Controller\AppController;

/**
 * Checks Controller
 *
 * @property \App\Model\Table\ChecksTable $Checks
 *
 * @method \App\Model\Entity\Check[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
   

    public function isAuthorized($user = null){
       
        if($user['role'] === 'admin') {
           return true;
        } else if($user['role'] === 'user') {
            $this->redirect('http://localhost:8765/users/logout');
        }
        
        return false;
    }

   public function index() {
       $allUsers = $this->Users->getAllUsers();
       $this->set(compact('allUsers'));
    }

    public function checksindex($user_id) {
        $user = $this->Users->get($user_id);

        $userChecks = $this->loadModel('Checks')->getIndividualChecksByAdmin($user_id);

        $this->set(compact('userChecks','user'));
    }

    public function refertoregisters($check_id) {
        $title = $this->loadModel('Checks')->get($check_id);

        if($this->loadModel('Checks')->getRegistersByAdmin($check_id) !== '') {
            $registers = $this->loadModel('Checks')->getRegistersByAdmin($check_id);
        } else {
            $registers = NULL;
        }

        $this->set(compact('registers','title'));
    }

    public function groupindex() {
        $index = $this->loadModel('members')->showAllGroups();
        
        $this->set(compact('index'));
    }
    
}
