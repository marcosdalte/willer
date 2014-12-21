<?php

namespace Core {
	use \Rain\Tpl;

	trait TplEngine {
		public static function ready() {
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
	}
}

?>