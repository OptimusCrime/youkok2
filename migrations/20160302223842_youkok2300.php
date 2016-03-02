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
        $this->dropTable('flag');
        $this->dropTable('report');
        $this->dropTable('vote');
        
        $table = $this->table('archive');
        $table->renameColumn('is_directory', 'directory');
        $table->renameColumn('is_accepted', 'pending');
        $table->renameColumn('is_visible', 'deleted');
    }
    
    public function down()
    {
        // Hmmmm
        
        $table = $this->table('archive');
        $table->renameColumn('directory', 'is_directory');
        $table->renameColumn('pending', 'is_accepted');
        $table->renameColumn('deleted', 'is_visible');
    }
}
