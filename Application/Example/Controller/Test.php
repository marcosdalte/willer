<?php

namespace Application\Example\Controller {
    use \Exception;
    use \Util;
    use \Auth\ProtectResource;
    use \Rain\Tpl;
    use \Application\Log\Model\ALogError;
    class Test extends ProtectResource {
        function __construct($request) {
            $a_log_error = new ALogError;

            $csrf = Util::csrf($request);

            $test_info = "test information";

            $a_log_error_value = $a_log_error->databaseUse(DB_LOG)->filter()->value();

            $rain_tpl_assign = [
                "test_info" => $test_info,
                "log_error_value" => $a_log_error_value,
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