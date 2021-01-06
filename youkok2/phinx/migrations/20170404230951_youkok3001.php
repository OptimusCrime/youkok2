<?php

use Phinx\Migration\AbstractMigration;

class Youkok3001 extends AbstractMigration
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
        // Rename url to link
        $this->table('element')
            ->renameColumn('url', 'link')
            ->save();

        // Add a new column uri
        $this->table('element')
            ->addColumn('uri', 'string', array('limit' => 255, 'default' => null, 'null' => true, 'after' => 'slug'))
            ->update();
    }
}
