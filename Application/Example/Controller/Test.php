<?php

namespace Application\Example\Controller {
    use \Exception;
    use \Util;
    use \Auth\ProtectResource;
    use \Rain\Tpl;
    use \Application\Log\Model\LogError;
    class Test extends ProtectResource {
        function __construct($request) {
            $log_error = new LogError;

            $csrf = Util::csrf($request);
            $_SESSION["csrf"] = $csrf;

            $test_info = "test information";

            $log_error_value = $log_error->databaseUse(DB_LOG)->filter()->value();

            $rain_tpl_assign = [
                "test_info" => $test_info,
                "log_error_value" => $log_error_value,
                "csrf" => $csrf,
                "page_view" => "home",
                "page_menu" => "menu",
                "template" => "default"];

            Tpl::configure($GLOBALS["rain_tpl_configure"]);

            $rain_tpl = new Tpl;
            $rain_tpl->assign($rain_tpl_assign);
            $rain_tpl->draw("template");
        }
    }
}

?>