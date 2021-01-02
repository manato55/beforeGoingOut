<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
?>

  
  <div class="view_container">
     <h3 class="view_title">
         <?= $title ?>
         <span class="view_time"><?= date('y/n/j G:i', strtotime($created)) ?></span>
     </h3>
    

    <ol start="1">
        <?php for($i=0;$i<count($checks_data);$i++): ?>
            <li class="checkUnderline">
                <?= $checks_data[$i] ?>
                <?php if($registers_data[$i] !== NULL): ?>
                    <span class="checkMark"><?= '✔' ?></span>
                <?php endif ?>
            </li>
        <?php endfor ?>
    </ol>

  </div>
