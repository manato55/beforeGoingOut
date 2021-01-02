

<div>
    <h3>グループ検索</h3>
    <span class="regist_new_group">
        <?= $this->Html->link('新規登録',[
            'action'=>'enrollpage'
        ])
        ?>
    </span>
    <div class="group_search_box">
        <?= $this->Form->create($group) ?>
        <?= $this->Form->control('groupname',[
            'label' => 'グループ名',
        ])
        ?>
        <?= $this->Form->error('groupname') ?>
        <?= $this->Form->control('password',[
            'label' => 'パスワード'
        ])
        ?>
        <?= $this->Form->submit('検索') ?>
        <?= $this->Form->end() ?>
    </div>

        <!--検索ボタンを押す前は$resultは空のため-->
        <?php if($result !== ""): ?>
            <!--検索の結果、該当するグループがあった場合-->
            <?php if($result !== NULL): ?>
                <div class="show_result">
                    <h4 class="search_result_title">検索結果</h4>
                    <p><?= 'グループ名：'.$result[0]['groupings']['groupname'] ?></p>
                    <p><?= 'メンバー：' ?>
                        <?php foreach($result as $v): ?>
                        <?= $v['users']['username'].'&emsp;' ?>
                        <?php endforeach; ?>
                    </p>
                    <p>
                        <!--そのグループに参加していなかったら、参加ボタンは表示-->
                        <?php if($cnt === 0): ?>
                            <?= $this->Html->link('参加',[
                                'controller' => 'members',
                                'action'=>'join',
                                $result[0]['groupings']['id']
                            ],['confirm'=> '参加しますか？'])
                            ?>
                        <?php endif; ?>
                    </p>
                </div>
            <!--検索の結果、該当するグループがなかった場合-->
            <?php else: ?>
                <p class="found_no_group"><?= '該当するグループはありません' ?></p>
            <?php endif; ?>
        <?php endif; ?>

</div>