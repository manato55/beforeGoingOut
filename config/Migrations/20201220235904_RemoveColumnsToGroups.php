<?php
use Migrations\AbstractMigration;

class RemoveColumnsToGroups extends AbstractMigration
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
        $table = $this->table('groups');
        $table->removeColumn('user_id')
              ->removeColumn('role')
              ->update();
    }
    public function down()
    {
        $table = $this->table('groups');
        $table->addColumn('user_id','integer')
              ->addColumn('role','string')
              ->update();
    }
}
