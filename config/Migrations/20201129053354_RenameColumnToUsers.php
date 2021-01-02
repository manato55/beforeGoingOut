<?php
use Migrations\AbstractMigration;

class RenameColumnToUsers extends AbstractMigration
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
            $table->renameColumn('route', 'routeFrom')
                  ->update();          
    }
    public function down()
    {
        $table = $this->table('users');
            $table->renameColumn('routeFrom', 'route')
                  ->update();          
    }
}
