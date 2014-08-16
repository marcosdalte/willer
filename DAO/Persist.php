<?php

namespace DAO {
    use \PDO as PDO;
    use \Exception as Exception;

    class Persist {
        private $link;
        private $database;
        private $last_insert_id;
  
        public function __construct() {}

        public function getLink() {
            return $this->link;
        }

        protected function setLink($value) {
            $this->link = $value;
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

                if ($database_info[$database]["DB_AUTOCOMMIT"] === 0) {
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);

                } else if ($database_info[$database]["DB_AUTOCOMMIT"] === 1) {
					$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
				}

				if ($database_info[$database]["DB_DEBUG"] === 0) {
					$pdo->setAttribute(PDO::ATTR_ERRMODE,1);
                	$pdo->setAttribute(PDO::ERRMODE_EXCEPTION,1);

				} else if ($database_info[$database]["DB_DEBUG"] === 1) {
					$pdo->setAttribute(PDO::ATTR_ERRMODE,0);
                	$pdo->setAttribute(PDO::ERRMODE_EXCEPTION,0);
				}

            } catch (Exception $error) {
                throw new Exception($error);
            }

            $this->link = $pdo;

            return $this;
        }

        public function databaseUse($name) {
            $this->setDatabase($name);

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