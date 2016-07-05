<?php
/*
 * File: QueryParserTest.php
 * Holds: Testes the QueryParser class
 * Created: 04.07.2016
 * Project: Youkok2
 *
 */

namespace Youkok2\Tests\Utilities;

use Youkok2\Youkok2;
use Youkok2\Utilities\QueryParser;

class QueryParserTest extends \Youkok2\Tests\YoukokTestCase
{
    public function testQueryParser() {
        // Test null
        $youkok0 = new Youkok2();
        $query0 = new QueryParser($youkok0);
        $this->assertEquals('/', $query0->getPath());

        // Test empty
        $youkok1 = new Youkok2();
        $youkok1->setGet('q', '');
        $query1 = new QueryParser($youkok1);
        $this->assertEquals('/', $query1->getPath());

        // Test frontpage
        $youkok2 = new Youkok2();
        $youkok2->setGet('q', '/');
        $query2 = new QueryParser($youkok2);
        $this->assertEquals('/', $query2->getPath());

        // Test path with slash
        $youkok3 = new Youkok2();
        $youkok3->setGet('q', '/om');
        $query3 = new QueryParser($youkok3);
        $this->assertEquals('/om', $query3->getPath());

        // Test path with slash
        $youkok4 = new Youkok2();
        $youkok4->setGet('q', '/admin/bidrag');
        $query4 = new QueryParser($youkok4);
        $this->assertEquals('/admin/bidrag', $query4->getPath());

        // Test path with corrupt urls 1
        $youkok5 = new Youkok2();
        $youkok5->setGet('q', '///////');
        $query5 = new QueryParser($youkok5);
        $this->assertEquals('/', $query5->getPath());

        // Test path with corrupt urls 2
        $youkok6 = new Youkok2();
        $youkok6->setGet('q', '//admin//bidrag///');
        $query6 = new QueryParser($youkok6);
        $this->assertEquals('/admin/bidrag', $query6->getPath());
    }

    public function testQueryParserServer() {
        $youkok0 = new Youkok2();
        $youkok0->setServer('SERVER_SOFTWARE', 'PHP 5.2.21 Development Server');
        $youkok0->setServer('REQUEST_URI', '/om-oss?foobar');
        $query0 = new QueryParser($youkok0);
        $this->assertEquals('/om-oss', $query0->getPath());
    }
}
