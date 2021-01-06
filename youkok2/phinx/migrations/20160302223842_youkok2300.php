<?php

use Phinx\Migration\AbstractMigration;

class Youkok2300 extends AbstractMigration
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
        $this->execute('ALTER TABLE flag DROP FOREIGN KEY flag_ibfk_1');
        $this->execute('ALTER TABLE flag DROP FOREIGN KEY flag_ibfk_2');

        $this->execute('ALTER TABLE report DROP FOREIGN KEY report_ibfk_1');
        $this->execute('ALTER TABLE report DROP FOREIGN KEY report_ibfk_2');

        $this->execute('ALTER TABLE vote DROP FOREIGN KEY vote_ibfk_1');
        $this->execute('ALTER TABLE vote DROP FOREIGN KEY vote_ibfk_2');

        $this->table('flag')->drop()->save();
        $this->table('report')->drop()->save();
        $this->table('vote')->drop()->save();

        $this->table('archive')
            ->renameColumn('is_directory', 'directory')
            ->renameColumn('is_accepted', 'pending')
            ->renameColumn('is_visible', 'deleted')
            ->save();
    }

    public function down()
    {
    }
}
