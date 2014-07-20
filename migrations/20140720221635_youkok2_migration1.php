<?php

use Phinx\Migration\AbstractMigration;

class Youkok2Migration1 extends AbstractMigration
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
        $this->table('user')
             ->removeColumn('ntnu_email')
             ->removeColumn('ntnu_verified')
             ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}