<?php

require(ROOT_PATH.'/src/Core/Util.php');

use \Exception as Exception;
use \Core\Util;

class UtilTest extends PHPUnit_Framework_TestCase {
    public function testGetArrayDict() {
    	$array_dict = ['key1' => 'value1','key2' => 'value2'];

        $this->assertEquals('value1',Util::get($array_dict,'key1',null));
    }

    public function testGetObject() {
        $standard_test = new stdClass();
        $standard_test->propert1 = 'value1';

        $this->assertEquals('value1',Util::get($standard_test,'propert1',null));
    }
}
