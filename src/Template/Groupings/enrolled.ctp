

<div>
    <table>
        <thead>
            <tr>
                <th class="group_col">グループ名</th>
                <th class="paticipant_col">参加者</th>
                <th class="table_del_col">削除</th>
            </tr>
        </thead>
        <tbody>
            <?php for($i=0;$i<count($index);$i++): ?>
                <tr>
                    <td class="group_name_row">
                        <?= $index[$i]['groupings']['groupname'] ?>
                    </td>
                    <td class="paticipant_row">
                        <?php foreach($user[$i] as $v): ?>
                            <?= $this->Html->link($v['users']['username'],[
                                'controller' => 'histories',
                                'action' => 'index',
                                $v['users']['id']
                            ])
                            ?>
                            <?= '&emsp;' ?>
                        <?php endforeach; ?>
                    </td>
                    <td class="table_del_col">
                        <?php if($index[$i]['members']['role'] === 'host'):?>
                            <?= $this->Html->link('☓',[
                                'action'=>'delete',
                                $index[$i]['groupings']['id']
                            ],['confirm' => '削除しますか？'])
                            ?>
                        <?php endif;?>
                    </td>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>
    
</div>