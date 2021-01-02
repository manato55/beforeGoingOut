<?php
use Migrations\AbstractMigration;

class ChangeTableGroupsName extends AbstractMigration
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
        $table->rename('groupings')
              ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('groupings');
        $table->rename('groups')
              ->save();
    }
}
