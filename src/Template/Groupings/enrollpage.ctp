

<div>
    <h3>新規グループ登録</h3>
    <span class="regist_new_group">
        <?= $this->Html->link('グループ検索',[
            'action'=>'index'
        ])
        ?>
    </span>

    <div class="group_search_box">
        <?= $this->Form->create($group)?>
      
        <?= $this->Form->control('groupname',[
            'label' => 'グループ名',
            'value' => $old_val,
        ])
        ?>
        <?= $this->Form->error('groupname') ?>
        <?= $this->Form->control('password',[
            'label' => 'パスワード(8文字以上)',
            'value' => ''
        ])
        ?>
        <?= $this->Form->control('password',[
            'label' => 'パスワード(確認)',
            'name' => 'pass_again',
            'id' => 'pass_again',
            'value' => ''
        ])
        ?>
        <?= $this->Form->submit('登録',[
            'id' => 'groupEnrollBtn'
        ]) ?>
        <?= $this->Form->end() ?>
    </div>
</div>

<script>
    'use strict'; 
    {

        $('#groupEnrollBtn').on('click',function(e) {
            if($('#groupname').val() === '') {
                alert('グループ名を入力してください');
                e.preventDefault();
            }

            if($('#password').val().length < 8 || $('#pass_again').val().length < 8) {
                alert('パスワードは８文字以上設定してください');
                e.preventDefault();
            } else {
                return !confirm('登録しますか？')? false: true;
            }

        })









    }



</script>