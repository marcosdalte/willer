<?php

namespace Core {
	use \Exception as Exception;

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
			$mt_rand = mt_rand();
			$csrf = vsprintf('%s/%s',[$_SERVER['REMOTE_ADDR'],$mt_rand]);
			$csrf = hash("whirlpool",$csrf);
			$_SESSION["csrf"] = $csrf;

			return $csrf;
		}

		public static function httpRedirect($url) {
			header('Location: '.$url);
		}

		public static function exceptionToJson($exception = null) {
			header('Content-Type: application/json');

			if (empty($exception)) {
				exit();
			}

			$exception = json_encode(array(
				'message' => $exception->getMessage(),
				'file' => $exception->getFile(),
				'line' => $exception->getLine(),
			));

			exit($exception);
		}

		public static function renderToJson($data = []) {
			header('Content-Type: application/json');

			$data = json_encode($data,JSON_UNESCAPED_UNICODE);

			exit($data);
		}

		public static function urlRequest($url,$params_get = null,$params_post = null,$params_header = null) {
			if (!empty($params_get)) {
				$url = vsprintf('%s/?%s',[$url,http_build_query($params_get)]);
			}

			if (!empty($params_post)) {
	            $params_post = http_build_query($params_post);
	        }

			$http = curl_init();
			curl_setopt($http,CURLOPT_URL,$url);
			curl_setopt($http,CURLOPT_POST,1);
			curl_setopt($http,CURLOPT_POSTFIELDS,$params_post);
			curl_setopt($http,CURLOPT_HEADER,0);
			curl_setopt($http,CURLOPT_HTTPHEADER,$params_header);
			curl_setopt($http,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($http,CURLOPT_TIMEOUT,10);
			$output = curl_exec($http);
			curl_close($http);

			return json_decode($output,true);
		}
	}
}
