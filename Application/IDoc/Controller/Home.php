<?php

namespace Application\IDoc\Controller {
    use \Exception as Exception;
    use \Core\Util;
    use \Core\TplEngine;
    use \Core\Controller;
    use \Core\DAO\Transaction;
    use \Application\ALog\Model\Log;

    class Home extends Controller {
        function __construct() {
            $transaction = new Transaction;
            $log_register = new Log\Register();
            $log_error = new Log\Error();
            $log_user = new Log\User();

            $csrf = Util::csrf();

            try {
                // $transaction->beginTransaction(DB_LOG);
                $transaction->connect(DB_LOG);

                // $log_error->save([
                //     "name" => "teucu8",
                //     "describe" => "lalalalal"]);

                // $log_error->name = "teucu7";
                // $log_error->describe = "bbsdofijdfjsdlf";
                // $log_error->save();

                // $log_error->get([
                //     "name" => "teucu5"]);

                // $log_error->delete();

                // $log_error->delete([
                //     "name" => "teucu5"]);

                $log_register->save([
                    "user_id" => ,
                    "error_id" => ,
                    "url" => ,
                    "post" => ,
                    "get" => ,
                    "message" => ,
                    "dateadd" => ,]);

                // $total = $log_error
                //     ->executeRowsTotal();
                //     ->dump();

                // $log_error
                //     ->select()
                //     ->orderBy(["id" => "asc","name" => "asc"])
                //     ->limit(
                //         $page = 1,
                //         $limit = 5)
                //     ->filter()
                //     ->execute();

                // $transaction->commit();

            } catch (Exception $error) {
                // $transaction->rollBack();

                throw new Exception($error);
            }

            Util::renderToJson($log_register);

            // $template_assign = [
                // "log_register" => $log_register_value,
                // "log_error" => $log_error_value,
                // "csrf" => $csrf,
                // "page_view" => "home",
                // "page_menu" => "menu",
                // "template" => "default"];

			// $template_engine = TplEngine::ready()->assign($template_assign)->draw("template");
        }
    }
}

?>