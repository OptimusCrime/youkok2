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

class UtilitiesTest extends \Youkok2\Tests\YoukokTestCase
{
    public function testQueryParser() {
        $youkok = new Youkok2();
        $youkok->setGet('q', '/');

        $query = new QueryParser($youkok);
        $this->assertEquals('/', $query);
    }
}
