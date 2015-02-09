<?php

namespace Core\DAO {
    use \PDO as PDO;
    use Exception as Exception;
    use \Core\Util;

    abstract class DataManipulationLanguage {
        private $transaction;
        private $db_escape;
        private $field;
        private $related;
        private $limit;
        private $order_by;
        private $primary_key;
        private $last_insert_id;
        private $query;
        private $query_value;
        private $register;

        public function __construct(Transaction $transaction = null) {
            if (empty($transaction)) {
                throw new Exception("transaction object doesn't loaded");
            }

            $this->setTransaction($transaction);
            $this->definePrimaryKey(null);

            $get_database_info = $this->transaction->getDatabaseInfo();
            $db_driver = $get_database_info["DB_DRIVER"];

            if ($db_driver == "mysql") {
                $this->db_escape = "";

            } else if ($db_driver == "pgsql") {
                $this->db_escape = "\"";
            }
        }

        private function getTableName() {
            return $this->name();
        }

        private function getTableColumn() {
            return $this->column();
        }

        private function getTableSchema() {
            return $this->schema();
        }

        protected function getTransaction() {
            return $this->transaction;
        }

        protected function setTransaction($transaction) {
            $this->transaction = $transaction;
        }

        private function getField() {
            return $this->field;
        }

        private function setField($value) {
            $this->field = $value;
        }

        private function getRelated() {
            return $this->related;
        }

        private function setRelated($value) {
            $this->related = $value;
        }

        private function getLimit() {
            return $this->limit;
        }

        private function setLimit($limit) {
            $this->limit = $limit;
        }

        private function getOrderBy() {
            return $this->order_by;
        }

        private function setOrderBy($value) {
            $this->order_by = $value;
        }

        protected function getPrimaryKey() {
            return $this->primary_key;
        }

        private function setPrimaryKey($column) {
            $this->primary_key = $column;
        }

        private function getLastInsertId() {
            return $this->last_insert_id;
        }

        private function setLastInsertId($id) {
            $this->last_insert_id = $id;
        }

        private function getQuery() {
            return $this->query;
        }

        private function setQuery($query) {
            $this->query = $query;
        }

        private function getQueryValue() {
            return $this->query_value;
        }

        private function setQueryValue($query_value) {
            $this->query_value = $query_value;
        }

        private function getRegister() {
            return $this->register;
        }

        private function setRegister($register) {
            $this->register = $register;
        }

        private function definePrimaryKey($column = null) {
            $table_schema = $this->schema();

            foreach ($table_schema as $i => $value) {
                if ($value->method == "primaryKey") {
                    $column = $i;
                    break;
                }
            }

            if (empty($column)) {
                throw new Exception("primary key missing in schema");
            }

            $this->setPrimaryKey($column);
        }

        public function select($field = []) {
            if (!empty($field)) {
                $this->setField($field);

            } else {
                $field = $this->getTableColumn();
                $field_list = [];

                foreach ($field as $i => $value) {
                    $field_list[] = $i;

                }

                $this->setField($field_list);
            }

            return $this;
        }

        public function orderBy($order_by = []) {
            if (!empty($order_by)) {
                $order_by_list = [];

                foreach ($order_by as $i => $value) {
                    $order_by_list[] = vsprintf("%s %s",[$i,$value]);
                }

                $order_by_list = implode(",",$order_by_list);
                $order_by_list = vsprintf("order by %s",[$order_by_list,]);

                $this->setOrderBy($order_by_list);
            }

            return $this;
        }

        public function limit($page = 1,$limit = QUERY_LIMIT_ROW) {
            $limit_value = null;

            if ($page <= 1) {
                $limit_value = vsprintf("limit %s offset 0",[$limit,]);

            } else {
                $page -= 1;
                $page_x_limit = $page * $limit;
                $limit_value = vsprintf("limit %s offset %s",[$limit,$page_x_limit]);
            }

            $this->setLimit($limit_value);

            return $this;
        }

        private function related() {
            // $table_column = $this->getTableColumn();
            // $table_name = $this->getTableName();
            // $table_schema = $this->getTableSchema();
            // $field = $this->getField();
        }

        public function filter($where = []) {
            $table_column = $this->getTableColumn();
            $table_name = $this->getTableName();
            $table_schema = $this->getTableSchema();
            $field = $this->getField();

            $table_name_with_escape = vsprintf("%s%s%s",[$this->db_escape,$table_name,$this->db_escape]);

            if (empty($field)) {
                $field = $this->select([])->getField();
            }

            $field = implode(",",$field);

            $query = vsprintf("select %s from %s",[$field,$table_name_with_escape]);

            $query_value_list = [];

            if (empty($where)) {
                $query = vsprintf("%s",[$query,]);

            } else {
                $where_escape = [];

                foreach ($where as $i => $value) {
                    if (!array_key_exists($i,$table_column)) {
                        throw new Exception("field missing, check our model");
                    }

                    if (!array_key_exists($i,$table_schema)) {
                        throw new Exception("field missing, check our schema");
                    }

                    $where_value = null;

                    if (empty($value)) {
                        $where_value = vsprintf("%s is null",[$i,]);

                    } else if (!is_array($value)) {
                        $query_value_list[] = $value;

                        $where_value = vsprintf("%s=?",[$i,]);

                    } else if (is_array($value)) {
                        $query_value_list = array_merge($query_value_list,$value);
                        $value = implode(",",array_map(function ($value) {
                            return "?";
                        },$value));

                        $where_value = vsprintf("%s in(%s)",[$i,$value]);

                    } else {
                        throw new Exception("filter query error");
                    }

                    $where_escape[] = $where_value;
                }

                $where_escape = implode(" and ",$where_escape);
                $query = vsprintf("%s where %s",[$query,$where_escape]);
            }

            $this->setQuery($query);
            $this->setQueryValue($query_value_list);

            return $this;
        }

