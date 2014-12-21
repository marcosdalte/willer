<?php

namespace Core\DAO {
    use \PDO as PDO;
    use \Exception as Exception;
    use \Util;
    use \DAO\Connection;
    class DataDefinitionLanguage extends Connection {
        private $query_value;

        public function __construct() {
            $this->connect();
        }

        protected function getQueryValue() {
            return $this->query_value;
        }

        protected function setQueryValue($value) {
            $this->query_value = $value;
        }

        public function value() {
            return $this->getQueryValue();
        }

        public function dbCreate($params = []) {
            $db_name = Util::get($params,"DB_NAME",null);

            if (empty($db_name)) {
                throw new Exception("param missing");
            }

            $link = $this->getLink();

            $sql_consult = "CREATE DATABASE IF NOT EXISTS ".$db_name." CHARACTER SET UTF8 COLLATE utf8_general_ci";

            try {
                $prepare = $link->prepare($sql_consult);
                $prepare->execute();
                $result = $prepare->rowCount();

            } catch (Exception $error) {
                throw new Exception($error);
            }

            $this->setQueryValue($result);

            return $this;
        }

        public function dbDelete($params = []) {
            $db_name = Util::get($params,"DB_NAME",null);

            if (empty($db_name)) {
                throw new Exception("param missing");
            }

            $link = $this->getLink();

            $sql_consult = "DROP DATABASE ".$db_name."";

            try {
                $prepare = $link->prepare($sql_consult);
                $prepare->execute();
                $result = $prepare->rowCount();

            } catch (Exception $error) {
                throw new Exception($error);
            }

            $this->setQueryValue($result);

            return $this;
        }

        public function userCreate($params = []) {
            $user_name = Util::get($params,"USER_NAME",null);
            $server = Util::get($params,"SERVER",null);
            $password = Util::get($params,"PASSWORD",null);

            if (empty($user_name) || empty($server) || empty($password)) {
                throw new Exception("param missing");
            }

            $link = $this->getLink();

            $sql_consult = "CREATE USER '".$user_name."'@'".$server."' IDENTIFIED BY '".$password."'";

            try {
                $prepare = $link->prepare($sql_consult);
                $prepare->execute();
                $result = $prepare->rowCount();

            } catch (Exception $error) {
                throw new Exception($error);
            }

            $this->setQueryValue($result);

            return $this;
        }

        public function userDelete($params = []) {
            $user_name = Util::get($params,"USER_NAME",null);
            $server = Util::get($params,"SERVER",null);

            if (empty($user_name) || empty($server)) {
                throw new Exception("param missing");
            }

            $link = $this->getLink();

            $sql_consult = "DROP USER '".$user_name."'@'".$server."'";

            try {
                $prepare = $link->prepare($sql_consult);
                $prepare->execute();
                $result = $prepare->rowCount();

            } catch (Exception $error) {
                throw new Exception($error);
            }

            $this->setQueryValue($result);

            return $this;
        }

        public function __destruct() {
            unset($this);
        }
    }
}

?>