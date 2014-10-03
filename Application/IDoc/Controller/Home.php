<?php

namespace Application\IDoc\Controller {
    use \Exception as Exception;
    use \Helper\Util;
    use \Helper\System;
    use \Helper\ProtectResource;
    use \Application\ALog\Model\LogRegister;
    use \Application\ALog\Model\LogError;

    class Home extends ProtectResource {
		/*
		 * Available POST,PUT,DELETE,GET or void(simple html)
		 *
		 */
		const ACCESS_METHOD = null;

        function __construct() {
            $log_register = new LogRegister(DB_LOG);
            $log_error = new LogError(DB_LOG);

            $csrf = Util::csrf();

            $GLOBALS["PERSIST"]->beginTransaction();

            $log_error_value = $log_error->orderBy(["id" => "desc"])->filter()->value();

            $GLOBALS["PERSIST"]->commit();

            // $log_register_value = $log_register->databaseUse(DB_LOG)->orderBy(["id" => "desc"])->filter()->value();

            $template_assign = [
                // "log_register" => $log_register_value,
                "log_error" => $log_error_value,
                "csrf" => $csrf,
                "page_view" => "home",
                "page_menu" => "menu",
                "template" => "default"];

			$template_engine = System::templateEngineReady();
            $template_engine->assign($template_assign);
            $template_engine->draw("template");
        }
    }
}

?>