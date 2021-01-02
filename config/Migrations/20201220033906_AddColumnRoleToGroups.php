<?php
use Migrations\AbstractMigration;

class AddColumnRoleToGroups extends AbstractMigration
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
        $table->addColumn('role','string',[
            'default' => 'host',
        ])
        ->update();
    }
    public function down()
    {
        $table = $this->table('groups');
        $table->removeColumn('role','string',[
            'default' => 'host',
        ])
        ->update();
    }
}
