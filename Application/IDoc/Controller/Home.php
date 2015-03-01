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

        public function index($url_fragment) {
            $log_error = new Log\Error($this->transaction_log);
            $log_errortype = new Log\ErrorType($this->transaction_log);
            $log_user = new Log\User($this->transaction_log);
            $log_register = new Log\Register($this->transaction_log);

            try {
                $this->transaction_log->beginTransaction();
                // $this->transaction_log->connect();

                // $log_error->save([
                //     "name" => "test4",
                //     "describe" => "test describe"]);

                // $log_errortype->name = "nameteste15";
                // $log_errortype->save();

                // $log_errortype->get(["name" => "nameteste15"]);

                // $log_errortype->delete();

                // $log_errortype->name = "nameteste14";
                // $log_errortype->save();

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
                    ->where(["register.id" => [11,10,9,8]])
                    ->orderBy(["id" => "asc"])
                    ->limit($page = 1,$limit = 5)
                    ->execute(["join" => "left"]);

                $this->transaction_log->commit();

            } catch (Exception $error) {
                $this->transaction_log->rollBack();

                throw new Exception($error);
            }

            // $log_register
            //     ->where(["register.id" => [1,11,10,9,8]])
            //     ->orderBy(["id" => "asc"])
            //     ->limit($page = 1,$limit = 5)
            //     ->execute(["join" => "left"]);

            Util::renderToJson([
                "last_query" => $log_register->lastQuery(),
                "log_error" => $log_error,
                "log_errortype" => $log_errortype,
                "log_errortype_dump" => $log_errortype,
                "log_user" => $log_user,
                "log_register" => $log_register->dump()]);
        }

        public function contact($url_fragment) {}
    }
}