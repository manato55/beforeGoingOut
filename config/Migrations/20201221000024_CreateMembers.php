<?php
use Migrations\AbstractMigration;

class CreateMembers extends AbstractMigration
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
        $table = $this->table('members');
        $table->addColumn('group_id','integer')
              ->addColumn('user_id','integer')
              ->addColumn('role','string')
              ->addColumn('created','datetime')
              ->create();
    }
    public function down()
    {
        $table = $this->table('members');
        $table->drop();
    }
}
