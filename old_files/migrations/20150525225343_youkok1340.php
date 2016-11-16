<?php

use Phinx\Migration\AbstractMigration;

class Youkok1340 extends AbstractMigration
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
        // Drop foreign keys on drop table (for eazy sake)
        $this->query('SET foreign_key_checks = 0');
        
        // Drop table
        $this->dropTable('course');
        
        // Use foreign key constraints again
        $this->query('SET foreign_key_checks = 0');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}