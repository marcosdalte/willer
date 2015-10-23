<?php

namespace Core\Exception {
	use \Exception as Exception;
	use \Core\Util;

	class ExceptionHandler extends Exception {
		public function __construct($exception_name = null) {
            Util::load();
        }
	}
}