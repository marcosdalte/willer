<?php

require(ROOT_SRC_PATH.'/src/Core/Util.php');

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

    public function testLoad() {
        $application_path = 'Test';
        $subset = [
            "vendor" => [
                "lib_1" => "value_lib_1",
                "lib_2" => "value_lib_2"
            ],
            "Test_config" => [
                "key_1" => "value_1",
                "key_2" => "value_2",
                "key_3" => [
                    "sub_key_3_1" => "sub_value_3_1"
                ]
            ]];

        $this->assertArraySubset($subset,Util::load($application_path));
    }
}
