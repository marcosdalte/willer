<?php

namespace Application\IDoc\Controller {
    use \Exception as Exception;
    use \Core\Util;
    use \Core\TplEngine;
    use \Core\Controller;
    use \Core\DAO\Transaction;
    use \Application\ALog\Model\Log;

    class Home extends Controller {
		protected $api_rest_rule = ["GET","POST","PUT","DELETE"];

        function __construct() {
            try {
                $transaction = new Transaction;
                $log_register = new Log\LogRegister(DB_LOG);
                $log_error = new LogError(DB_LOG);

            } catch (Exception $error) {
                throw new Exception($error);
            }

            $csrf = Util::csrf();

            $transaction->beginTransaction();

            $log_register = $log_register->save([
                "log_user_id" => null,
                "log_error_id" => null,
                "url" => "test",
                "post" => "test",
                "get" => "test",
                "message" => "test"]);

            $transaction->commit();

            $template_assign = [
                // "log_register" => $log_register_value,
                "log_error" => $log_error_value,
                "csrf" => $csrf,
                "page_view" => "home",
                "page_menu" => "menu",
                "template" => "default"];

			$template_engine = TplEngine::ready()->assign($template_assign)->draw("template");
        }
    }
}

?>