<?php
use Migrations\AbstractMigration;

class AddColumnUseridToChecks extends AbstractMigration
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
        $table = $this->table('checks');
        $table->addColumn('user_id', 'integer')
              ->update();
    }
    public function down()
    {
        $table = $this->table('checks');
        $table->removeColumn('user_id', 'integer')
              ->update();
    }
}
