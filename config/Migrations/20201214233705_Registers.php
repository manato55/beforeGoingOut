<?php
use Migrations\AbstractMigration;

class Registers extends AbstractMigration
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
        $table = $this->table('registers');
            $table->addColumn('checks_id', 'integer');
            for($i=1;$i<10;$i++) {
                $table->addColumn('register_'.$i, 'string');
            }
            $table->addColumn('created', 'datetime');
            $table->create();
            
    }
    public function down()
    {
        $table = $this->table('registers');
            $table->removeColumn('checks_id', 'integer');
            for($i=1;$i<10;$i++) {
                $table->removeColumn('register_'.$i, 'string');
            }
            $table->removeColumn('created', 'datetime');
            $table->drop();
    }
}
