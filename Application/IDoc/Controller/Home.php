<?php

namespace Application\IDoc\Controller {
    use \Exception as Exception;
    use \Core\Util;
    use \Core\TplEngine;
    use \Core\Controller;
    use \Core\DAO\Transaction;
    use \Application\ALog\Model\Log;

    class Home extends Controller {
        public function __construct($request_method = null) {
            parent::__construct($request_method);

            $this->transaction_main = new Transaction(DB_DEFAULT);
            $this->transaction_log = new Transaction(DB_LOG);
        }

        public function index() {
            $log_error = new Log\Error($this->transaction_log);
            $log_user = new Log\User($this->transaction_log);
            $log_register = new Log\Register($this->transaction_log);

            try {
                $this->transaction_log->beginTransaction();

                // $log_error->save([
                //     "name" => "test4",
                //     "describe" => "test describe"]);

                // $log_user->save([
                //     "active" => 1,
                //     "name" => "test4",
                //     "publickey" => "123456",
                //     "dateadd" => Util::datetimeNow()]);

                // $log_register->save([
                //     "user_id" => $log_user,
                //     "error_id" => $log_error,
                //     "url" => "url/test",
                //     "post" => "post",
                //     "get" => "post",
                //     "message" => "message test",
                //     "dateadd" => Util::datetimeNow()]);

                // delete
                // $log_error->delete();
                // $log_user->delete();
                // $log_register->delete();

                // $log_error->delete([
                //     "id" => $log_error->id]);

                // $log_user->delete([
                //     "id" => $log_user->id]);

                // $log_register->delete([
                //     "id" => $log_register->id]);

                // get
                // $log_error->get([
                //     "name" => "test4"]);

                // filter
                $log_register
                    ->select()
                    ->filter()
                    ->orderBy(["id" => "asc"])
                    ->limit($page = 1,$limit = 5)
                    ->execute();

                $this->transaction_log->commit();

            } catch (Exception $error) {
                $this->transaction_log->rollBack();

                throw new Exception($error);
            }

            Util::renderToJson([
                "log_error" => $log_error,
                "log_user" => $log_user,
                "log_register" => $log_register->dump()]);

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

        public function contact() {
            $log_register = new Log\Register($this->transaction_log->connect());

            $log_register
                ->select()
                ->filter()
                ->orderBy(["id" => "asc"])
                ->limit($page = 1,$limit = 5)
                ->execute();

            Util::renderToJson([
                "log_register" => $log_register->dump()]);
        }
    }
}