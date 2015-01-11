<?php

use Phinx\Migration\AbstractMigration;

class Youkok1004 extends AbstractMigration
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
             ->addColumn('empty', 'boolean', array('limit' => 1, 'null' => false, 'default' => 1, 'after' => 'parent'))
             ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}