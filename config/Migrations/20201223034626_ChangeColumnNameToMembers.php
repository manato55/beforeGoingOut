<?php
use Migrations\AbstractMigration;

class ChangeColumnNameToMembers extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function up()
    {
        //tableメソッドの引数にはイニシャルは小文字で
        $table = $this->table('members');
        $table->renameColumn('group_id','grouping_id')
              ->update();
    }

    public function down()
    {
        $table = $this->table('members');
        $table->renameColumn('grouping_id','group_id')
              ->update();
    }
}
