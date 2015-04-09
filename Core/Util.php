<?php

namespace Core {
	use \DateTime as DateTime;

	trait Util {
		public static function str($output,$value) {
			return vsprintf($output,$value);
		}

		public static function get($input,$key,$default = null) {
			if (!is_array($input) and !(is_object($input))) {
				return $default;
			}

			if (is_array($input)) {
				return isset($input[$key]) ? !empty($input[$key]) ? $input[$key] : $default : $default;

			} else if (is_object($input)) {
				return isset($input->$key) ? !empty($input->$key) ? $input->$key : $default : $default;
			}
		}

		public static function datetimeNow() {
			$date_time = new DateTime();
			$format = $date_time->format("Y-m-d H:i:s");

			return $format;
		}

		public static function csrf() {
			$mt_rand = mt_rand();
			$csrf = Util::str("%s/%s",[$_SERVER["REMOTE_ADDR"],$mt_rand]);
			$csrf = hash("whirlpool",$csrf);
			$_SESSION["csrf"] = $csrf;

			return $csrf;
		}

		public static function httpRedirect($url) {
			header("Location: ".$url);
		}

		public static function exceptionToJson($exception = null) {
			header("Content-Type: application/json");

			if (empty($exception)) {
				exit();
			}

			$exception = json_encode(array(
				"message" => $exception->getMessage(),
				"file" => $exception->getFile(),
				"line" => $exception->getLine(),
			));

			exit($exception);
		}

		public static function renderToJson($data = []) {
			header("Content-Type: application/json");

			$data = json_encode($data,JSON_UNESCAPED_UNICODE);

			exit($data);
		}

		public static function urlRequest($url,$params_get = null,$params_post = null,$params_header = null) {
			if (!empty($params_get)) {
				$url = Util::str("%s/?%s",[$url,http_build_query($params_get)]);
			}

			if (!empty($params_post)) {
	            $params_post = http_build_query($params_post);
	        }

			$http = curl_init();
			curl_setopt($http,CURLOPT_URL,$url);
			// curl_setopt($http,CURLOPT_USERPWD,"AXwWTbM2FCotK4Sv4Sj349kONiUhpIjoQYhnYskzdLOCMTuqGrTJpaGWz47BAz0zFcAJ5zb025DwE1fQ:EPZdjBpbQU1GnkjNkx1pxDjE9Lf3f0FwEzmpLAajg0e5jDNfCxlCM9Vwavr8uF4gFjG_ROO6OSP8Y_fM");
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