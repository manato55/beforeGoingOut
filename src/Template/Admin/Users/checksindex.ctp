
<h3><?= $user['username']?></h3>

<div class="history_container">
    <ol start="1">
        <?php foreach($userChecks as $check): ?>
            <li class="checkUnderline">
                <?= $this->Html->link($check['title'],[
                    'action' => 'refertoregisters',
                    $check['id']
                ]) ?>
            </li>
        <?php endforeach;?>
    </ol>
</div>