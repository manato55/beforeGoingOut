<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Auth\DefaultPasswordHasher;


/**
 * Groups Controller
 *
 *
 * @method \App\Model\Entity\Group[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class GroupingsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */

    public function isAuthorized($user = null) {
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
        $group = $this->Groupings->newEntity();
        $data = $this->Trim->trimString($this->request->getData());

        if($this->request->is('post')) {
            $result = $this->loadModel('Members')->searchGroup($data['groupname'],$data['password']);  
        } else {
            //POST（検索ボタンを押下）する前は＄RESULTは空にしとく
            $result = '';
        }

        //参加ボタンを表示するか非表示にするか判別する処理
        $cnt = 0;
        if($result !== '' && $result !== NULL) {
            foreach($result as $v) {
                if((int)$v['members']['user_id'] === $user['id']) {
                    $cnt++;
                } 
            }
        }
        
        $this->set(compact('group','result','cnt'));
    }

    public function enrollpage() {
        $group = $this->Groupings->newEntity();
        $user = $this->Auth->user();
        //グループ名の値保持のため。POST処理する前の値はNULL
        $old_val = NULL;

        //post（登録ボタンを押下）した際の処理
        if($this->request->is('post')) {
            $data = $this->request->getData();
            //前後にあるスペースを削除
            $data = $this->Trim->trimString($data);
            //グループ登録処理
            if($data['password'] !== $data['pass_again']) {
                $this->Flash->error('確認用パスワードと一致しません。再度入力してください');
            //入力したグループ名が既に使用されていないかチェック
            } else if($this->Groupings->checkDupicateGroupName($data['groupname'])) {
                $this->Flash->error('グループ名が既に使用されています。');
            //エラーがなければ新規登録処理
            } else {
                $this->Groupings->insertGroup($data['groupname'], $data['password'], $user['id']);
                $this->Flash->success('登録しました');
            }

            //グループ名の値保持のため
            $old_val = $data['groupname'];
        }

        $this->set(compact('group','old_val'));
    }

    // public function enroll() {
    //    $group = $this->Groups->newEntity();
    //    $data = $this->request->getData();
    //    //前後にあるスペースを削除
    //    $data = $this->Trim->trimString($data);
    //    $user = $this->Auth->user();

    //    //グループ登録処理
    //    if($data['password'] !== $data['pass_again']) {
    //        $this->Flash->error('確認用パスワードと一致しません。再度入力してください');
    //        $this->setAction('enrollpage',['name'=>$data['groupname']]);
    //    //入力したグループ名が既に使用されていないかチェック
    //    } else if($this->Groups->checkDupicateGroupName($data['groupname'])) {
    //         $this->Flash->error('グループ名が既に使用されています。');
    //         $this->setAction('enrollpage',['name'=>$data['groupname']]);
    //     } else {
    //         $this->Groups->insertGroup($data['groupname'], $data['password'], $user['id']);
    //         $this->Flash->success('登録しました');
    //    }

    // }

    public function enrolled() {
        $user = $this->Auth->user();

        list($index,$user) = $this->loadModel('Members')->getEnrolledIndex($user['id']);

        $this->set(compact('index','user'));
    }

    public function delete($id) {

        if($this->Groupings->deleteGroup($id)) {
            $this->Flash->success('削除しました');
            $this->redirect(['action'=>'enrolled']);
        }
        
    }
   
}
