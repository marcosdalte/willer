<?php

namespace Core\DAO {
    use \PDO as PDO;
    use \Exception as Exception;

    class Transaction {
        private $resource;
        private $database;
        private $last_insert_id;
        private $db_default = 'default';
        private $database_path = ROOT_PATH.'/database.json';

        public function __construct($database = null) {
            if (empty($database)) {
                $database = $this->db_default;
            }

            if (!file_exists($this->database_path)) {
                throw new Exception('database.json dont find in src folder');
            }

            $this->database_path = json_decode(file_get_contents($this->database_path),true);

            if (!array_key_exists($database,$this->database_path)) {
                throw new Exception('database not found in database.json');
            }

            $this->setDatabase($database);
        }

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
                $database = $this->db_default;
            }

            return $this->database_path[$database];
        }

        public function connect() {
            $database_info = $this->getDatabaseInfo();

            try {
                if (in_array($database_info['driver'],['mysql','pgsql'])) {
                    $pdo = new PDO(vsprintf('%s:host=%s;port=%s;dbname=%s',[$database_info['driver'],$database_info['host'],$database_info['port'],$database_info['name']]),$database_info['user'],$database_info['password']);

                } else if ($database_info['driver'] == 'sqlite') {
                    $pdo = new PDO(vsprintf('%s:%s',[$database_info['driver'],$database_info['host']]));
                }

                if ($database_info['driver'] == 'mysql') {
                    if ($database_info['autocommit'] == 0) {
                        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);

                    } else if ($database_info['autocommit'] == 1) {
                        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
                    }
                }

                if ($database_info['debug'] == 0) {
                    $pdo->setAttribute(PDO::ATTR_ERRMODE,0);

                } else if ($database_info['debug'] == 1) {
                    $pdo->setAttribute(PDO::ATTR_ERRMODE,1);
                }

            } catch (Exception $error) {
                throw new Exception($error);
            }

            $this->resource = $pdo;

            return $this;
        }

        public function beginTransaction() {
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
            if (!empty($this->resource)) {
                try {
                    $this->resource->commit();

                } catch (Exception $error) {
                    throw new Exception($error);
                }
            }

            return $this;
        }

        public function rollBack() {
            if (!empty($this->resource)) {
                try {
                    $this->resource->rollBack();

                } catch (Exception $error) {
                    throw new Exception($error);
                }
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
