
<h3><?= $title['title'] ?></h3>

<div class="history_container">
    <ol start="1">
        <?php foreach($registers as $regist): ?>
            <li class="checkUnderline">
               <?= $regist ?>
            </li>
        <?php endforeach;?>
    </ol>
</div>