<?php

use Phinx\Migration\AbstractMigration;

class Youkok1200 extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
     * public function change()
     * {
     * }
     */

    /**
     * Migrate Up.
     */
    public function up()
    {
        // Get table
        $table = $this->table('history');

        // Do other stuff
        $table->removeColumn('flag')
            ->removeColumn('type')
            ->removeColumn('karma')
            ->removeColumn('active')
            ->removeColumn('positive')
            ->addColumn('visible', 'boolean', array('default' => true, 'null' => false))
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}
