<?php

namespace Core {
    use \Exception as Exception;

    trait Log {
        public static function write($message,$filename = null) {
        	if (empty($filename)) {
        		$filename = ROOT_PATH."/log.txt";
        	}

        	file_put_contents($filename,$message,FILE_APPEND);
        }
    }
}