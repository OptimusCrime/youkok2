<?php

use Phinx\Migration\AbstractMigration;

class Youkok2401 extends AbstractMigration
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
        $contributorTable = $this->table('contributor');
        $contributorTable
            ->removeColumn('password')
            ->removeColumn('nick')
            ->removeColumn('module_settings')
            ->removeColumn('last_seen')
            ->removeColumn('karma')
            ->removeColumn('karma_pending')
            ->save();

        $resourceTable = $this->table('resource');
        $resourceTable
            ->removeColumn('course')
            ->removeColumn('missing_image')
            ->removeColumn('alias')
            ->renameColumn('url_friendly', 'slug')
            ->save();

        $downloadTable = $this->table('download');
        $downloadTable
            ->renameColumn('file', 'resource')
            ->renameColumn('user', 'session')
            ->save();
    }
}
