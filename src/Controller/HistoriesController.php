<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Histories Controller
 *
 *
 * @method \App\Model\Entity\History[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class HistoriesController extends AppController
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


    public function index($user_id)
    {
        $user = $this->Auth->user();
        $histories = $this->loadModel('Registers')->getHistories($user_id);

        $this->set(compact('histories','user','user_id'));
    }

    public function view($id)
    {
        $user = $this->Auth->user();

        //選択した項目のregisters.idを使ってregieters,checksのデータ取得
        $registers_tmp = $this->loadModel('Registers')->getRegisters($id);

        $checks_tmp = $this->loadModel('Checks')->getChecks($registers_tmp[0]['checks']['id']);
      
        $title = $registers_tmp[0]['checks']['title'];
        $created = $registers_tmp[0]['created'];

        //$checks_tmp $registers_tmpの中から、それぞれcheck_1~9, register_1~9のNULL以外のデータ抽出
        $checks_data = array();
        $registers_data = array();
        for($i=1;$i<10;$i++) {
            if($checks_tmp[0]['check_'.$i] !== NULL) {
                $checks_data[] = $checks_tmp[0]['check_'.$i];
            }
            if($registers_tmp[0]['register_'.$i] !== NULL) {
                $registers_data[] = $registers_tmp[0]['register_'.$i];
            }
        }


        $this->set(compact('checks_data',
                           'registers_data',
                           'title',
                           'created'));

    }

    
    public function delete($id = null)
    {
        $user = $this->Auth->user();
        $this->request->allowMethod(['get', 'delete']);
        $history = $this->loadModel('Registers')->get($id);
        if ($this->loadModel('Registers')->delete($history)) {
            $this->Flash->success('削除しました');
        } else {
            $this->Flash->error('削除失敗');
        }

        return $this->redirect(['action' => 'index',$user['id']]);
    }

    public function deleteall() {
        $user = $this->Auth->user();

        $this->request->allowMethod(['get', 'delete']);

        if($this->loadModel('Registers')->deleteAllRegistry($user['id'])) {
            $this->Flash->success('削除しました');
        } else {
            $this->Flash->error('削除失敗');
        }

        $this->redirect([
            'action'=>'index',
            $user['id']
        ]);
    }
}
