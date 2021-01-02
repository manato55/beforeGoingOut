

<div>
    <table>
        <thead>
            <tr>
                <th>グループ名</th>
                <th class="paticipant_col">参加者</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($index as $v): ?>
            <tr>
                <td><?= $v['groupname']; ?></td>
                <td><?= $v['username']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
</div>