<?php
use Migrations\AbstractMigration;

class CreateGroupsTable extends AbstractMigration
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
        $table->addColumn('groupname', 'string',[
            'limit'=>255
        ])->addColumn('password', 'string',[
            'limit'=>255
        ])->addColumn('created','datetime',[
            'limit'=>255
        ])
        ->create();
    }
    public function down()
    {
        $table = $this->table('groups');
        $table->removeColumn('groupname', 'string',[
            'limit'=>255
        ])->removeColumn('password', 'string',[
            'limit'=>255
        ])->removeColumn('created','datetime',[
            'limit'=>255
        ])
        ->create();
    }
}
