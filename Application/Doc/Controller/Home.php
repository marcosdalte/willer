<?php

namespace Application\Doc\Controller {
    use \Exception;
    use \Util;
    use \Auth\ProtectResource;
    use \Rain\Tpl;
    use \Application\ALog\Model\ALogRegister;
    class Home extends ProtectResource {
        function __construct($request) {
            $a_log_register = new ALogRegister;

            $csrf = Util::csrf($request);

            $test_info = "test information";

            $a_log_register_value = $a_log_register->databaseUse(DB_LOG)->filter()->value();

            $rain_tpl_assign = [
                "test_info" => $test_info,
                "log_register" => $a_log_register_value,
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