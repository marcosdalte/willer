<?php

namespace DAO {
    use \PDO as PDO;
    use \Exception as Exception;
    use \Util;
    abstract class Connection {
        private $link;
        private $database;
        private $last_insert_id;
  
        public function __construct() {}

        protected function getLink() {
            return $this->link;
        }

        protected function setLink($value) {
            $this->link = $value;
        }

        protected function getDatabase() {
            return $this->database;
        }

        protected function setDatabase($value) {
            $this->database = $value;
        }

        protected function getLastInsertId() {
            return $this->last_insert_id;
        }

        protected function setLastInsertId($value) {
            $this->last_insert_id = $value;
        }

        protected function connect() {
            $database = $this->getDatabase();

            if (empty($database)) {
                $database = DB_DEFAULT;
            }

            $database_info = $GLOBALS["database_info"];

            try {
                $pdo = new PDO($database_info[$database]["DB_DRIVER"].":host=".$database_info[$database]["DB_HOST"].";port=".$database_info[$database]["DB_PORT"].";dbname=".$database_info[$database]["DB_NAME"],$database_info[$database]["DB_USER"],$database_info[$database]["DB_PASSWORD"]);

                if (DB_AUTOCOMMIT) {
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);
                }

                $pdo->setAttribute(PDO::ATTR_ERRMODE,1);
                $pdo->setAttribute(PDO::ERRMODE_EXCEPTION,1);
  
            } catch (Exception $error) {
                throw new Exception($error);
            }

            $this->link = $pdo;

            return $this;
        }

        public function databaseUse($name) {
            $this->setDatabase($name);
            $this->connect();

            return $this;
        }
  
        public function beginTransaction() {
            try {
                $this->link->beginTransaction();
  
            } catch (Exception $error) {  
                throw new Exception($error);
            }
  
            return $this;
        }
  
        public function commit() {
            try {
                $this->link->commit();
  
            } catch (Exception $error) {  
                throw new Exception($error);
            }
  
            return $this;
        }
  
        public function rollBack() {
            try {
                $this->link->rollBack();
  
            } catch (Exception $error) {
                throw new Exception($error);
            }
  
            return $this;
        }
  
        public function lastInsertId($name = null) {
            try {
                $this->setLastInsertId($this->link->lastInsertId($name));
  
            } catch (Exception $error) {  
                throw new Exception($error);
            }
  
            return $this->getLastInsertId();
        }
  
        public function __destruct() {
            unset($this);
        }
    }
}
  
?>