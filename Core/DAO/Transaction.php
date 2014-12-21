<?php

namespace Core\DAO {
    use \PDO as PDO;
    use \Exception as Exception;

    class Transaction {
        private $resource;
        private $database;
        private $last_insert_id;
  
        public function __construct() {}

        public function getResource() {
            return $this->resource;
        }

        protected function setResource($value) {
            $this->resource = $value;
        }

        public function getDatabase() {
            return $this->database;
        }

        protected function setDatabase($value) {
            $this->database = $value;
        }

        public function getLastInsertId() {
            return $this->last_insert_id;
        }

        protected function setLastInsertId($value) {
            $this->last_insert_id = $value;
        }

        public function connect() {
            $database = $this->getDatabase();

            if (empty($database)) {
                $database = DB_DEFAULT;
            }

            $database_info = $GLOBALS["DATABASE_INFO"];

            try {
                $pdo = new PDO($database_info[$database]["DB_DRIVER"].":host=".$database_info[$database]["DB_HOST"].";port=".$database_info[$database]["DB_PORT"].";dbname=".$database_info[$database]["DB_NAME"],$database_info[$database]["DB_USER"],$database_info[$database]["DB_PASSWORD"]);

                if ($database_info[$database]["DB_DRIVER"] == "mysql") {
                    if ($database_info[$database]["DB_AUTOCOMMIT"] == 0) {
                        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);

                    } else if ($database_info[$database]["DB_AUTOCOMMIT"] == 1) {
                        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
                    }
                }

                if ($database_info[$database]["DB_DEBUG"] == 0) {
                    $pdo->setAttribute(PDO::ATTR_ERRMODE,1);

                } else if ($database_info[$database]["DB_DEBUG"] == 1) {
                    $pdo->setAttribute(PDO::ATTR_ERRMODE,0);
                }

            } catch (Exception $error) {
                throw new Exception($error);
            }

            $this->resource = $pdo;

            return $this;
        }

        public function databaseUse($name) {
            $this->setDatabase($name);

            return $this;
        }
  
        public function beginTransaction() {
            try {
                $GLOBALS["TRANSACTION"] &= $this->connect();
  
            } catch (Exception $error) {  
                throw new Exception($error);
            }

            try {
                $this->resource->beginTransaction();
  
            } catch (Exception $error) {  
                throw new Exception($error);
            }
  
            return $this;
        }
  
        public function commit() {
            try {
                $this->resource->commit();
  
            } catch (Exception $error) {  
                throw new Exception($error);
            }
  
            return $this;
        }
  
        public function rollBack() {
            try {
                $this->resource->rollBack();
  
            } catch (Exception $error) {
                throw new Exception($error);
            }
  
            return $this;
        }
  
        public function lastInsertId($name = null) {
            try {
                $this->setLastInsertId($this->resource->lastInsertId($name));
  
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