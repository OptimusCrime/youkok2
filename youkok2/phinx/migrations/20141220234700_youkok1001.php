<?php

use Phinx\Migration\AbstractMigration;

class Youkok1001 extends AbstractMigration
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
             ->addColumn('checksum', 'string', array('limit' => 200, 'null' => true, 'default' => null, 'after' => 'course'))
             ->update();

        // Workaround
        $this->execute('ALTER TABLE `archive` CHANGE `location` `location` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}