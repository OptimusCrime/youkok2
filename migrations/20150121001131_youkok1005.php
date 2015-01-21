<?php

use Phinx\Migration\AbstractMigration;

class Youkok1005 extends AbstractMigration
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
             ->addColumn('owner', 'integer', array('limit' => 22, 'null' => true, 'default' => null, 'after' => 'url_friendly'))
             ->update();
        $this->execute('ALTER TABLE  `archive` CHANGE  `owner`  `owner` BIGINT( 22 ) NULL');
        
        $this->table('archive')
             ->addForeignKey('owner', 'user', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
             ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}