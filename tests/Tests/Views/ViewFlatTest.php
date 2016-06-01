<?php
/*
 * File: ViewFlatTest.php
 * Holds: Tests the Flat view
 * Created: 02.06.2016
 * Project: Youkok2
 *
 */

namespace Youkok2\Tests\Views;

use Youkok2\Youkok2;

class ViewFlatTest extends \Youkok2\Tests\YoukokTestCase
{
    public function testViewFlatAbout() {
        $flat_about_wrapper = new Youkok2();
        $flat_about_view = $flat_about_wrapper->load('om', [
            'close_db' => false
        ]);
        $this->assertEquals('Youkok2\Views\Flat', get_class($flat_about_view));
        $this->assertEquals(200, $flat_about_wrapper->getStatus());
    }

    public function testViewFlatTerms() {
        $flat_terms_wrapper = new Youkok2();
        $flat_terms_view = $flat_terms_wrapper->load('retningslinjer', [
            'close_db' => false
        ]);
        $this->assertEquals('Youkok2\Views\Flat', get_class($flat_terms_view));
        $this->assertEquals(200, $flat_terms_wrapper->getStatus());
    }

    public function testViewFlatHelp() {
        $flat_help_wrapper = new Youkok2();
        $flat_hjelp_view = $flat_help_wrapper->load('hjelp', [
            'close_db' => false
        ]);
        $this->assertEquals('Youkok2\Views\Flat', get_class($flat_hjelp_view));
        $this->assertEquals(200, $flat_help_wrapper->getStatus());
    }
}
