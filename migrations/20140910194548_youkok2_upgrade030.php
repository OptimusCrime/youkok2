<?php

use Phinx\Migration\AbstractMigration;

class Youkok2Upgrade030 extends AbstractMigration
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
        ->addColumn('url', 'string', array('limit' => 255, 'null' => true, 'default' => null, 'after' => 'is_visible'))
        ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}