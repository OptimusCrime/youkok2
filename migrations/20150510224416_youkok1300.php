<?php

use Phinx\Migration\AbstractMigration;

class Youkok1300 extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    public function change()
    {
    }
    */
    
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('archive')
             ->addColumn('exam', 'datetime', array('default' => null, 'null' => true, 'after' => 'is_visible'))
             ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}