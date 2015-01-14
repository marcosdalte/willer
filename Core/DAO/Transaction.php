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

        protected function setResource($resource) {
            $this->resource = $resource;
        }

        public function getDatabase() {
            return $this->database;
        }

        protected function setDatabase($database) {
            $this->database = $database;
        }

        public function getLastInsertId() {
            return $this->last_insert_id;
        }

        protected function setLastInsertId($id) {
            $this->last_insert_id = $id;
        }

        public function getDatabaseInfo() {
            $database = $this->getDatabase();

            if (empty($database)) {
                $database = DB_DEFAULT;
            }

            return $GLOBALS["DATABASE_INFO"][$database];
        }

        public function connect($database = null) {
            if (!empty($database)) {
                if (!array_key_exists($database,$GLOBALS["DATABASE_INFO"])) {
                    throw new Exception("database_not_found_in_DATABASE_INFO");
                }

                $this->setDatabase($database);
            }

            $database_info = $this->getDatabaseInfo();

            try {
                $pdo = new PDO($database_info["DB_DRIVER"].":host=".$database_info["DB_HOST"].";port=".$database_info["DB_PORT"].";dbname=".$database_info["DB_NAME"],$database_info["DB_USER"],$database_info["DB_PASSWORD"]);

                if ($database_info["DB_DRIVER"] == "mysql") {
                    if ($database_info["DB_AUTOCOMMIT"] == 0) {
                        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);

                    } else if ($database_info["DB_AUTOCOMMIT"] == 1) {
                        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
                    }
                }

                if ($database_info["DB_DEBUG"] == 0) {
                    $pdo->setAttribute(PDO::ATTR_ERRMODE,0);

                } else if ($database_info["DB_DEBUG"] == 1) {
                    $pdo->setAttribute(PDO::ATTR_ERRMODE,1);
                }

            } catch (Exception $error) {
                throw new Exception($error);
            }

            $this->resource = $pdo;

            $GLOBALS["TRANSACTION"] =& $this;

            return $this;
        }
  
        public function beginTransaction($database = null) {
            if (!empty($database)) {
                if (!array_key_exists($database,$GLOBALS["DATABASE_INFO"])) {
                    throw new Exception("database_not_found_in_DATABASE_INFO");
                }

                $this->setDatabase($database);
            }

            try {
                $this->connect();
  
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
  
        public function lastInsertId($sequence_name = null) {
            try {
                $this->setLastInsertId($this->resource->lastInsertId($sequence_name));
  
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