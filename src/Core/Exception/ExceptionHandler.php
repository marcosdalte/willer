<?php

namespace Core\Exception {
	use \Exception as Exception;
	use \Core\Util;

	class ExceptionHandler extends Exception {
		private $exception_name;

		public function __construct($exception_name = null) {
			$this->exception_name = $exception_name;
        }

        public function getException() {
        	$load = Util::load();

            $exception_dict = Util::get($load,'exception',null);
            $exception = Util::get($exception_dict,$this->exception_name,null);

            return [
        		'exception_name' => $this->exception_name,
        		'exception_message' => $exception];
        }
	}
}