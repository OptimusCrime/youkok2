<?php

use Phinx\Migration\AbstractMigration;

class Youkok3002 extends AbstractMigration
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
        $this->table('cache')
            ->addColumn('key', 'string', array('limit' => 150, 'null' => false))
            ->addColumn('value', 'text', array('null' => true, 'default' => null))
            ->addColumn('expiration', 'integer')
            ->addIndex(array('key'), array('unique' => true))
            ->create();
    }
}
