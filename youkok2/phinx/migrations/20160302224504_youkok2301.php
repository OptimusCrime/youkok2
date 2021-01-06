<?php

use Phinx\Migration\AbstractMigration;

class Youkok2301 extends AbstractMigration
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
    public function up()
    {
        $this->query('UPDATE `archive` SET `pending` = 2 WHERE `pending` = 0');
        $this->query('UPDATE `archive` SET `pending` = 0 WHERE `pending` = 1');
        $this->query('UPDATE `archive` SET `pending` = 1 WHERE `pending` = 2');

        // Swap deleted
        $this->query('UPDATE `archive` SET `deleted` = 2 WHERE `deleted` = 0');
        $this->query('UPDATE `archive` SET `deleted` = 0 WHERE `deleted` = 1');
        $this->query('UPDATE `archive` SET `deleted` = 1 WHERE `deleted` = 2');
    }

    public function down()
    {
    }
}
