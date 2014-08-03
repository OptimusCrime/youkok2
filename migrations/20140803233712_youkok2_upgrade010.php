<?php

use Phinx\Migration\AbstractMigration;

class Youkok2Upgrade010 extends AbstractMigration
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
        $this->table('history')
         ->addColumn('type', 'integer', array('limit' => 1, 'null' => false ,'after' => 'flag'))
         ->removeColumn('history_text')
         ->addColumn('history_text', 'text', array('null' => true, 'after' => 'type'))
         ->addColumn('karma', 'integer', array('limit' => 2, 'null' => true, 'default' => 0, 'after' => 'history_text'))
         ->addColumn('active', 'integer', array('limit' => 1, 'null' => false, 'default' => 1, 'after' => 'karma'))
         ->addColumn('positive', 'integer', array('limit' => 1, 'null' => false, 'default' => 1, 'after' => 'active'))
         ->save();

        $this->table('user')
         ->addColumn('karma_pending', 'integer', array('limit' => 11, 'null' => false, 'default' => 0, 'after' => 'karma'))
         ->save();

        $this->dropTable('verify');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}