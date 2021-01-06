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
     * public function change()
     * {
     * }
     */

    /**
     * Migrate Up.
     */
    public function up()
    {
        try {
            $this->execute('ALTER TABLE archive DROP FOREIGN KEY archive_ibfk_2');
        } catch (\Exception $e) {
            //
        }

        $this->table('course')->drop()->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}
