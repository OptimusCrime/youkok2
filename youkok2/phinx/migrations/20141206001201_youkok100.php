<?php

use Phinx\Migration\AbstractMigration;

class Youkok100 extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
     * public function change()
     * {
     * }
     */

    /**
     * Migrate Up.
     */
    public function up()
    {
        /*
         * Archive table
         */

        $this->table('archive')
            ->addColumn('name', 'string', array('limit' => 200, 'null' => false))
            ->addColumn('url_friendly', 'string', array('limit' => 200, 'null' => false))
            ->addColumn('parent', 'biginteger', array('limit' => 22, 'null' => true, 'default' => null))
            ->addColumn('course', 'biginteger', array('limit' => 22, 'null' => true, 'default' => null))
            ->addColumn('location', 'string', array('limit' => 200, 'null' => false))
            ->addColumn('mime_type', 'string', array('limit' => 200, 'null' => true, 'default' => null))
            ->addColumn('missing_image', 'boolean', array('limit' => 1, 'null' => false, 'default' => 0))
            ->addColumn('size', 'integer', array('limit' => 11, 'null' => true, 'default' => null))
            ->addColumn('is_directory', 'boolean', array('limit' => 1, 'null' => false, 'default' => 0))
            ->addColumn('is_accepted', 'boolean', array('limit' => 1, 'null' => false, 'default' => 0))
            ->addColumn('is_visible', 'boolean', array('limit' => 1, 'null' => false, 'default' => 1))
            ->addColumn('url', 'string', array('limit' => 255, 'null' => true, 'default' => null))
            ->addColumn('added', 'timestamp', array('null' => false, 'default' => 'CURRENT_TIMESTAMP'))
            ->create();
        $this->execute('ALTER TABLE  `archive` CHANGE  `id`  `id` BIGINT( 22 ) NOT NULL AUTO_INCREMENT');

        /*
         * Changepassword table
         */

        $this->table('changepassword')
            ->addColumn('user', 'biginteger', array('limit' => 22, 'null' => false))
            ->addColumn('hash', 'string', array('limit' => 150, 'null' => false))
            ->addColumn('timeout', 'timestamp', array('null' => false))
            ->create();
        $this->execute('ALTER TABLE  `changepassword` CHANGE  `id`  `id` BIGINT( 22 ) NOT NULL AUTO_INCREMENT');

        /*
         * Course table
         */

        $this->table('course')
            ->addColumn('code', 'string', array('limit' => 15, 'null' => false))
            ->addColumn('name', 'string', array('limit' => 150, 'null' => false))
            ->create();
        $this->execute('ALTER TABLE  `course` CHANGE  `id`  `id` BIGINT( 22 ) NOT NULL AUTO_INCREMENT');

        /*
         * Download table
         */

        $this->table('download')
            ->addColumn('file', 'biginteger', array('limit' => 22, 'null' => false))
            ->addColumn('downloaded_time', 'timestamp', array('null' => false, 'default' => 'CURRENT_TIMESTAMP'))
            ->addColumn('ip', 'string', array('limit' => 25, 'null' => false))
            ->addColumn('agent', 'string', array('limit' => 200, 'null' => true, 'default' => null))
            ->addColumn('user', 'biginteger', array('limit' => 22, 'null' => true, 'default' => null))
            ->create();
        $this->execute('ALTER TABLE  `download` CHANGE  `id`  `id` BIGINT( 22 ) NOT NULL AUTO_INCREMENT');

        /*
         * Favorite table
         */

        $this->table('favorite')
            ->addColumn('file', 'biginteger', array('limit' => 22, 'null' => false))
            ->addColumn('user', 'biginteger', array('limit' => 22, 'null' => false))
            ->addColumn('favorited_time', 'timestamp', array('null' => false, 'default' => 'CURRENT_TIMESTAMP'))
            ->addColumn('ordering', 'integer', array('limit' => 4, 'null' => false, 'default' => 0))
            ->create();
        $this->execute('ALTER TABLE  `favorite` CHANGE  `id`  `id` BIGINT( 22 ) NOT NULL AUTO_INCREMENT');

        /*
         * Flag table
         */

        $this->table('flag')
            ->addColumn('file', 'biginteger', array('limit' => 22, 'null' => false))
            ->addColumn('user', 'biginteger', array('limit' => 22, 'null' => false))
            ->addColumn('flagged', 'timestamp', array('null' => false, 'default' => 'CURRENT_TIMESTAMP'))
            ->addColumn('type', 'integer', array('limit' => 2, 'null' => false))
            ->addColumn('active', 'boolean', array('limit' => 1, 'null' => false, 'default' => 1))
            ->addColumn('data', 'text', array('null' => true, 'default' => null))
            ->addColumn('message', 'text', array('null' => true, 'default' => null))
            ->create();
        $this->execute('ALTER TABLE  `flag` CHANGE  `id`  `id` BIGINT( 22 ) NOT NULL AUTO_INCREMENT');

        /*
         * History table
         */

        $this->table('history')
            ->addColumn('user', 'biginteger', array('limit' => 22, 'null' => false))
            ->addColumn('file', 'biginteger', array('limit' => 22, 'null' => false))
            ->addColumn('flag', 'biginteger', array('limit' => 22, 'null' => true, 'default' => null))
            ->addColumn('type', 'integer', array('limit' => 1, 'null' => false, 'default' => 1))
            ->addColumn('history_text', 'text', array('null' => true, 'default' => null))
            ->addColumn('karma', 'integer', array('limit' => 2, 'null' => false, 'default' => 0))
            ->addColumn('added', 'timestamp', array('null' => false, 'default' => 'CURRENT_TIMESTAMP'))
            ->addColumn('active', 'boolean', array('limit' => 1, 'null' => false, 'default' => 1))
            ->addColumn('positive', 'boolean', array('limit' => 1, 'null' => false, 'default' => 1))
            ->create();
        $this->execute('ALTER TABLE  `history` CHANGE  `id`  `id` BIGINT( 22 ) NOT NULL AUTO_INCREMENT');

        /*
         * Report table
         */

        $this->table('report')
            ->addColumn('file', 'biginteger', array('limit' => 22, 'null' => false))
            ->addColumn('user', 'biginteger', array('limit' => 22, 'null' => false))
            ->addColumn('reason', 'string', array('limit' => 30, 'null' => false))
            ->addColumn('comment', 'text', array('null' => true, 'default' => null))
            ->addColumn('reported', 'timestamp', array('null' => false, 'default' => 'CURRENT_TIMESTAMP'))
            ->create();
        $this->execute('ALTER TABLE  `report` CHANGE  `id`  `id` BIGINT( 22 ) NOT NULL AUTO_INCREMENT');

        /*
         * User table
         */

        $this->table('user')
            ->addColumn('email', 'string', array('limit' => 100, 'null' => false))
            ->addColumn('password', 'string', array('limit' => 200, 'null' => false))
            ->addColumn('salt', 'string', array('limit' => 150, 'null' => false))
            ->addColumn('nick', 'string', array('limit' => 100, 'null' => true, 'default' => null))
            ->addColumn('most_popular_delta', 'integer', array('limit' => 1, 'null' => true, 'default' => 1))
            ->addColumn('last_seen', 'timestamp', array('null' => false, 'default' => 'CURRENT_TIMESTAMP'))
            ->addColumn('karma', 'integer', array('limit' => 11, 'null' => false, 'default' => 5))
            ->addColumn('karma_pending', 'integer', array('limit' => 11, 'null' => false, 'default' => 0))
            ->addColumn('banned', 'boolean', array('null' => false, 'default' => 0))
            ->create();
        $this->execute('ALTER TABLE  `user` CHANGE  `id`  `id` BIGINT( 22 ) NOT NULL AUTO_INCREMENT');

        /*
         * Verify table
         */

        $this->table('verify')
            ->addColumn('user', 'biginteger', array('limit' => 22, 'null' => false))
            ->addColumn('hash', 'string', array('limit' => 225, 'null' => false))
            ->addColumn('username', 'string', array('limit' => 40, 'null' => false))
            ->create();
        $this->execute('ALTER TABLE  `verify` CHANGE  `id`  `id` BIGINT( 22 ) NOT NULL AUTO_INCREMENT');

        /*
         * Vote table
         */

        $this->table('vote')
            ->addColumn('user', 'biginteger', array('limit' => 22, 'null' => false))
            ->addColumn('flag', 'biginteger', array('limit' => 22, 'null' => true, 'default' => null))
            ->addColumn('value', 'boolean', array('limit' => 1, 'null' => false))
            ->addColumn('voted', 'timestamp', array('null' => false, 'default' => 'CURRENT_TIMESTAMP'))
            ->create();
        $this->execute('ALTER TABLE  `vote` CHANGE  `id`  `id` BIGINT( 22 ) NOT NULL AUTO_INCREMENT');

        /*
         * Foreign keys
         */

        $this->table('archive')
            ->addForeignKey('parent', 'archive', 'id', array('delete' => 'SET_NULL', 'update' => 'NO_ACTION'))
            ->addForeignKey('course', 'course', 'id', array('delete' => 'SET_NULL', 'update' => 'CASCADE'))
            ->update();

        $this->table('changepassword')
            ->addForeignKey('user', 'user', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->update();

        $this->table('download')
            ->addForeignKey('file', 'archive', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->addForeignKey('user', 'user', 'id', array('delete' => 'SET_NULL', 'update' => 'CASCADE'))
            ->update();

        $this->table('favorite')
            ->addForeignKey('file', 'archive', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->addForeignKey('user', 'user', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->update();

        $this->table('flag')
            ->addForeignKey('file', 'archive', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->addForeignKey('user', 'user', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->update();

        $this->table('history')
            ->addForeignKey('file', 'archive', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->addForeignKey('user', 'user', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->update();

        $this->table('report')
            ->addForeignKey('file', 'archive', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->addForeignKey('user', 'user', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->update();

        $this->table('verify')
            ->addForeignKey('user', 'user', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->update();

        $this->table('vote')
            ->addForeignKey('user', 'user', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->addForeignKey('flag', 'flag', 'id', array('delete' => 'SET_NULL', 'update' => 'CASCADE'))
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}
