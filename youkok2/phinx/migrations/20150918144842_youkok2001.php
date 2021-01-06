<?php

use Phinx\Migration\AbstractMigration;

class Youkok2001 extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     */
    public function change()
    {
        $this->table('karma')
            ->addColumn('state', 'boolean', array('default' => 1, 'null' => false, 'after' => 'pending'))
            ->update();
    }
}
