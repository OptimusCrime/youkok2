<?php

use Phinx\Migration\AbstractMigration;

class Youkok1201 extends AbstractMigration
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
        $this->table('karma')
             ->addColumn('user', 'biginteger', array('limit' => 22, 'null' => false))
             ->addColumn('file', 'biginteger', array('limit' => 22, 'null' => false))
             ->addColumn('value', 'integer', array('limit' => 2, 'null' => false, 'default' => 5))
             ->addColumn('pending', 'boolean', array('null' => false, 'default' => true))
             ->addColumn('added', 'timestamp', array('null' => false, 'default' => 'CURRENT_TIMESTAMP'))
             ->create();
        $this->execute('ALTER TABLE  `karma` CHANGE  `id`  `id` BIGINT( 22 ) NOT NULL AUTO_INCREMENT');
        
        // Add foreign keys
        $this->table('karma')
             ->addForeignKey('file', 'archive', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
             ->addForeignKey('user', 'user', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
             ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}