<?php

namespace Application\Test {
    use \Exception as Exception;
    use \Core\Util;

    trait load {
        public static function load() {
            $vendor_json = Util::loadJsonFile(ROOT_PATH.'/vendor.json',true);
            $config_json = Util::loadJsonFile(ROOT_PATH.'/config.json',true);

            return array_merge($vendor_json,$config_json);
        }
    }
}