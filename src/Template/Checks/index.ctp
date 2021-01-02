<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
?>

<span class="toGroupPage">
    <?= $this->Html->link('グループ一覧',[
        'controller' => 'groupings',
        'action' => 'enrolled'
    ])
    ?>
</span>

<div class="users index large-9 medium-8 columns content">
    <div class="container">
    
        <!--セレクトボックスの処理-->
        <?= $this->Form->create($checks,[
            'type' => 'post',
            'url' => [
                'action' => 'index'
            ],
            'id' => 'choiceForm'
        ])?>
        
        <?= $this->Form->select('title',
            ['options' => $options],
            ['empty' => '選択してください','id' => 'choice'],
        )?>
        <?= $this->Form->end() ?>
        <!--ここまで-->

        <?= date('Y年n月j日 G時i分') ?>


        <div class="index_container">
            <?= $this->Form->create($checks,[
                'type' => 'post',
                'url'=> [
                    'controller' => 'registers',
                    'action' => 'submitchecks'
                ]
            ]) ?>
            <ol start="1">
                <?php for($i=0;$i<count($choiced);$i++): ?>
                    <label for="checkBox_<?=$i?>">
                        <li class="checkUnderline">
                            <?= $choiced[$i] ?>
                            <span class="check_box">
                                    <?= $this->Form->checkbox('check_'.$i,
                                        ['value' => '1','class' => 'checkBox_'.$i,'id' => 'checkBox_'.$i ]).'<br>' 
                                    ?>
                            </span>
                        </li>
                    </label>
                <?php endfor; ?>
            </ol>
            <?= $this->Form->hidden('id',[
                'value' => $id
            ]) ?>
            <?= $this->Form->submit('登録',[
                'id' => 'checksSend'
            ]) ?>
            <?=  $this->Form->end() ?>
        </div>


    </div>
</div>

<script>
    'user strict'; {
        $(function() {

            //セレクトボックスで選択するとsubmit
            $('#choice').change(function() {
                if($('#choice').val() !== 'options') {
                    $('#choiceForm').submit();
                }
            });

            //チェック項目の数を取得
            let num = $('.checkUnderline').length;
            
            //登録ボタンを押したときの挙動
            $('#checksSend').on('click',function(e){
                let cnt = 0;
                //チェック項目の数だけループ処理
                for(let i = 0;i<num;i++) {
                    if($('.checkBox_'+i).prop('checked') === false ) {
                        //チェックされていなければカウントに１プラス
                        cnt++;
                        alert('未確認の項目があります');
                        e.preventDefault();
                        break;
                    } 
                }
                if($('#choice').val() === '' || $('#choice').val() === 'options') {
                    cnt++;
                    alert('選択してください');
                    e.preventDefault();
                }

                //カウントが０、すなわち全ての項目にチェックされていれば登録
                if(cnt === 0 && !confirm('登録しますか？')) {
                    return false;
                } 
            })
            
        });



    }



</script>
