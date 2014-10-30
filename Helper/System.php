<?php

namespace Helper {
	use \Helper\Util;
	use \DAO\Persist;
	use \Rain\Tpl;

	trait System {
		public static function templateEngineReady() {
			$template_config = [
				"tpl_dir" => vsprintf("%s/Application/%s/View/",[ROOT_PATH,APPLICATION]),
				"cache_dir" => vsprintf("%s/Application/%s/View/cache/",[ROOT_PATH,APPLICATION]),
				"path_replace" => false,
				"check_template_update" => true,
			];

			Tpl::configure($template_config);
			$template_engine = new Tpl;

			return $template_engine;
		}

		public static function appReady($URL) {
			System::iniSetReady();
			System::autoLoadReady();
			System::sessionReady();
			System::persistReady();
			System::urlRouteReady($URL,PAGE);
		}

		private static function iniSetReady() {
			date_default_timezone_set(TIMEZONE);
			ini_set("error_reporting",ERROR_REPORTING);
			ini_set("display_errors",DISPLAY_ERRORS);
		}

		private static function autoLoadReady() {
			spl_autoload_register(function ($class) {
				$class_ = $class;

				$class = str_replace("\\","/",$class);
				$class = sprintf("%s/%s.php",ROOT_PATH,$class);

				if (!file_exists($class)) {
					$scan_dir = array_diff(scandir(ROOT_PATH."/Vendor"),array("..","."));

					foreach ($scan_dir as $value) {
						$class = Util::str("%s/Vendor/%s/%s.php",[ROOT_PATH,$value,$class_]);
						$class = str_replace("\\","/",$class);

						if (file_exists($class)) {
							break;
						}

						$class = null;
					}
				}
				
				if (empty($class)) {
					Util::httpRedirect(URL_NOT_FOUND);

				}

				include_once $class;
			});
		}

		private static function sessionReady() {
			session_start();
		}

		private static function persistReady() {
			$GLOBALS["PERSIST"] = new Persist;
		}

		private static function urlRouteReady($url,$page) {
			$flag = false;

			foreach ($url as $key => $value) {
				if (preg_match($key,$page)) {
					$flag = true;

					define("APPLICATION",$value["application"]);
					define("CONTROLLER",$value["controller"]);
					define("PROTECT_RESOURCE",$value["protect_resource"]);

					$controller = Util::str("Application\\%s\\Controller\\%s",[APPLICATION,CONTROLLER]);

					try {
						new $controller();

					} catch (Exception $error) {
						Util::renderToJson($error);
					}
				}
			}

			if (empty($flag)) {
				Util::httpRedirect(URL_NOT_FOUND);
			}
		}
	}
}

?>
