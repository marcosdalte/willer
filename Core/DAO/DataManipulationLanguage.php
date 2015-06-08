<?php

namespace Core\DAO {
    use \PDO as PDO;
    use Exception as Exception;

    abstract class DataManipulationLanguage {
        private $transaction;
        private $db_escape;
        private $related;
        private $limit;
        private $order_by;
        private $primary_key;
        private $last_insert_id;
        private $where_unique;
        private $where_unique_value;
        private $where;
        private $where_value;
        private $query;
        private $query_value;

        public function __construct(Transaction $transaction = null) {
            if (empty($transaction)) {
                throw new Exception("transaction object doesn't loaded");
            }

            $this->setTransaction($transaction);

            $get_database_info = $this->transaction->getDatabaseInfo();
            $db_driver = $get_database_info["DB_DRIVER"];

            if ($db_driver == "mysql") {
                $this->db_escape = "";

            } else if ($db_driver == "sqlite") {
                $this->db_escape = "\"";

            } else if ($db_driver == "pgsql") {
                $this->db_escape = "\"";
            }
        }

        private function getClassName() {
            return $this->className();
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

        private function getRelated() {
            return $this->related;
        }

        private function setRelated($related) {
            $this->related = $related;
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

        private function setOrderBy($order_by) {
            $this->order_by = $order_by;
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

        private function getWhereUnique() {
            return $this->where_unique;
        }

        private function setWhereUnique($where_unique) {
            $this->where_unique = $where_unique;
        }

        private function getWhereUniqueValue() {
            return $this->where_unique_value;
        }

        private function setWhereUniqueValue($where_unique_value) {
            $this->where_unique_value = $where_unique_value;
        }

        private function getWhere() {
            return $this->where;
        }

        private function setWhere($where) {
            $this->where = $where;
        }

        private function getWhereValue() {
            return $this->where_value;
        }

        private function setWhereValue($where_value) {
            $this->where_value = $where_value;
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

        protected function definePrimaryKey($column = null) {
            $table_schema = $this->schema();
            $primarykey_flag = false;

            foreach ($table_schema as $i => $value) {
                if ($value->method == "primaryKey") {
                    if (!empty($primarykey_flag)) {
                        throw new Exception("primary key need be unique");
                    }

                    $column = $i;

                    $primarykey_flag = true;
                }
            }

            if (empty($column)) {
                throw new Exception("primary key missing in schema");
            }

            $this->setPrimaryKey($column);
        }

        public function orderBy($order_by = []) {
            if (!empty($order_by)) {
                $table_name = $this->getTableName();

                $order_by_list = [];

                foreach ($order_by as $i => $value) {
                    $order_by_list[] = vsprintf("%s %s",[$i,$value]);
                }

                $get_order_by = $this->getOrderBy();

                if (empty($get_order_by)) {
                    $get_order_by = [];
                }

                $this->setOrderBy(array_merge($get_order_by,$order_by_list));
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

        private function related($table_related,$query_list = []) {
            $table_name = $table_related->getTableName();
            $table_schema = $table_related->getTableSchema();

            $table_name_with_escape = vsprintf("%s%s%s",[$this->db_escape,$table_name,$this->db_escape]);

            if (empty($query_list)) {
                $query_list = [
                    "column" => [],
                    "join" => [],
                ];
            }

            foreach ($table_schema as $i => $table) {
                if ($table->method == "foreignKey") {
                    $table_foreign_key = $i;
                    $table_related = $table->rule["table"];
                    $table_related_table_name = $table_related->getTableName();
                    $table_related_table_column = $table_related->getTableColumn();
                    $table_related_primary_key = $table_related->getPrimaryKey();

                    $table_related_table_name_with_escape = vsprintf("%s%s%s",[$this->db_escape,$table_related_table_name,$this->db_escape]);

                    $column_list = [];

                    foreach ($table_related_table_column as $ii => $column) {
                        $column_list[] = vsprintf("%s.%s %s__%s",[$table_related_table_name_with_escape,$ii,$table_related_table_name,$ii]);
                    }

                    $query_list["column"][] = $column_list;
                    $query_list["join"][] = vsprintf("join %s on %s.%s = %s.%s",[$table_related_table_name_with_escape,$table_related_table_name_with_escape,$table_related_primary_key,$table_name_with_escape,$table_foreign_key]);

                    $query_list = $this->related($table_related,$query_list);
                }
            }



            return $query_list;
        }

        public function where($where = []) {
            $where_value_list = [];

            if (empty($where)) {
                $where_query = null;

            } else {
                $where_query = [];

                foreach ($where as $i => $value) {
                    $where_value = null;

                    if (empty($value)) {
                        $where_value = vsprintf("%s is null",[$i,]);

                    } else if (!is_array($value)) {
                        $where_value_list[] = $value;

                        $where_value = vsprintf("%s=?",[$i,]);

                    } else if (is_array($value)) {
                        $where_value_list = array_merge($where_value_list,$value);
                        $value = implode(",",array_map(function ($value) {
                            return "?";
                        },$value));

                        $where_value = vsprintf("%s in(%s)",[$i,$value]);

                    } else {
                        throw new Exception("filter query error");
                    }

                    $where_query[] = $where_value;
                }

                $get_where = $this->getWhere();
                $get_where_value = $this->getWhereValue();

                if (empty($get_where)) {
                    $get_where = [];
                }

                if (empty($get_where_value)) {
                    $get_where_value = [];
                }

                $this->setWhereUnique(null);
                $this->setWhereUniqueValue(null);

                $this->setWhere(array_merge($get_where,$where_query));
                $this->setWhereValue(array_merge($get_where_value,$where_value_list));
            }

            return $this;
        }

        public function get($where = [],$setting = []) {
            $transaction_resource = $this->transaction->getResource();

            if (empty($transaction_resource)) {
                throw new Exception("conection resource dont initiated");
            }

            if (empty($where)) {
                throw new Exception("error in get, where don't set");
            }

            $join = "inner";

            if (!empty($setting)) {
                if (array_key_exists("join",$setting)) {
                    $join = $setting["join"];
                }
            }

            $table_column = $this->getTableColumn();
            $table_name = $this->getTableName();
            $table_schema = $this->getTableSchema();
            $related = $this->related($this);

            $table_name_with_escape = vsprintf("%s%s%s",[$this->db_escape,$table_name,$this->db_escape]);

            $related_join = null;
            $related_column = [];

            if (!empty($related) && !empty($related["join"])) {
                $related_join = vsprintf("%s %s",[$join,implode(vsprintf(" %s ",[$join,]),$related["join"])]);

                foreach ($related["column"] as $i => $column) {
                    $related_column[] = implode(",",$column);
                }

            }

            $where_escape_list = [];
            $query_value_list = [];

            foreach ($where as $i => $value) {
                $where_escape_list[] = vsprintf("%s=?",[$i,]);
                $query_value_list[] = $value;
            }

            $column_list = [];

            foreach ($table_column as $i => $column) {
                $column_list[] = vsprintf("%s.%s %s__%s",[$table_name_with_escape,$i,$table_name,$i]);
            }

            $column_list = array_merge($related_column,$column_list);
            $column_list = implode(",",$column_list);

            $where = vsprintf("where %s",[implode(" and ",$where_escape_list),]);

            $query_total = vsprintf("select count(1) total from %s %s %s",[$table_name_with_escape,$related_join,$where]);
            $query = vsprintf("select %s from %s %s %s",[$column_list,$table_name_with_escape,$related_join,$where]);

            try {
                $pdo_query_total = $transaction_resource->prepare($query_total);

                $transaction_resource_error_info = $transaction_resource->errorInfo();

                if ($transaction_resource_error_info[0] != "00000") {
                    throw new Exception($transaction_resource_error_info[2]);
                }

                $pdo_query_total->execute($query_value_list);
                $pdo_query_total = $pdo_query_total->fetch(PDO::FETCH_OBJ);

                if (empty($pdo_query_total)) {
                    throw new Exception("Trying to get property of non-object");
                }

                if ($pdo_query_total->total <= 0) {
                    throw new Exception("error in get, don't register");
                }

                if ($pdo_query_total->total > 1) {
                    throw new Exception("error in get, don't unique register");
                }

                $pdo_query = $transaction_resource->prepare($query);

                $transaction_resource_error_info = $transaction_resource->errorInfo();

                if ($transaction_resource_error_info[0] != "00000") {
                    throw new Exception($transaction_resource_error_info[2]);
                }

                $pdo_query->execute($query_value_list);
                $pdo_query_fetch = $pdo_query->fetch(PDO::FETCH_OBJ);

            } catch (Exception $error) {
                throw new Exception($error);
            }

            foreach ($table_column as $column => $value) {
                $table_column_str = vsprintf("%s__%s",[$table_name,$column]);

                $this->$column = $pdo_query_fetch->$table_column_str;
            }

            $class_name = $this->getClassName();
            $transaction = $this->getTransaction();
            $obj_column_list = $this->getTableColumn();
            $obj_schema_dict = $table_schema;

            $query_fetch = $pdo_query_fetch;
            $obj = $this;

            $related_fetch = $this->relatedFetch($obj_column_list,$obj_schema_dict,$query_fetch,$transaction,$obj);

            $this->setWhere(null);
            $this->setWhereValue(null);

            $this->setWhereUnique($where_escape_list);
            $this->setWhereUniqueValue($query_value_list);

            $this->setQuery($query);
            $this->setQueryValue($query_value_list);

            return $related_fetch;
        }

        public function save($field = null) {
            $transaction_resource = $this->transaction->getResource();

            if (empty($transaction_resource)) {
                throw new Exception("conection resource dont initiated");
            }

            $table_name = $this->getTableName();
            $table_column = $this->getTableColumn();
            $table_schema = $this->getTableSchema();
            $primary_key = $this->getPrimaryKey();
            $last_insert_id = $this->getLastInsertId();

            $table_name_with_escape = vsprintf("%s%s%s",[$this->db_escape,$table_name,$this->db_escape]);

            $column_list = [];
            $query_value_list = [];
            $query_escape_list = [];
            $set_escape = [];
            $add_flag = false;

            if (!empty($field)) {
                if (!is_array($field)) {
                    throw new Exception("wrong type of filter");
                }

                $last_insert_id = $this->setLastInsertId(null);

                $table_column = $field;
            }

            foreach ($table_column as $i => $value) {
                if ($primary_key != $i) {
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

                    $set_escape[] = vsprintf("%s=?",[$i,]);
                    $query_value_update_list[] = $value;

                    $column_list[] = $i;
                    $query_value_add_list[] = $value;
                    $query_escape_list[] = "?";
                }
            }

            $set_escape = implode(",",$set_escape);

            if (!empty($table_column[$primary_key])) {
                $where = vsprintf("%s=%s",[$primary_key,$table_column[$primary_key]]);

            } else {
                $where = vsprintf("%s=%s",[$primary_key,$last_insert_id]);
            }

            $column_list = implode(",",$column_list);
            $query_escape_list = implode(",",$query_escape_list);

            if (!empty($last_insert_id) || !empty($table_column[$primary_key])) {
                $query = vsprintf("update %s set %s where %s",[$table_name_with_escape,$set_escape,$where]);
                $query_value_list = $query_value_update_list;

            } else {
                $query = vsprintf("insert into %s (%s) values(%s)",[$table_name_with_escape,$column_list,$query_escape_list]);
                $query_value_list = $query_value_add_list;

                $add_flag = true;
            }

            try {
                $pdo_query = $transaction_resource->prepare($query);

                $transaction_resource_error_info = $transaction_resource->errorInfo();

                if ($transaction_resource_error_info[0] != "00000") {
                    throw new Exception($transaction_resource_error_info[2]);
                }

                $pdo_query->execute($query_value_list);

            } catch (Exception $error) {
                throw new Exception($error);
            }

            $pdo_query_error_info = $pdo_query->errorInfo();

            if ($pdo_query_error_info[0] != "00000") {
                throw new Exception($pdo_query_error_info[2]);
            }

            if (empty($add_flag)) {
                $this->get([
                    vsprintf("%s.id",[$table_name_with_escape,]) => $table_column[$primary_key]]);

            } else {
                $get_database_info = $this->transaction->getDatabaseInfo();

                $sequence_name = null;

                if ($get_database_info["DB_DRIVER"] == "pgsql") {
                    $sequence_name = vsprintf("%s_id_seq",[$table_name,]);
                }

                $last_insert_id = $this->transaction->lastInsertId($sequence_name);

                $this->setLastInsertId($last_insert_id);
                $this->get([
                    vsprintf("%s.id",[$table_name_with_escape,]) => $last_insert_id]);
            }

            $this->setQuery($query);
            $this->setQueryValue($query_value_list);

            return $this;
        }

        public function update($set = null) {
            if (empty($set)) {
                throw new Exception("set update is null");
            }

            $transaction_resource = $this->transaction->getResource();

            if (empty($transaction_resource)) {
                throw new Exception("conection resource dont initiated");
            }

            if (!is_array($set)) {
                throw new Exception("set is not a list");
            }

            $table_name = $this->getTableName();
            $table_column = $this->getTableColumn();
            $table_schema = $this->getTableSchema();

            $set_list = [];

            foreach ($set as $i => $value) {
                if (!array_key_exists($i,$table_column)) {
                    throw new Exception("field missing, check our model");
                }

                if (!array_key_exists($i,$table_schema)) {
                    throw new Exception("field missing, check our schema");
                }

                $set_list[] = vsprintf("%s='%s'",[$i,$value]);
            }

            $set_list = implode(",",$set_list);

            $table_name_with_escape = vsprintf("%s%s%s",[$this->db_escape,$table_name,$this->db_escape]);

            $where = "";
            $query_value = [];

            $get_where_unique = $this->getWhereUnique();

            if (!empty($get_where_unique)) {
                $get_where_unique_value = $this->getWhereUniqueValue();

                $where = vsprintf("where %s",[implode(" and ",$get_where_unique),]);

                $query_value = array_merge($query_value,$get_where_unique_value);

            } else {
                $get_where = $this->getWhere();
                $get_where_value = $this->getWhereValue();

                if (!empty($get_where)) {
                    $where .= implode(" and ",$get_where);

                    $query_value = array_merge($query_value,$get_where_value);
                }

                if (!empty($where)) {
                    $where = vsprintf("where %s",[$where,]);
                }
            }

            $query = vsprintf("update %s set %s %s",[$table_name_with_escape,$set_list,$where]);

            try {
                $query = $transaction_resource->prepare($query);

                $transaction_resource_error_info = $transaction_resource->errorInfo();

                if ($transaction_resource_error_info[0] != "00000") {
                    throw new Exception($transaction_resource_error_info[2]);
                }

                $query->execute($query_value);

            } catch (Exception $error) {
                throw new Exception($error);
            }

            $this->setQuery($query);
            $this->setQueryValue($query_value);

            return $this;
        }

        public function delete($where = null) {
            $transaction_resource = $this->transaction->getResource();

            if (empty($transaction_resource)) {
                throw new Exception("conection resource dont initiated");
            }

            $table_name = $this->getTableName();
            $table_column = $this->getTableColumn();
            $table_schema = $this->getTableSchema();

            $where_str = "";
            $query_value = [];

            if (!empty($where)) {
                $where_list = [];

                if (!is_array($where)) {
                    throw new Exception("where is not a list");
                }

                foreach ($where as $i => $value) {
                    if (!array_key_exists($i,$table_column)) {
                        throw new Exception("field missing, check our model");
                    }

                    if (!array_key_exists($i,$table_schema)) {
                        throw new Exception("field missing, check our schema");
                    }

                    $where_list[] = vsprintf("%s=?",[$i,]);
                    $query_value[] = $value;
                }

                $where_str = vsprintf("where %s",[implode(" and ",$where_list),]);

            } else {
                $get_where_unique = $this->getWhereUnique();

                if (!empty($get_where_unique)) {
                    $get_where_unique_value = $this->getWhereUniqueValue();

                    $where_str = vsprintf("where %s",[implode(" and ",$get_where_unique),]);

                    $query_value = $get_where_unique_value;

                } else {
                    $get_where = $this->getWhere();
                    $get_where_value = $this->getWhereValue();

                    if (!empty($get_where)) {
                        $where_str .= implode(" and ",$get_where);

                        $query_value = array_merge($query_value,$get_where_value);
                    }

                    if (!empty($where_str)) {
                        $where_str = vsprintf("where %s",[$where_str,]);
                    }
                }
            }

            $table_name_with_escape = vsprintf("%s%s%s",[$this->db_escape,$table_name,$this->db_escape]);

            $query = vsprintf("delete from %s %s",[$table_name_with_escape,$where_str]);

            try {
                $query = $transaction_resource->prepare($query);

                $transaction_resource_error_info = $transaction_resource->errorInfo();

                if ($transaction_resource_error_info[0] != "00000") {
                    throw new Exception($transaction_resource_error_info[2]);
                }

                $query->execute($query_value);

            } catch (Exception $error) {
                throw new Exception($error);
            }

            $this->setQuery($query);
            $this->setQueryValue($query_value);

            return $this;
        }

        public function execute($setting = []) {
            $transaction_resource = $this->transaction->getResource();

            if (empty($transaction_resource)) {
                throw new Exception("conection resource dont initiated");
            }

            $join = "inner";

            if (!empty($setting)) {
                if (array_key_exists("join",$setting)) {
                    $join = $setting["join"];
                }
            }

            $table_name = $this->getTableName();
            $table_column = $this->getTableColumn();
            $get_where = $this->getWhere();
            $get_where_value = $this->getWhereValue();
            $order_by = $this->getOrderBy();
            $limit = $this->getLimit();
            $related = $this->related($this);

            $table_name_with_escape = vsprintf("%s%s%s",[$this->db_escape,$table_name,$this->db_escape]);

            $related_join = null;
            $related_column = [];

            if (!empty($related) && !empty($related["join"])) {
                $related_join = vsprintf("%s %s",[$join,implode(vsprintf(" %s ",[$join,]),$related["join"])]);

                foreach ($related["column"] as $i => $column) {
                    $related_column[] = implode(",",$column);
                }

            }

            $column_list = [];

            foreach ($table_column as $i => $column) {
                $column_list[] = vsprintf("%s.%s %s__%s",[$table_name_with_escape,$i,$table_name,$i]);
            }

            $column_list = array_merge($related_column,$column_list);
            $column_list = implode(",",$column_list);

            $query_value = [];

            if (empty($get_where)) {
                $where = "";

            } else {
                $where = vsprintf("where %s",[implode(" and ",$get_where),]);

                $query_value = array_merge($query_value,$get_where_value);
            }

            if (empty($order_by)) {
                $order_by = "";

            } else {
                $order_by = vsprintf("order by %s",[implode(",",$order_by),]);
            }

            $query = vsprintf("select %s from %s %s %s %s %s",[$column_list,$table_name_with_escape,$related_join,$where,$order_by,$limit]);

            $this->setQuery($query);
            $this->setQueryValue($query_value);

            try {
                $pdo_query = $transaction_resource->prepare($query);

                $transaction_resource_error_info = $transaction_resource->errorInfo();

                if ($transaction_resource_error_info[0] != "00000") {
                    throw new Exception($transaction_resource_error_info[2]);
                }

                $pdo_query->execute($query_value);
                $query_fetch_all = $pdo_query->fetchAll(PDO::FETCH_OBJ);

            } catch (Exception $error) {
                throw new Exception($error);
            }

            $query_fetch_all_list = [];

            if (!empty($query_fetch_all)) {
                $class_name = $this->getClassName();
                $table_name = $this->getTableName();
                $column_list = $this->getTableColumn();
                $transaction = $this->getTransaction();

                foreach ($query_fetch_all as $i => $query_fetch) {
                    $obj = new $class_name($transaction);

                    foreach ($column_list as $column => $value) {
                        $table_column = vsprintf("%s__%s",[$table_name,$column]);

                        $obj->$column = $query_fetch->$table_column;
                    }

                    $obj_column_list = $obj->getTableColumn();
                    $obj_schema_dict = $obj->schema();

                    $related_fetch = $this->relatedFetch($obj_column_list,$obj_schema_dict,$query_fetch,$transaction,$obj);

                    $query_fetch_all_list[] = $related_fetch;
                }
            }

            return $query_fetch_all_list;
        }

        private function relatedFetch($obj_column_list,$obj_schema_dict,$fetch,$transaction,$obj) {
            foreach ($obj_column_list as $column => $value) {
                if ($obj_schema_dict[$column]->method == "foreignKey") {
                    $obj_foreignkey = $obj_schema_dict[$column]->rule["table"];

                    $obj_foreignkey_class_name = $obj_foreignkey->getClassName();
                    $obj_foreignkey_table_name = $obj_foreignkey->getTableName();
                    $obj_foreignkey_column_list = $obj_foreignkey->getTableColumn();
                    $obj_foreignkey_schema_dict = $obj_foreignkey->schema();

                    $obj_foreignkey = new $obj_foreignkey_class_name($transaction);

                    foreach ($obj_foreignkey_column_list as $column_ => $value_) {
                        $table_column = vsprintf("%s__%s",[$obj_foreignkey_table_name,$column_]);

                        $obj_foreignkey->$column_ = $fetch->$table_column;
                    }

                    $obj->$column = $obj_foreignkey;

                    $this->relatedFetch($obj_foreignkey_column_list,$obj_foreignkey_schema_dict,$fetch,$transaction,$obj_foreignkey);
                }
            }

            return $obj;
        }

        public function lastQuery() {
            $query = $this->getQuery();
            $query_value = $this->getQueryValue();

            return (object) [
                "query" => $query,
                "query_value" => $query_value,
            ];
        }

        public function __destruct() {
            unset($this);
        }
    }
}
