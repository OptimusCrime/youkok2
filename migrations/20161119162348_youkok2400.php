<?php

use Phinx\Migration\AbstractMigration;

class Youkok2400 extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $archiveTable = $this->table('archive');
        $archiveTable->rename('resource');

        $userTable = $this->table('user');
        $userTable->rename('contributor');

        $this->dropTable('changepassword');
        $this->dropTable('course');
        $this->dropTable('favorite');
        $this->dropTable('history');
        $this->dropTable('karma');
    }
}