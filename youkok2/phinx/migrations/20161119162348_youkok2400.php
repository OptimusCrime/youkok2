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
        $this->table('archive')
            ->rename('resource')
            ->save();

        $this->table('user')
            ->rename('contributor')
            ->save();

        try {
            $this->execute('ALTER TABLE resource DROP FOREIGN KEY resource_ibfk_2');

            $this->execute('ALTER TABLE favorite DROP FOREIGN KEY favorite_ibfk_1');
            $this->execute('ALTER TABLE favorite DROP FOREIGN KEY favorite_ibfk_2');

            $this->execute('ALTER TABLE history DROP FOREIGN KEY history_ibfk_1');
            $this->execute('ALTER TABLE history DROP FOREIGN KEY history_ibfk_2');

            $this->execute('ALTER TABLE karma DROP FOREIGN KEY karma_ibfk_1');
            $this->execute('ALTER TABLE karma DROP FOREIGN KEY karma_ibfk_2');
        } catch (\Exception $e) {
            //
        }

        $this->table('changepassword')->drop()->save();
        $this->table('course')->drop()->save();
        $this->table('favorite')->drop()->save();
        $this->table('history')->drop()->save();
        $this->table('karma')->drop()->save();
    }
}