        public function get($where = []) {
            if (empty($where)) {
                throw new Exception("error in get, where don't set");
            }

            $table_column = $this->getTableColumn();
            $table_name = $this->getTableName();
            $table_schema = $this->getTableSchema();

            $table_name_with_escape = vsprintf("%s%s%s",[$this->db_escape,$table_name,$this->db_escape]);

            $where_escape_list = [];
            $query_value_list = [];

            foreach ($where as $i => $value) {
                if (!array_key_exists($i,$table_column)) {
                    throw new Exception("field missing, check our model");
                }

                if (!array_key_exists($i,$table_schema)) {
                    throw new Exception("field missing, check our schema");
                }

                $where_escape_list[] = vsprintf("%s=?",[$i,]);
                $query_value_list[] = $value;
            }

            $where = vsprintf("where %s",[implode(" and ",$where_escape_list),]);

            $field = implode(",",array_keys($table_column));

            $query_total = vsprintf("select count(1) total from %s %s",[$table_name_with_escape,$where]);
            $query = vsprintf("select %s from %s %s",[$field,$table_name_with_escape,$where]);

            try {
                $query_total = $this->transaction->getResource()->prepare($query_total);
                $query_total->execute($query_value_list);
                $query_total = $query_total->fetch(PDO::FETCH_OBJ);

                if ($query_total->total != 1) {
                    throw new Exception("error in get, don't unique register");
                }

                $query = $this->transaction->getResource()->prepare($query);
                $query->execute($query_value_list);
                $query_fetch = $query->fetch(PDO::FETCH_OBJ);

            } catch (Exception $error) {
                throw new Exception($error);
            }

            foreach ($query_fetch as $i => $value) {
                $this->$i = $value;
            }

            $this->setQuery($query);
            $this->setQueryValue($query_value_list);

            return $this;
        }

        public function save($field = null) {
            $table_name = $this->getTableName();
            $table_column = $this->getTableColumn();
            $table_schema = $this->getTableSchema();

            $table_name_with_escape = vsprintf("%s%s%s",[$this->db_escape,$table_name,$this->db_escape]);

            if (!empty($field)) {
                $column_list = [];
                $query_value_list = [];
                $query_escape_list = [];

                foreach ($field as $i => $value) {
                    if (!array_key_exists($i,$table_column)) {
                        throw new Exception("field missing, check our model");
                    }

                    if (!array_key_exists($i,$table_schema)) {
                        throw new Exception("field missing, check our schema");
                    }

                    $method = $table_schema[$i]->method;
                    $rule = $table_schema[$i]->rule;

                    try {
                        $value = $this->$method($rule,$value,true);

                    } catch (Exception $error) {
                        throw new Exception($error);
                    }

                    $column_list[] = $i;
                    $query_value_list[] = $value;
                    $query_escape_list[] = "?";
                }

                $column_list = implode(",",$column_list);
                $query_escape_list = implode(",",$query_escape_list);

                $query = vsprintf("insert into %s (%s) values(%s)",[$table_name_with_escape,$column_list,$query_escape_list]);

            } else {
                $query_value_list = [];
                $set_escape = [];

                foreach ($table_column as $i => $value) {
                    $method = $table_schema[$i]->method;
                    $rule = $table_schema[$i]->rule;

                    try {
                        $value = $this->$method($rule,$value,true);

                    } catch (Exception $error) {
                        throw new Exception($error);
                    }

                    $set_escape[] = vsprintf("%s=?",[$i,]);
                    $query_value_list[] = $value;
                }

                $set_escape = implode(",",$set_escape);

                $primary_key = $this->getPrimaryKey();
                $last_insert_id = $this->getLastInsertId();

                $where = vsprintf("%s=%s",[$primary_key,$last_insert_id]);

                $query = vsprintf("update %s set %s where %s",[$table_name_with_escape,$set_escape,$where]);
            }

            try {
                $query = $this->transaction->getResource()->prepare($query);
                $query->execute($query_value_list);

            } catch (Exception $error) {
                throw new Exception($error);
            }

            $query_error_info = $query->errorInfo();

            if ($query_error_info[0] != "00000") {
                throw new Exception($query_error_info[2]);
            }

            $get_database_info = $this->transaction->getDatabaseInfo();

            $sequence_name = null;

            if ($get_database_info["DB_DRIVER"] == "pgsql") {
                $sequence_name = vsprintf("%s_id_seq",[$table_name,]);
            }

            $last_insert_id = $this->transaction->lastInsertId($sequence_name);

            $this->setLastInsertId($last_insert_id);
            $this->get(["id" => $last_insert_id]);

            $this->setQuery($query);
            $this->setQueryValue($query_value_list);

            return $this;
        }

