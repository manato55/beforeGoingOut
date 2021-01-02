<?php
use Migrations\AbstractMigration;

class AddColumnUseridToGroups extends AbstractMigration
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
        $table->addColumn('user_id', 'integer')
            ->update();
    }
    public function down()
    {
        $table = $this->table('groups');
        $table->removeColumn('user_id', 'integer')
            ->update();
    }
}
