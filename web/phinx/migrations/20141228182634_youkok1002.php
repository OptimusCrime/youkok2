<?php

use Phinx\Migration\AbstractMigration;

class Youkok1002 extends AbstractMigration
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
        $this->table('course')
             ->addColumn('empty', 'boolean', array('limit' => 1, 'null' => false, 'default' => 1, 'after' => 'name'))
             ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}