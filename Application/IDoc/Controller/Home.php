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
            $transaction_main = new Transaction(DB_DEFAULT);
            $transaction_log = new Transaction(DB_LOG);

            $log_error = new Log\Error($transaction_log);
            $log_user = new Log\User($transaction_log);
            $log_register = new Log\Register($transaction_log);

            try {
                $transaction_log->beginTransaction();

                $log_error->save([
                    "name" => "test4",
                    "describe" => "test describe"]);

                $log_user->save([
                    "active" => 1,
                    "name" => "test4",
                    "publickey" => "123456",
                    "dateadd" => Util::datetimeNow()]);

                $log_register->save([
                    "user_id" => $log_user,
                    "error_id" => $log_error,
                    "url" => "url/test",
                    "post" => "post",
                    "get" => "post",
                    "message" => "message test",
                    "dateadd" => Util::datetimeNow()]);

                // $log_error->name = "teucu7";
                // $log_error->describe = "bbsdofijdfjsdlf";
                // $log_error->save();

                // $log_error->get([
                //     "name" => "teucu5"]);

                // $log_error->delete();

                // $log_error->delete([
                //     "name" => "teucu5"]);

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

                $transaction_log->commit();

            } catch (Exception $error) {
                $transaction_log->rollBack();

                throw new Exception($error);
            }

            Util::renderToJson([
                "log_error" => $log_error,
                "log_user" => $log_user,
                "log_register" => $log_register]);

            // $template_assign = [
                // "log_register" => $log_register_value,
                // "log_error" => $log_error_value,
                // "csrf" => $csrf,
                // "page_view" => "home",
                // "page_menu" => "menu",
                // "template" => "default"];

            // $template_engine = TplEngine::ready()
            //     ->assign($template_assign)
            //     ->draw("template");
        }
    }
}