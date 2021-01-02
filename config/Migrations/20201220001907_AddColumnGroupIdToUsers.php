<?php
use Migrations\AbstractMigration;

class AddColumnGroupIdToUsers extends AbstractMigration
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
        $table = $this->table('users');
        $table->addColumn('group_id','integer',[
            'default'=> NULL
        ])->addForeignKey('group_id','groups','id')
        ->update();
    }
    public function down()
    {
        $table = $this->table('users');
        $table->removeColumn('group_id','integer',[
            'default'=> NULL
        ])->addForeignKey('group_id','groups','id')
        ->update();
    }
}
