<?php

namespace Core {
    use \Exception as Exception;

    trait Log {
        public static function write($message,$filename) {
        	file_put_contents($filename,$message,FILE_APPEND);
        }
    }
}