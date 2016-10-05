<?php
namespace Youkok2\Tests\Utilities;

use Youkok2\Utilities\TemplateHelper;

class TemplateHelperTest extends \Youkok2\Tests\YoukokTestCase
{

    public function testTemplateHelper() {
        $url_empty = TemplateHelper::urlFor('');
        $url_wrong_identifier = TemplateHelper::urlFor('foobar');
        $url_frontpage = TemplateHelper::urlFor('frontpage');
        $url_admin = TemplateHelper::urlFor('admin_scripts');
        $url_params = TemplateHelper::urlFor('archive', [
            'foo', 'bar'
        ]);

        $this->assertEquals('', $url_empty);
        $this->assertEquals('', $url_wrong_identifier);
        $this->assertEquals('', $url_frontpage);
        $this->assertEquals('admin/scripts', $url_admin);
        $this->assertEquals('emner/foo/bar/', $url_params);
    }
}
