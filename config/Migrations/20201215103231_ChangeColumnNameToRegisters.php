<?php
use Migrations\AbstractMigration;

class ChangeColumnNameToRegisters extends AbstractMigration
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
        $table = $this->table('registers');
        $table->renameColumn('checks_id','check_id')
              ->update();
    }

    public function down()
    {
        $table = $this->table('registers');
        $table->renameColumn('check_id','checks_id')
              ->update();
    }
}
