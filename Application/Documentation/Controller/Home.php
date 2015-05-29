<?php

namespace Application\Documentation\Controller {
    use \Exception as Exception;
    use \Core\Util;
    use \Core\TplEngine;
    use \Core\Controller;
    use \Core\DAO\Transaction;
    use \Application\Log\Model\Log;
    use \Application\Container\Model\Service;
    use \Application\Container\Model\Container;
    use \Application\Client\Model\Client;

    class Home extends Controller {
        public function __construct($request_method = null) {
            parent::__construct($request_method);

            $this->transaction_default = new Transaction(DB_DEFAULT);
            $this->transaction_mysql = new Transaction(DB_MYSQL);
            $this->transaction_sqlite = new Transaction(DB_SQLITE);
        }

        public function index($url_fragment) {
            $log_error = new Log\Error($this->transaction_default);
            $log_errortype = new Log\ErrorType($this->transaction_default);
            $log_user = new Log\User($this->transaction_default);
            $log_register = new Log\Register($this->transaction_default);

            $service = new Service\Service($this->transaction_mysql);
            $container = new Container\Container($this->transaction_mysql);

            $client = new Client\Client($this->transaction_sqlite);

            try {
                // $this->transaction_default->beginTransaction();
                $this->transaction_mysql->beginTransaction();
                // $this->transaction_sqlite->beginTransaction();

                $service->save([
                    "nome" => "servico",
                    "descricao" => "servico"]);

                $container->save([
                    "nome" => "container",
                    "servico_id" => $service,
                    "descricao" => "container"]);

                $container_list = $container
                    ->where()
                    ->orderBy()
                    ->limit(1,5)
                    ->execute();

                // $service_list = $service
                //     ->where()
                //     ->orderBy()
                //     ->limit(1,5)
                //     ->execute();

                // print_r($service->lastQuery());

                // $container_list = $container
                //     ->where()
                //     ->orderBy()
                //     ->limit(1,5)
                //     ->execute();

                // $client->save([
                //     "nome" => "teste123456rewrewr3424",
                //     "descricao" => "lalalalalawqwqewqewqel"]);
                //
                // $client_list = $client
                //     ->where()
                //     ->orderBy()
                //     ->limit(2,5)
                //     ->execute();

                // $service_list = $service
                //     ->where()
                //     ->orderBy()
                //     ->limit(1,5)
                //     ->execute();

                // $container_list = $container
                //     ->where()
                //     ->orderBy()
                //     ->limit(1,5)
                //     ->execute();

                // $this->transaction_default->connect();

                // $log_error->save([
                //     "name" => "test4",
                //     "describe" => "test describe"]);

                // $log_error->get([
                //     "name" => "test4"]);

                // $log_error->name = "test5";
                // $log_error->save();

                // $log_user->get([
                //     "name" => "test4"]);

                // $log_register->save([
                //     "user_id" => $log_user,
                //     "error_id" => $log_error,
                //     "url" => "testeeeee",
                //     "post" => "{}",
                //     "get" => "{}",
                //     "message" => "message of test",
                //     "dateadd" => Util::datetimeNow()]);

                // $log_errortype->name = "nameteste15";
                // $log_errortype->save();

                // $log_errortype->get(["name" => "nameteste14"]);

                // $log_errortype->name = "nameteste99999";
                // $log_errortype->save();

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

                // $log_errortype->get([
                //     "id" => "1"]);
                //
                // $log_error->save([
                //     "tipo_id" => $log_errortype,
                //     "nome" => "erro_teste",
                //     "descricao" => "descricao_teste"]);

                // $log_error->get([
                //     "nome" => "erro_teste"]);

                // print_r($log_errortype->lastQuery());

                // $log_register
                //     ->where(["register.id" => [11,10,9,8,1]])
                //     ->update("url" => "blabla-bla blaaaaa");
                //
                // $log_register
                //     ->where(["register.id" => [11,10,9,8,1]])
                //     ->delete();

                // filter
                // $log_register_list = $log_register
                //     ->where(["register.id" => [11,10,9,8,1]])
                //     ->orderBy(["id" => "asc"])
                //     ->limit($page = 1,$limit = 5)
                //     ->execute(["join" => "left"]);

                // print "<pre>";
                // print_r($log_register->lastQuery());
                // print_r($log_register->dump());
                // print_r($log_register_list);
                // exit();

                // foreach ($log_register_list as $i => $log_register_) {
                    // $log_register_->url = "1231232ewewrf";
                    // $log_register_->save();

                    // print "</br>------------------------</br>";
                    // print_r(get_class_methods($log_register_));
                    // print "</br>------------------------</br>";
                    // print_r(get_class_methods($log_register_->error_id));
                    // print "</br>------------------------</br>";

                    // $log_register_->error_id->describe = "alallalalalal";
                    // $log_register_->error_id->save();

                    // $log_register_->error_id->type_id->name = mt_rand();
                    // $log_register_->error_id->type_id->save();

                // }

                // $this->transaction_default->commit();
                $this->transaction_mysql->commit();
                // $this->transaction_sqlite->commit();

            } catch (Exception $error) {
                // $this->transaction_default->rollBack();
                $this->transaction_mysql->rollBack();
                // $this->transaction_sqlite->rollBack();

                throw new Exception($error);
            }

            Util::renderToJson($container_list);
        }

        public function contact($url_fragment) {}
    }
}
