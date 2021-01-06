<?php


use Phinx\Migration\AbstractMigration;

class Youkok3005 extends AbstractMigration
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
        $elementTable = $this->table('element');
        $elementTable
            ->removeColumn('owner')
            ->removeColumn('mime_type')
            ->removeColumn('exam')
            ->save();

        $this->table('cache')->drop()->save();
        $this->table('contributor')->drop()->save();
        $this->table('download')->removeColumn('session')->save();
    }
}
