<?php

namespace Core\DAO {
    use \PDO as PDO;
    use \Exception as Exception;
    use \PDOException as PDOException;
    use \Core\Exception\WF_Exception;

    abstract class DataManipulationLanguage {
        private $transaction;
        private $db_escape;
        private $related;
        private $limit;
        private $limit_value;
        private $order_by;
        private $primary_key;
        private $last_insert_id;
        private $where_unique;
        private $where_unique_value;
        private $where;
        private $where_value;
        private $query;

        private $flag_getnotest;

        public function __construct($transaction = null) {
            if (empty($transaction)) {
                throw new WF_Exception(vsprintf('Transaction object not loaded, in model instance "%s"',[$this->name(),]));
            }

            if (!$transaction instanceof Transaction) {
                throw new WF_Exception(vsprintf('incorrect loaded instance of Transaction, in model instance "%s"',[$this->name(),]));
            }

            $this->setTransaction($transaction);

            $get_database_info = $this->transaction->getDatabaseInfo();
            $db_driver = $get_database_info['driver'];

            if ($db_driver == 'mysql') {
                $this->db_escape = '';

            } else if ($db_driver == 'sqlite') {
                $this->db_escape = '\'';

            } else if ($db_driver == 'pgsql') {
                $this->db_escape = '"';
            }

            $this->query = [];
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

        private function getLimitValue() {
            return $this->limit_value;
        }

        private function setLimitValue($page,$limit) {
            $this->limit_value = [
                'page' => $page,
                'limit' => $limit,
            ];
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

        private function setQuery($sql,$value) {
            $this->query[] = [
                'sql' => $sql,
                'value' => $value
            ];
        }

        protected function definePrimaryKey($column = null) {
            $table_schema = $this->schema();
            $primarykey_flag = false;

            foreach ($table_schema as $i => $value) {
                if ($value->method == 'primaryKey') {
                    if (!empty($primarykey_flag)) {
                        throw new WF_Exception(vsprintf('"%s" field error, primary key need be unique',[$i,]));
                    }

                    $column = $i;

                    $primarykey_flag = true;
                }
            }

            if (empty($column)) {
                throw new WF_Exception(vsprintf('primary key missing in schema of model "%s"',[$this->name(),]));
            }

            $this->setPrimaryKey($column);
        }

        public function orderBy($order_by = []) {
            if (!empty($order_by)) {
                $table_name = $this->getTableName();

                $order_by_list = [];

                foreach ($order_by as $i => $value) {
                    $order_by_list[] = vsprintf('%s %s',[$i,$value]);
                }

                $get_order_by = $this->getOrderBy();

                if (empty($get_order_by)) {
                    $get_order_by = [];
                }

                $this->setOrderBy(array_merge($get_order_by,$order_by_list));
            }

            return $this;
        }

        public function limit($page = 1,$limit = 1000) {
            $limit_value = null;

            $page = intval($page);
            $limit = intval($limit);

            if ($page <= 1) {
                $page = 1;

                $limit_value = vsprintf('limit %s offset 0',[$limit,]);

            } else {
                $page_ = $page - 1;
                $page_x_limit = $page_ * $limit;
                $limit_value = vsprintf('limit %s offset %s',[$limit,$page_x_limit]);
            }

            $this->setLimitValue($page,$limit);
            $this->setLimit($limit_value);

            return $this;
        }

        private function related($table_related,$query_list = []) {
            $table_name = $table_related->getTableName();
            $table_schema = $table_related->getTableSchema();

            $table_name_with_escape = vsprintf('%s%s%s',[$this->db_escape,$table_name,$this->db_escape]);

            if (empty($query_list)) {
                $query_list = [
                    'column' => [],
                    'join' => [],
                ];
            }

            foreach ($table_schema as $i => $table) {
                if ($table->method == 'foreignKey') {
                    $table_foreign_key = $i;
                    $table_related = $table->rule['table'];
                    $table_related_table_name = $table_related->getTableName();
                    $table_related_table_column = $table_related->getTableColumn();
                    $table_related_primary_key = $table_related->getPrimaryKey();

                    $table_related_table_name_with_escape = vsprintf('%s%s%s',[$this->db_escape,$table_related_table_name,$this->db_escape]);

                    $column_list = [];

                    foreach ($table_related_table_column as $ii => $column) {
                        $column_list[] = vsprintf('%s.%s %s__%s',[$table_related_table_name_with_escape,$ii,$table_related_table_name,$ii]);
                    }

                    $query_list['column'][] = $column_list;
                    $query_list['join'][] = vsprintf('join %s on %s.%s = %s.%s',[$table_related_table_name_with_escape,$table_related_table_name_with_escape,$table_related_primary_key,$table_name_with_escape,$table_foreign_key]);

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

                foreach ($where as $key => $value) {
                    $where_value = null;

                    if (empty($value)) {
                        $where_value = vsprintf('%s is null',[$key,]);

                    } else if (!is_array($value) && (is_string($value) || is_numeric($value) || is_bool($value))) {
                        $where_value_list[] = $value;

                        $where_value = vsprintf('%s=?',[$key,]);

                    } else if (is_array($value)) {
                        $where_value_list = array_merge($where_value_list,$value);
                        $value = implode(',',array_map(function ($value) {
                            return '?';
                        },$value));

                        $where_value = vsprintf('%s in(%s)',[$key,$value]);

                    } else {
                        throw new WF_Exception(vsprintf('value is incorrect with type "%s", in instance of model "%s"',[gettype($value),$this->name()]));
                    }

                    $where_query[] = $where_value;
                }

                $this->setWhereUnique(null);
                $this->setWhereUniqueValue(null);

                $this->setWhere($where_query);
                $this->setWhereValue($where_value_list);
            }

            return $this;
        }

        public function get($where = [],$setting = []) {
            $transaction = $this->getTransaction();

            if (empty($transaction)) {
                throw new WF_Exception(vsprintf('transaction object not loaded in model instance "%s"',[$this->name(),]));
            }

            if (!$transaction instanceof Transaction) {
                throw new WF_Exception(vsprintf('incorrect loaded instance of Transaction, in model instance "%s"',[$this->name(),]));
            }

            $transaction_resource = $this->transaction->getResource();

            if (empty($transaction_resource)) {
                throw new WF_Exception(vsprintf('transaction instance not loaded, in model instance "%s"',[$this->name(),]));
            }

            if (empty($where)) {
                throw new WF_Exception(vsprintf('where condition not defined, in model instance "%s"',[$this->name(),]));
            }

            $join = 'inner';

            if (!empty($setting)) {
                if (array_key_exists('join',$setting)) {
                    $join = $setting['join'];
                }
            }

            $table_column = $this->getTableColumn();
            $table_name = $this->getTableName();
            $table_schema = $this->getTableSchema();
            $related = $this->related($this);

            $table_name_with_escape = vsprintf('%s%s%s',[$this->db_escape,$table_name,$this->db_escape]);

            $related_join = null;
            $related_column = [];

            if (!empty($related) && !empty($related['join'])) {
                $related_join = vsprintf('%s %s',[$join,implode(vsprintf(' %s ',[$join,]),$related['join'])]);

                foreach ($related['column'] as $i => $column) {
                    $related_column[] = implode(',',$column);
                }
            }

            $where_escape_list = [];
            $query_value_list = [];

            foreach ($where as $i => $value) {
                $where_escape_list[] = vsprintf('%s=?',[$i,]);
                $query_value_list[] = $value;
            }

            $column_list = [];

            foreach ($table_column as $i => $column) {
                $column_list[] = vsprintf('%s.%s %s__%s',[$table_name_with_escape,$i,$table_name,$i]);
            }

            $column_list = array_merge($related_column,$column_list);
            $column_list = implode(',',$column_list);

            $where = vsprintf('where %s',[implode(' and ',$where_escape_list),]);

            $query_total = vsprintf('select count(1) total from %s %s %s',[$table_name_with_escape,$related_join,$where]);
            $query = vsprintf('select %s from %s %s %s',[$column_list,$table_name_with_escape,$related_join,$where]);

            try {
                if (empty($this->flag_getnotest)) {
                    $pdo_query_total = $transaction_resource->prepare($query_total);

                    $transaction_resource_error_info = $transaction_resource->errorInfo();

                    if ($transaction_resource_error_info[0] != '00000') {
                        throw new WF_Exception(vsprintf('PDO error message "%s", in model instance "%s"',[$transaction_resource_error_info[2],$this->name(),]));
                    }

                    $pdo_query_total->execute($query_value_list);
                    $pdo_query_total = $pdo_query_total->fetch(PDO::FETCH_OBJ);

                    $this->setQuery($query_total,$query_value_list);

                    if (empty($pdo_query_total)) {
                        throw new WF_Exception(vsprintf('query error, in model instance "%s"',[$this->name(),]));
                    }

                    if ($pdo_query_total->total <= 0) {
                        throw new WF_Exception(vsprintf('query result is empty, in model instance "%s"',[$this->name(),]));
                    }

                    if ($pdo_query_total->total > 1) {
                        throw new WF_Exception(vsprintf('query result not unique, in model instance "%s"',[$this->name(),]));
                    }
                }

                $this->flag_getnotest = false;

                $pdo_query = $transaction_resource->prepare($query);

                $transaction_resource_error_info = $transaction_resource->errorInfo();

                if ($transaction_resource_error_info[0] != '00000') {
                    throw new WF_Exception(vsprintf('PDO error message "%s", in model instance "%s"',[$transaction_resource_error_info[2],$this->name(),]));
                }

                $pdo_query->execute($query_value_list);
                $pdo_query_fetch = $pdo_query->fetch(PDO::FETCH_OBJ);

            } catch (PDOException $error) {
                throw $error;

            } catch (Exception $error) {
                throw $error;
            }

            foreach ($table_column as $column => $value) {
                $table_column_str = vsprintf('%s__%s',[$table_name,$column]);

                $this->$column = $pdo_query_fetch->$table_column_str;
            }

            $class_name = $this->getClassName();
            $obj_column_list = $this->getTableColumn();
            $obj_schema_dict = $table_schema;

            $query_fetch = $pdo_query_fetch;
            $obj = $this;

            $related_fetch = $this->relatedFetch($obj_column_list,$obj_schema_dict,$query_fetch,$transaction,$obj);

            $this->setWhere(null);
            $this->setWhereValue(null);

            $this->setWhereUnique($where_escape_list);
            $this->setWhereUniqueValue($query_value_list);

            $this->setQuery($query,$query_value_list);

            return $related_fetch;
        }

        public function save($field = null) {
            $transaction = $this->getTransaction();

            if (empty($transaction)) {
                throw new WF_Exception(vsprintf('transaction object do not loaded in model instance "%s"',[$this->name(),]));
            }

            $transaction_resource = $this->transaction->getResource();

            if (empty($transaction_resource)) {
                throw new WF_Exception('WF_conectionResourceDoNotInitiated');
            }

            $flag_getdiscard = false;

            $table_name = $this->getTableName();
            $table_column = $this->getTableColumn();
            $table_schema = $this->getTableSchema();
            $primary_key = $this->getPrimaryKey();
            $last_insert_id = $this->getLastInsertId();

            $table_name_with_escape = vsprintf('%s%s%s',[$this->db_escape,$table_name,$this->db_escape]);

            $column_list = [];
            $query_value_list = [];
            $query_escape_list = [];
            $set_escape = [];
            $add_flag = false;

            if (!empty($field)) {
                if (!is_array($field)) {
                    throw new WF_Exception('WF_wrongTypeOfFilter');
                }

                $last_insert_id = $this->setLastInsertId(null);

                $table_column = $field;
            }

            foreach ($table_column as $i => $value) {
                if ($primary_key != $i) {
                    if (!array_key_exists($i,$table_column)) {
                        throw new WF_Exception('WF_fieldMissingCheckOurModel');
                    }

                    if (!array_key_exists($i,$table_schema)) {
                        throw new WF_Exception('WF_fieldMissingCheckOurSchema');
                    }

                    $method = $table_schema[$i]->method;
                    $rule = $table_schema[$i]->rule;

                    $value = $this->$method($rule,$value,true);

                    $set_escape[] = vsprintf('%s=?',[$i,]);
                    $query_value_update_list[] = $value;

                    $column_list[] = $i;
                    $query_value_add_list[] = $value;
                    $query_escape_list[] = '?';
                }
            }

            $set_escape = implode(',',$set_escape);

            if (!empty($table_column[$primary_key])) {
                $where = vsprintf('%s=%s',[$primary_key,$table_column[$primary_key]]);

            } else {
                $where = vsprintf('%s=%s',[$primary_key,$last_insert_id]);
            }

            $column_list = implode(',',$column_list);
            $query_escape_list = implode(',',$query_escape_list);

            if (!empty($last_insert_id) || !empty($table_column[$primary_key])) {
                $query = vsprintf('update %s set %s where %s',[$table_name_with_escape,$set_escape,$where]);
                $query_value_list = $query_value_update_list;

                $flag_getdiscard = true;

            } else {
                $query = vsprintf('insert into %s (%s) values(%s)',[$table_name_with_escape,$column_list,$query_escape_list]);
                $query_value_list = $query_value_add_list;

                $add_flag = true;
            }

            try {
                $pdo_query = $transaction_resource->prepare($query);

                $transaction_resource_error_info = $transaction_resource->errorInfo();

                if ($transaction_resource_error_info[0] != '00000') {
                    throw new WF_Exception(vsprintf('PDO error info "%s"',[$transaction_resource_error_info[2],]));
                }

                $pdo_query->execute($query_value_list);

            } catch (PDOException $error) {
                throw new WF_Exception(vsprintf('PDOException message "%s"',[$error->getMessage(),]));

            } catch (Exception $error) {
                throw new WF_Exception(vsprintf('PDO transaction error "%s"',[$error->getMessage(),]));
            }


            $pdo_query_error_info = $pdo_query->errorInfo();

            if ($pdo_query_error_info[0] != '00000') {
                throw new WF_Exception($pdo_query_error_info[2]);
            }

            if (empty($flag_getdiscard)) {
                $this->flag_getnotest = true;

                if (empty($add_flag)) {
                    $this->get([
                        vsprintf('%s.id',[$table_name_with_escape,]) => $table_column[$primary_key]]);

                } else {
                    $get_database_info = $this->transaction->getDatabaseInfo();

                    $sequence_name = null;

                    if ($get_database_info['driver'] == 'pgsql') {
                        $sequence_name = vsprintf('%s_id_seq',[$table_name,]);
                    }

                    $last_insert_id = $this->transaction->lastInsertId($sequence_name);

                    $this->setLastInsertId($last_insert_id);
                    $this->get([
                        vsprintf('%s.id',[$table_name_with_escape,]) => $last_insert_id]);
                }
            }

            $this->setQuery($query,$query_value_list);

            return $this;
        }

        public function update($set = null) {
            if (empty($set)) {
                throw new WF_Exception('WF_setUpdateIsNull');
            }

            if (!is_array($set)) {
                throw new WF_Exception('WF_setIsNotList');
            }

            $transaction_resource = $this->transaction->getResource();

            if (empty($transaction_resource)) {
                throw new WF_Exception('WF_conectionResourceDoNotInitiated');
            }

            $table_name = $this->getTableName();
            $table_column = $this->getTableColumn();
            $table_schema = $this->getTableSchema();

            $set_escape = [];
            $query_value_update_list = [];

            foreach ($set as $i => $value) {
                if (!array_key_exists($i,$table_column)) {
                    throw new WF_Exception('WF_fieldMissingCheckOurModel');
                }

                if (!array_key_exists($i,$table_schema)) {
                    throw new WF_Exception('WF_fieldMissingCheckOurSchema');
                }

                $method = $table_schema[$i]->method;
                $rule = $table_schema[$i]->rule;

                try {
                    $value = $this->$method($rule,$value,true);

                } catch (Exception $error) {
                    throw new WF_Exception($error);
                }

                $set_escape[] = vsprintf('%s=?',[$i,]);
                $query_value_update_list[] = $value;
            }

            $set_escape = implode(',',$set_escape);

            $table_name_with_escape = vsprintf('%s%s%s',[$this->db_escape,$table_name,$this->db_escape]);

            $where = '';
            $query_value = array_merge([],$query_value_update_list);

            $get_where_unique = $this->getWhereUnique();

            if (!empty($get_where_unique)) {
                $get_where_unique_value = $this->getWhereUniqueValue();

                $where = vsprintf('where %s',[implode(' and ',$get_where_unique),]);

                $query_value = array_merge($query_value,$get_where_unique_value);

            } else {
                $get_where = $this->getWhere();
                $get_where_value = $this->getWhereValue();

                if (!empty($get_where)) {
                    $where .= implode(' and ',$get_where);

                    $query_value = array_merge($query_value,$get_where_value);
                }

                if (!empty($where)) {
                    $where = vsprintf('where %s',[$where,]);
                }
            }

            $query = vsprintf('update %s set %s %s',[$table_name_with_escape,$set_escape,$where]);

            try {
                $query = $transaction_resource->prepare($query);

                $transaction_resource_error_info = $transaction_resource->errorInfo();

                if ($transaction_resource_error_info[0] != '00000') {
                    throw new WF_Exception($transaction_resource_error_info[2]);
                }

                $query->execute($query_value);

            } catch (Exception $error) {
                throw new WF_Exception($error);
            }

            $this->setQuery($query,$query_value);

            return $this;
        }

        public function delete($where = null) {
            $transaction_resource = $this->transaction->getResource();

            if (empty($transaction_resource)) {
                throw new WF_Exception('WF_conectionResourceDoNotInitiated');
            }

            $table_name = $this->getTableName();
            $table_column = $this->getTableColumn();
            $table_schema = $this->getTableSchema();

            $where_str = '';
            $query_value = [];

            if (!empty($where)) {
                $where_list = [];

                if (!is_array($where)) {
                    throw new WF_Exception('WF_whereIsNotList');
                }

                foreach ($where as $i => $value) {
                    if (!array_key_exists($i,$table_column)) {
                        throw new WF_Exception('WF_fieldMissingCheckOurModel');
                    }

                    if (!array_key_exists($i,$table_schema)) {
                        throw new WF_Exception('WF_fieldMissingCheckOurSchema');
                    }

                    $where_list[] = vsprintf('%s=?',[$i,]);
                    $query_value[] = $value;
                }

                $where_str = vsprintf('where %s',[implode(' and ',$where_list),]);

            } else {
                $get_where_unique = $this->getWhereUnique();

                if (!empty($get_where_unique)) {
                    $get_where_unique_value = $this->getWhereUniqueValue();

                    $where_str = vsprintf('where %s',[implode(' and ',$get_where_unique),]);

                    $query_value = $get_where_unique_value;

                } else {
                    $get_where = $this->getWhere();
                    $get_where_value = $this->getWhereValue();

                    if (!empty($get_where)) {
                        $where_str .= implode(' and ',$get_where);

                        $query_value = array_merge($query_value,$get_where_value);
                    }

                    if (!empty($where_str)) {
                        $where_str = vsprintf('where %s',[$where_str,]);
                    }
                }
            }

            $table_name_with_escape = vsprintf('%s%s%s',[$this->db_escape,$table_name,$this->db_escape]);

            $query = vsprintf('delete from %s %s',[$table_name_with_escape,$where_str]);

            try {
                $query = $transaction_resource->prepare($query);

                $transaction_resource_error_info = $transaction_resource->errorInfo();

                if ($transaction_resource_error_info[0] != '00000') {
                    throw new WF_Exception($transaction_resource_error_info[2]);
                }

                $query->execute($query_value);

            } catch (Exception $error) {
                throw new WF_Exception($error);
            }

            $this->setQuery($query,$query_value);

            return $this;
        }

        public function execute($setting = []) {
            $transaction_resource = $this->transaction->getResource();

            if (empty($transaction_resource)) {
                throw new WF_Exception('WF_conectionResourceDoNotInitiated');
            }

            $join = 'inner';

            if (!empty($setting)) {
                if (array_key_exists('join',$setting)) {
                    $join = $setting['join'];
                }
            }

            $table_name = $this->getTableName();
            $table_column = $this->getTableColumn();
            $get_where = $this->getWhere();
            $get_where_value = $this->getWhereValue();
            $order_by = $this->getOrderBy();
            $limit = $this->getLimit();
            $related = $this->related($this);

            $table_name_with_escape = vsprintf('%s%s%s',[$this->db_escape,$table_name,$this->db_escape]);

            $related_join = null;
            $related_column = [];

            if (!empty($related) && !empty($related['join'])) {
                $related_join = vsprintf('%s %s',[$join,implode(vsprintf(' %s ',[$join,]),$related['join'])]);

                foreach ($related['column'] as $i => $column) {
                    $related_column[] = implode(',',$column);
                }

            }

            $column_list = [];

            foreach ($table_column as $i => $column) {
                $column_list[] = vsprintf('%s.%s %s__%s',[$table_name_with_escape,$i,$table_name,$i]);
            }

            $column_list = array_merge($related_column,$column_list);
            $column_list = implode(',',$column_list);

            $query_value = [];

            if (empty($get_where)) {
                $where = '';

            } else {
                $where = vsprintf('where %s',[implode(' and ',$get_where),]);

                $query_value = array_merge($query_value,$get_where_value);
            }

            if (empty($order_by)) {
                $order_by = '';

            } else {
                $order_by = vsprintf('order by %s',[implode(',',$order_by),]);
            }

            $query_total = vsprintf('select count(1) total from %s %s %s',[$table_name_with_escape,$related_join,$where]);

            $this->setQuery($query_total,$query_value);

            $query = vsprintf('select %s from %s %s %s %s %s',[$column_list,$table_name_with_escape,$related_join,$where,$order_by,$limit]);

            $this->setQuery($query,$query_value);

            try {
                $pdo_query_total = $transaction_resource->prepare($query_total);

                $transaction_resource_error_info = $transaction_resource->errorInfo();

                if ($transaction_resource_error_info[0] != '00000') {
                    throw new WF_Exception($transaction_resource_error_info[2]);
                }

                $pdo_query_total->execute($query_value);
                $pdo_query_total = $pdo_query_total->fetch(PDO::FETCH_OBJ);

            } catch (Exception $error) {
                throw new WF_Exception($error);
            }

            try {
                $pdo_query = $transaction_resource->prepare($query);

                $transaction_resource_error_info = $transaction_resource->errorInfo();

                if ($transaction_resource_error_info[0] != '00000') {
                    throw new WF_Exception($transaction_resource_error_info[2]);
                }

                $pdo_query->execute($query_value);
                $query_fetch_all = $pdo_query->fetchAll(PDO::FETCH_OBJ);

            } catch (Exception $error) {
                throw new WF_Exception($error);
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
                        $table_column = vsprintf('%s__%s',[$table_name,$column]);

                        $obj->$column = $query_fetch->$table_column;
                    }

                    $obj_column_list = $obj->getTableColumn();
                    $obj_schema_dict = $obj->schema();

                    $related_fetch = $this->relatedFetch($obj_column_list,$obj_schema_dict,$query_fetch,$transaction,$obj);

                    $query_fetch_all_list[] = $related_fetch;
                }
            }

            $limit_value = $this->getLimitValue();
            $register_total = $pdo_query_total->total;
            $register_perpage = $limit_value['limit'];
            $page_total = ceil($register_total / $register_perpage);
            $page_current = $limit_value['page'] >= $page_total ? $page_total : $limit_value['page'];
            $page_next = $page_current + 1 >= $page_total ? $page_total : $page_current + 1;
            $page_previous = $page_current - 1 <= 0 ? 1 : $page_current - 1;

            $result = [
                'register_total' => $pdo_query_total->total,
                'register_perpage' => $register_perpage,
                'page_total' => $page_total,
                'page_current' => $page_current,
                'page_next' => $page_next,
                'page_previous' => $page_previous,
                'data' => $query_fetch_all_list,
            ];

            return $result;
        }

        private function relatedFetch($obj_column_list,$obj_schema_dict,$fetch,$transaction,$obj) {
            foreach ($obj_column_list as $column => $value) {
                if ($obj_schema_dict[$column]->method == 'foreignKey') {
                    $obj_foreignkey = $obj_schema_dict[$column]->rule['table'];

                    $obj_foreignkey_class_name = $obj_foreignkey->getClassName();
                    $obj_foreignkey_table_name = $obj_foreignkey->getTableName();
                    $obj_foreignkey_column_list = $obj_foreignkey->getTableColumn();
                    $obj_foreignkey_schema_dict = $obj_foreignkey->schema();

                    $obj_foreignkey = new $obj_foreignkey_class_name($transaction);

                    foreach ($obj_foreignkey_column_list as $column_ => $value_) {
                        $table_column = vsprintf('%s__%s',[$obj_foreignkey_table_name,$column_]);

                        $obj_foreignkey->$column_ = $fetch->$table_column;
                    }

                    $obj->$column = $obj_foreignkey;

                    $this->relatedFetch($obj_foreignkey_column_list,$obj_foreignkey_schema_dict,$fetch,$transaction,$obj_foreignkey);
                }
            }

            return $obj;
        }

        public function dumpQuery() {
            $query = $this->getQuery();

            return $query;
        }

        public function __destruct() {
            unset($this);
        }
    }
}
