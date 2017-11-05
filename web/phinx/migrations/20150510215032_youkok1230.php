<?php

use Phinx\Migration\AbstractMigration;

class Youkok1230 extends AbstractMigration
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
        // Create table
        $this->table('message')
             ->addColumn('time_start', 'timestamp', array('null' => false, 'default' => 'CURRENT_TIMESTAMP'))
             ->addColumn('time_end', 'timestamp', array('null' => true, 'default' => null))
             ->addColumn('message', 'text', array('null' => true, 'default' => null))
             ->addColumn('type', 'string', array('limit' => 30, 'null' => false, 'default' => 'sucess'))
             ->addColumn('pattern', 'string', array('limit' => 30, 'null' => false, 'default' => '*'))
             ->create();
        $this->execute('ALTER TABLE  `message` CHANGE  `id`  `id` BIGINT( 22 ) NOT NULL AUTO_INCREMENT');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}