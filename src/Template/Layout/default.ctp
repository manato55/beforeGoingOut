<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var \App\View\AppView $this
 */

$cakeDescription = 'CakePHP: the rapid development php framework';
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $cakeDescription ?>:
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->Html->css('base.css') ?>
    <?= $this->Html->css('style.css') ?>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>


    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
    <nav class="top-bar expanded" data-topbar role="navigation">
        <ul class="title-area large-3 medium-4 columns">
            <li class="name">
                <?php if(isset($users) && $users['role'] === 'user'): ?>       
                    <h1><a href="/checks/index">お出かけくん</a></h1>
                <?php elseif(isset($users) && $users['role'] === 'admin'): ?>
                    <!-- <h1><a href="http://localhost:8765/admin/users">お出かけくん</a></h1> -->
                    <h1><?= $this->Html->link('お出かけくん',[
                        'controller' => 'users',
                        'action' => 'index',
                    ])?></h1>
                <?php else: ?>
                    <h1 style="color: white;">お出かけくん</h1>
                <?php endif ?>
            </li>
        </ul>
        <?php if(isset($users) && $users['role'] === 'admin'): ?> 
            <div class="top-bar-section">
                <nav class="NavMenu">
                    <ul class="right">
                        <li><?= $this->Html->link('ログアウト',[
                          'controller' => 'users',
                          'action' => 'logout',
                          'prefix' => false
                        ],
                        ['confirm' => 'ログアウトしますか？']) 
                        ?>
                        </li> 
                    </ul>
                </nav>
                <div class="Toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        <?php elseif(isset($users) && $users['role'] === 'user'): ?>
            <div class="top-bar-section">
                <nav class="NavMenu">
                    <ul class="right">
                        <li><?= $this->Html->link('履歴',['controller' => 'histories', 'action' => 'index',$users['id']]) ?></li>
                        <li><?= $this->Html->link('設定',['controller' => 'checks', 'action' => 'settings']) ?></li>
                        <li><?= $this->Html->link('グループ',['controller' => 'groupings', 'action' => 'index']) ?></li>
                        <li><?= $this->Html->link('ログアウト',['controller' => 'users', 'action' => 'logout'],['confirm' => 'ログアウトしますか？']) ?></li>     
                    </ul>
                </nav>
                <div class="Toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        <?php endif;?>
    </nav>
    <?= $this->Flash->render() ?>
    <div class="container clearfix">
        <?= $this->fetch('content') ?>
    </div>
    <footer>
    </footer>

    <script>
        $(function() {
           $('.Toggle').click(function() {

            $(this).toggleClass('active');

                if ($(this).hasClass('active')) {
                    $('.NavMenu').addClass('active');　 //クラスを付与
                } else {
                    $('.NavMenu').removeClass('active'); //クラスを外す
                }
        
            });
        });


    </script>


</body>
</html>
