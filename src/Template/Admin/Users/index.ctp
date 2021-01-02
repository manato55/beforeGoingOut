
<span class="toGroupPage">
    <?= $this->Html->link('グループ一覧',[
        'controller' => 'users',
        'action' => 'groupindex'
    ])
    ?>
</span>

<div class="history_container">
    <ol start="1">
        <?php foreach($allUsers as $user): ?>
            <li class="checkUnderline">
                <?= $this->Html->link($user['username'],[
                    'action'=> 'checksindex',
                    $user['id']
                ])  ?>
                <span class="admin_email_check"><?= $user['email'] ?></span>
            </li>
        <?php endforeach;?>
        </ol>
</div>