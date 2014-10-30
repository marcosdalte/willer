<?php

namespace Helper {
	trait Util {
		protected $connection = null;

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

		public static function isEmpty($list = []) {
			if (empty($list)) {
				return false;
			}

			foreach ($list as $value) {
				if (empty($value)) {
					return false;
				}
			}

			return true;
		}

		public static function datetimeNow() {
			return date("Y-m-d H:i:s");
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

		public static function renderToJson($data = []) {
			header("Content-Type: application/json");
			print json_encode($data,JSON_UNESCAPED_UNICODE);
		}

		public static function apiRequest($url,$params_get,$params_post) {
			if (!empty($params_get)) {
				$url = Util::str("%s/?%s",[$url,http_build_query($params_get)]);
			}

			$http = curl_init();
			curl_setopt($http,CURLOPT_URL,$url);
			curl_setopt($http,CURLOPT_POST,1);
			curl_setopt($http,CURLOPT_POSTFIELDS,$params_post);
			curl_setopt($http,CURLOPT_HEADER,0);
			curl_setopt($http,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($http,CURLOPT_TIMEOUT,10);
			$output = curl_exec($http);
			curl_close($http);

			return json_decode($output,true);
		}
	}
}

?>