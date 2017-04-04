<?php

use Phinx\Migration\AbstractMigration;

class Youkok3000 extends AbstractMigration
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
        // Remove foreign keys
        $this->execute('ALTER TABLE download DROP FOREIGN KEY download_ibfk_1');
        $this->execute('ALTER TABLE download DROP FOREIGN KEY download_ibfk_2');
        
        $this->execute('ALTER TABLE element DROP FOREIGN KEY element_ibfk_1');
        $this->execute('ALTER TABLE element DROP FOREIGN KEY element_ibfk_3');
        
        $this->execute('ALTER TABLE verify DROP FOREIGN KEY verify_ibfk_1');
    }
}
