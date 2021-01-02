<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
?>
    <?php if(count($histories) > 0): ?>
        <div class="history_container">
            <ul>
                <?php foreach($histories as $history): ?>
                    <li class="history_list">
                        <?= $this->Html->link($history['Checks']['title'],[
                                'action' => 'view',
                                $history['regist_id']
                        ]) ?>
                        <span class="history_index_time"><?= date('y/n/j G:i' ,strtotime($history->created)) ?></span>
                        <?php if($user['id'] == $user_id): ?>
                            <span class="delete_btn">
                                <?= $this->Html->link('☓',[
                                        'action' => 'delete',
                                        $history['regist_id']
                                ],['confirm' => '削除しますか？'])
                                ?>
                            </span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php else: ?>
        <?= '履歴はありません' ?>
    <?php endif ?>

    <div>
        <?php if(count($histories) > 0 && $user['id'] == $user_id): ?>
            <p class="del_all">
                <?= $this->Html->link('一括削除',[
                'action' => 'deleteall'
                ],['confirm'=>'全て削除しますか？'])
                ?>
            </p>
        <?php endif; ?>
    </div>
