<?php
use Migrations\AbstractMigration;

class CreateUsers extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('users');
        $table->addColumn('username', 'string',[
                'default' => null,
                'limit' => 100,
            ])->addColumn('password', 'string',[
                'default' => null,
                'limit' => 255
            ])->addColumn('role', 'string',[
                'default' => null
            ])->addColumn('email', 'string', [
                'default' => null,
                'limit' => 255,
            ])->addColumn('route', 'string', [
                'default' => null,
                'limit' => 255,
            ])->create();
    }

}
