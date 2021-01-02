<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;

/**
 * Checks Controller
 *
 * @property \App\Model\Table\ChecksTable $Checks
 *
 * @method \App\Model\Entity\Check[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ChecksController extends AppController
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

    public function index() {
        $user = $this->Auth->user();
        $checks = $this->Checks->newEntity();

        //ログインユーザーが登録しているタイトルを取得してセレクトボックスに表示する。
        $indices = $this->Checks->getOwnIndex($user['id'])->toArray();

        if(!empty($indices)) {
            foreach($indices as $index) {
                $options_title[] = $index['title'];
                $options_id[] = $index['id'];
            }
            $options = array_combine($options_id, $options_title);
        } else {
            $indices = NULL;
            $options = NULL;
        }

        //セレクトボックスを選択した後の処理。次の①も後処理。タイトルが「選択」のときは無効
        if($this->request->is('post') && $this->request->getData('title') !== "") {
            $data = $this->Checks->getChoice()->toArray();
        } else {
            $data = NULL;
        }

        //①レコードのcheck_1からcheck_9の中にNULLがあれば削除
        $choiced = array();
        if($data !== NULL) {
            for($i=1;$i<10;$i++) {
                $check = 'check_'.$i;
                if($data[0]->{$check} === NULL) {
                    unset($data[0]->{$check});
                } else {
                    $choiced[] = $data[0]->{$check};
                }
            }
            //$choicedの配列にはcheck_1からcheck_9までしか入っていないため、別途idを取得
            $id = $data[0]->id;
        } else {
            $id = NULL;
        }

        $this->set(compact('checks',
                           'indices',
                           'options',
                           'choiced',
                           'id',
                        ));
    }

    public function settings() {
        $check = $this->Checks->newEntity();    
        
        $this->set(compact('check'));

    }

    public function register() {
        $user = $this->Auth->user();
       
        if($this->Checks->setting($user['id'])) {
            $this->Flash->success('登録しました');
        } else {
            $this->Flash->error('登録失敗');
        }
        
        return $this->redirect(['action' => 'settings']);

    }

    public function deletepage() {
        $user = $this->Auth->user();
        $checks = $this->Checks->newEntity();

        //ログインユーザーが登録しているタイトルを取得してセレクトボックスに表示する。
        $indices = $this->Checks->getOwnIndex($user['id'])->toArray();

        if(!empty($indices)) {
            foreach($indices as $index) {
                $options_title[] = $index['title'];
                $options_id[] = $index['id'];
            }
            $options = array_combine($options_id, $options_title);
        } else {
            $indices = NULL;
            $options = NULL;
        }

        //セレクトボックスから選択されたときの処理
        if($this->request->is('post') && !empty($this->request->getData('title'))) {
            list($selectedTopic, $id) = $this->Checks->getSelectedTopicToDelete();
        } else {
            $selectedTopic = NULL;
            $id = NULL;
        }

        $this->set(compact('options',
                           'checks',
                           'selectedTopic',
                           'id'));
    }

    public function delete() {
        if($this->Checks->deleteChecksAndRegisters()) {
            $this->Flash->success('削除しました');
        } else {
            $this->Flash->error('削除失敗');
        }

        $this->redirect(['action' => 'deletepage']);

    }

}
