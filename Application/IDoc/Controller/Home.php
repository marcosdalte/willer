<?php

namespace Application\IDoc\Controller {
    use \Exception as Exception;
    use \Helper\Util;
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
            $log_register = new LogRegister;
            $log_error = new LogError;

            $csrf = Util::csrf();

            $log_error_value = $log_error->databaseUse(DB_LOG)->orderBy(["id" => "desc"])->filter()->value();

            // $log_register_value = $log_register->databaseUse(DB_LOG)->orderBy(["id" => "desc"])->filter()->value();

            $template_assign = [
                // "log_register" => $log_register_value,
                "log_error" => $log_error_value,
                "csrf" => $csrf,
                "page_view" => "home",
                "page_menu" => "menu",
                "template" => "default"];

			$template_engine = System::templateEngine();
            $template_engine->assign($template_assign);
            $template_engine->draw("template");
        }
    }
}

?>