<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
?>

<div class="users index large-9 medium-8 columns content">
  
    <div class="container">
        <?= $this->Form->create($checks,[
            'type' => 'post',
            'id' => 'selectForm',
            'url' => [
                'action' => 'deletepage'
            ]
            ])?>
        <?= $this->Form->select('title',
            ['options' => $options],
            ['empty' => '選択してください','id' => 'choice'],
        )?>
        <?= $this->Form->end() ?>

        <?php if(isset($selectedTopic)): ?>
            <ol start="1">
                <?php for($i=0;$i<count($selectedTopic);$i++): ?>
                    <li class="checkUnderline"><?= $selectedTopic[$i] ?></li>
                <?php endfor;?>
            </ol>
        <?php endif;?>


        <?= $this->Form->create($checks,[
            'type' => 'post',
            'url' => [
                'action' => 'delete'
            ]
        ])?>
        <?= $this->Form->hidden('title',[
            'value' => $id
        ])
        ?>
        <?= $this->Form->submit('削除',[
            'id' => 'del_btn'
        ]) ?>
        <?= $this->Form->end() ?>
       
    </div>
</div>

<script>
   'use strict'; 
   {

        $('#choice').on('change', function() {
            if($('#choice').val() !== 'options') {
                $('#selectForm').submit();
            }
        })

        $('#del_btn').on('click',function(e) {
            if($('#choice').val() === '' || $('#choice').val() === 'options') {
                alert('選択してください');
                e.preventDefault();
            } else {
                return (!confirm('削除しますか？')) ? false: true;
            }
        });

   }




</script>
