<?php

namespace Core {
	use \Exception as Exception;
	use \SplFileInfo as SplFileInfo;

	trait Util {
		public static function get($input,$key,$default = null) {
			if (!is_array($input) && !(is_object($input))) {
				return $default;
			}

			if (is_array($input)) {
				return isset($input[$key]) ? !empty($input[$key]) ? $input[$key] : $default : $default;

			} else if (is_object($input)) {
				return isset($input->$key) ? !empty($input->$key) ? $input->$key : $default : $default;
			}
		}

		public static function csrf() {
			$csrf = md5(uniqid(mt_rand(),true));
			$_SESSION["csrf"] = $csrf;

			return $csrf;
		}

		public static function httpRedirect($url) {
			header('Location: '.$url);
		}

		public static function load($application_path = null) {
			$scandir_root = array_diff(scandir(ROOT_PATH),array('..','.'));

			$scandir_application = null;

			if (!empty($application_path)) {
				$scandir_application = array_diff(scandir(vsprintf('%s/Application/%s',[ROOT_PATH,$application_path])),array('..','.'));
			}

			$load_var = [];

			foreach ($scandir_root as $file) {
				$spl_file_info = new SplFileInfo($file);

				if ($spl_file_info->getExtension() == 'json') {
					$key = $spl_file_info->getBasename('.json');

					$load_var[$key] = json_decode(file_get_contents(vsprintf('%s/%s',[ROOT_PATH,$file])),true);
				}
			}

			if (!empty($scandir_application)) {
				foreach ($scandir_application as $file) {
					$spl_file_info = new SplFileInfo($file);

					if ($spl_file_info->getExtension() == 'json') {
						$key = vsprintf('%s_%s',[$application_path,$spl_file_info->getBasename('.json')]);

						$load_var[$key] = json_decode(file_get_contents(vsprintf('%s/Application/%s',[ROOT_PATH,$file])),true);
					}
				}
			}

			return $load_var;
		}

		public static function exceptionToJson($exception = null) {
			header('Content-Type: application/json');

			if (empty($exception)) {
				throw new Exception('Value is null to exception');
			}

			$exception = json_encode(array(
				'message' => $exception->getMessage(),
				'file' => $exception->getFile(),
				'line' => $exception->getLine(),
				'success' => false,
			));

			print $exception;

			exit();
		}

		public static function renderToJson($data = []) {
			header('Content-Type: application/json');

			$data = json_encode($data,JSON_UNESCAPED_UNICODE);

			print $data;

			exit();
		}
	}
}
