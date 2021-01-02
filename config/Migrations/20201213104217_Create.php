<?php
use Migrations\AbstractMigration;

class Create extends AbstractMigration
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
        $table = $this->table('checks');
        for($i=1;$i<10;$i++) {
            $table->addColumn('check_'.$i, 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ]);
        }
        $table->addColumn('created', 'datetime',[
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->create();
    }
}