        public function delete($field = []) {
            $table_name = $this->getTableName();
            $table_column = $this->getTableColumn();
            $table_schema = $this->getTableSchema();

            $table_name_with_escape = vsprintf("%s%s%s",[$this->db_escape,$table_name,$this->db_escape]);

            if (!empty($field)) {
                $column = [];
                $query_value_list = [];

                foreach ($field as $i => $value) {
                    if (!array_key_exists($i,$table_column)) {
                        throw new Exception("field missing, check our model");
                    }

                    if (!array_key_exists($i,$table_schema)) {
                        throw new Exception("field missing, check our schema");
                    }

                    $method = $table_schema[$i]->method;
                    $rule = $table_schema[$i]->rule;

                    try {
                        $this->$method(
                            $rule = $rule,
                            $value = $value,
                            $flag = true);

                    } catch (Exception $error) {
                        throw new Exception($error);
                    }

                    $where[] = vsprintf("%s=?",[$i,]);
                    $query_value_list[] = $value;
                }

                $where = implode(",",$where);

                $query = vsprintf("delete from %s where %s",[$table_name_with_escape,$where]);

            } else {
                $query_value_list = [];

                foreach ($table_column as $i => $value) {
                    $this->$i = null;
                }

                $primary_key = $this->getPrimaryKey();
                $last_insert_id = $this->getLastInsertId();

                $where = vsprintf("%s=%s",[$primary_key,$last_insert_id]);

                $query = vsprintf("delete from %s where %s",[$table_name_with_escape,$where]);
            }

            try {
                $query = $this->transaction->getResource()->prepare($query);
                $query->execute($query_value_list);

            } catch (Exception $error) {
                throw new Exception($error);
            }

            $query_error_info = $query->errorInfo();

            if ($query_error_info[0] != "00000") {
                throw new Exception($query_error_info[2]);
            }

            $this->setQuery($query);
            $this->setQueryValue($query_value_list);

            return $this;
        }

        public function executeRowsTotal($where = []) {
            $table_column = $this->getTableColumn();
            $table_name = $this->getTableName();
            $table_schema = $this->getTableSchema();

            $table_name_with_escape = vsprintf("%s%s%s",[$this->db_escape,$table_name,$this->db_escape]);

            $query = vsprintf("select count(1) total from %s",[$table_name_with_escape,]);
            $query_value_list = [];

            if (!empty($where)) {
                $where_escape = [];

                foreach ($where as $i => $value) {
                    if (!array_key_exists($i,$table_column)) {
                        throw new Exception("field missing, check our model");
                    }

                    if (!array_key_exists($i,$table_schema)) {
                        throw new Exception("field missing, check our schema");
                    }

                    $where_value = null;

                    if (empty($value)) {
                        $where_value = vsprintf("%s is null",[$i,]);

                    } else if (!is_array($value)) {
                        $query_value_list[] = $value;

                        $where_value = vsprintf("%s=?",[$i,]);

                    } else if (is_array($value)) {
                        $query_value_list = array_merge($query_value_list,$value);

                        $value = implode(",",array_map(function ($value) {
                            return "?";
                        },$value));

                        $where_value = vsprintf("%s in(%s)",[$i,$value]);

                    } else {
                        throw new Exception("filter query error");
                    }

                    $where_escape[] = $where_value;
                }

                $where_escape = implode(" and ",$where_escape);
                $query = vsprintf("%s where %s",[$query,$where_escape]);
            }

            $this->setQuery($query);
            $this->setQueryValue($query_value_list);

            try {
                $query = $this->transaction->getResource()->prepare($query);
                $query->execute($query_value_list);
                $query_fetch = $query->fetch(PDO::FETCH_OBJ);

            } catch (Exception $error) {
                throw new Exception($error);
            }

            $this->setRegister($query_fetch);

            return $this;
        }

        public function lastQuery() {
            $query = $this->getQuery();
            $query_value = $this->getQueryValue();

            $order_by = $this->getOrderBy();
            $limit = $this->getLimit();

            $query = vsprintf("%s %s %s",[$query,$order_by,$limit]);

            return [
                "query" => $query,
                "query_value" => $query_value,
            ];
        }

        public function execute() {
            $order_by = $this->getOrderBy();
            $limit = $this->getLimit();
            $query = $this->getQuery();
            $query_value = $this->getQueryValue();

            $query = vsprintf("%s %s %s",[$query,$order_by,$limit]);

            try {
                $query = $this->transaction->getResource()->prepare($query);
                $query->execute($query_value);
                $query_fetch_all = $query->fetchAll(PDO::FETCH_OBJ);

            } catch (Exception $error) {
                throw new Exception($error);
            }

            $this->setRegister($query_fetch_all);

            return $this;
        }

        public function dump() {
            return $this->getRegister();
        }

        public function __destruct() {
            unset($this);
        }
    }
}