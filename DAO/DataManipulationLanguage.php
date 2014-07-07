<?php

namespace DAO {
    use \PDO as PDO;
    use \Exception as Exception;
    use \Util;
    use \DAO\Connection;
    abstract class DataManipulationLanguage extends Connection {
        private $table_name;
        private $table_column;
        private $table_primary_key;
        private $field;
        private $related;
        private $page;
        private $limit;
        private $order_by;
        private $query_value;
        private $update_filter_sql_escape;
        private $update_filter_sql_value_list;
        private $update_result;

        public function __construct() {
            $this->clearData();
            $this->connect();
        }

        protected function getTableName() {
            return $this->table_name;
        }

        protected function setTableName($value) {
            $this->table_name = $value;
        }

        protected function getTableColumn() {
            return $this->table_column;
        }

        protected function setTableColumn($value) {
            $this->table_column = $value;
        }

        protected function getTablePrimaryKey() {
            return $this->table_primary_key;
        }

        protected function setTablePrimaryKey($value) {
            $this->table_primary_key = $value;
        }

        protected function getField() {
            return $this->field;
        }

        protected function setField($value) {
            $this->field = $value;
        }

        protected function getRelated() {
            return $this->related;
        }

        protected function setRelated($value) {
            $this->related = $value;
        }

        protected function getPage() {
            return $this->page;
        }

        protected function setPage($value) {
            $this->page = $value;
        }

        protected function getLimit() {
            return $this->limit;
        }

        protected function setLimit($value) {
            $this->limit = $value;
        }

        protected function getOrderBy() {
            return $this->order_by;
        }

        protected function setOrderBy($value) {
            $this->order_by = $value;
        }

        protected function getQueryValue() {
            return $this->query_value;
        }

        protected function setQueryValue($value) {
            $this->query_value = $value;
        }

        protected function getUpdateFilterSqlEscape() {
            return $this->update_filter_sql_escape;
        }

        protected function setUpdateFilterSqlEscape($value) {
            $this->update_filter_sql_escape = $value;
        }

        protected function getUpdateFilterSqlValueList() {
            return $this->update_filter_sql_value_list;
        }

        protected function setUpdateFilterSqlValueList($value) {
            $this->update_filter_sql_value_list = $value;
        }

        protected function getUpdateResult() {
            return $this->update_result;
        }

        protected function setUpdateResult($value) {
            $this->update_result = $value;
        }

        public function value() {
            return $this->getQueryValue();
        }

        public function field($field = []) {
            if (!empty($field)) {
                $list = null;

                foreach ($field as $key => $value) {
                    $list[] = vsprintf("%s as %s",[$key,$value]);
                }

                $list = implode(",",$list);

                $this->setField($list);
            }

            return $this;
        }

        public function orderBy($order_by = []) {
            if (!empty($order_by)) {
                $list = [];

                foreach ($order_by as $key => $value) {
                    $list[] = vsprintf("%s %s",[$key,$value]);
                }

                $list = implode(",",$list);
                $list = vsprintf("order by %s",[$list]);

                $this->setOrderBy($list);
            }

            return $this;
        }

        public function page($page = 1,$limit = QUERY_LIMIT_ROW) {
            $value = null;

            if ($page <= 1) {
                $value = vsprintf("limit %s offset 0",[QUERY_LIMIT_ROW]);

            } else {
                $page -= 1;
                $page_x_limit = $page * $limit;
                $value = vsprintf("limit %s offset %s",[$limit,$page_x_limit]);
            }

            $this->setPage($value);

            return $this;
        }

        protected function related($related) {
            $list = [];

            foreach ($related as $key => $value) {
                foreach ($value as $sub_key => $sub_value) {
                    $table_b = key($sub_value);
                    $foreign_key_b = current($sub_value);

                    $list[] = vsprintf("inner join %s on %s.%s=%s.%s",[$key,$key,$sub_key,$table_b,$foreign_key_b]);
                }
            }

            $value = implode(" ",$list);

            $this->setRelated($value);

            return $this;
        }

        // public function exclude($exclude = array()) {
            // if (empty($exclude)) {
            //     $where = "";
  
            // } else {
            //     $where = array();
  
            //     foreach ($exclude as $key => $value) {
            //         $value_escape = array();
  
            //         foreach ($value as $sub_value) {
            //             $value_escape[] = "?";
            //             $this->sql_consult_escape[] = $sub_value;
            //         }
  
            //         $where[] = $key." not in(".implode(",",$value_escape).")";
            //     }
  
            //     $where = " and ".implode(" and ",$where);
            // }
  
            // retorna variavel $this->sql_exclude
  
            // return $this;
        // }

        public function filter($where = []) {
            $related = $this->getRelated();
            $table_name = $this->getTableName();
            $field = $this->getField();
            $order_by = $this->getOrderBy();
            $limit = $this->getLimit();
            $link = $this->getLink();

            $sql_consult_total = vsprintf("select count(1) from %s %s",[$table_name,$related]);
            $sql_consult_id = vsprintf("select %s.id from %s %s",[$table_name,$table_name,$related]);
            $sql_consult = vsprintf("select %s from %s %s",[$field,$table_name,$related]);

            $sql_value_list = [];

            if (empty($where)) {
                $sql_consult .= vsprintf(" %s %s",[$order_by,$limit]);

            } else {
                $where_escape = [];

                foreach ($where as $key => $value) {
                    $value_escape_list = [];

                    foreach ($value as $sub_value) {
                        $value_escape_list[] = "?";
                        $sql_value_list[] = $sub_value;
                    }

                    $value_escape_list = implode(",",$value_escape_list);

                    $where_escape[] = vsprintf("%s in(%s)",[$key,$value_escape_list]);
                }

                $where_escape = implode(" and ",$where_escape);
                $where_escape = vsprintf("where ",[$where_escape]);

                $sql_consult_total .= vsprintf(" %s",[$where_escape]);
                $sql_consult_id .= vsprintf(" %s",[$where_escape]);
                $sql_consult .= vsprintf(" %s %s s%",[$where_escape,$order_by,$limit]);
            }

            try {
                $statement = $link->prepare($sql_consult);
                $statement->execute($sql_value_list);
                $result = $statement->fetchAll(PDO::FETCH_CLASS);

            } catch (Exception $error) {
                $this->clearData();

                throw new Exception($error);
            }

            $this->clearData();

            $this->setQueryValue($result);
            $this->setUpdateFilterSqlEscape($sql_consult_id);
            $this->setUpdateFilterSqlValueList($sql_value_list);

            return $this;
        }

        public function get($where = []) {
            if (empty($where)) {
                $this->clearData();

                throw new Exception("error in get, where don't set");
            }

            $related = $this->getRelated();
            $table_column = $this->getTableColumn();
            $table_name = $this->getTableName();
            $link = $this->getLink();

            $where_escape = [];

            foreach ($where as $key => $value) {
                $where_escape[] = $table_name.".".$key."=?";
                $where_value[] = $value;
            }

            $where = "where ".implode(" and ",$where_escape);
            $field = [];

            foreach ($table_column as $key => $value) {
                $field[] = $table_name.".".$key;
            }

            $field = implode(",",$field);

            $sql_consult_total = "select count(1) as total from ".$table_name." ".$related." ".$where;
            $sql_consult = "select ".$field." from ".$table_name." ".$related." ".$where;

            try {
                $result_total = $link->prepare($sql_consult_total);
                $result_total->execute($where_value);
                $result_total = $result_total->fetch(PDO::FETCH_OBJ);

                if ($result_total->total != 1) {
                    $this->clearData();

                    throw new Exception("error in get, don't unique result");
                }

                $result = $link->prepare($sql_consult);
                $result->execute($where_value);
                $result = $result->fetch(PDO::FETCH_OBJ);

            } catch (Exception $error) {
                $this->clearData();

                throw new Exception($error);
            }

            foreach ($result as $key => $value) {
                $this->$key = $value;
            }

            $this->clearData();
            $this->setQueryValue($result);

            return $this;
        }

        public function save($field = null) {
            $this->clearData();
            $table_column = $this->getTableColumn();
            $table_name = $this->getTableName();
            $table_primary_key = $this->getTablePrimaryKey();
            $link = $this->getLink();

            if (!empty($field)) {
                foreach ($field as $key => $value) {
                    if (!array_key_exists($key,$table_column)) {
                        $this->clearData();

                        throw new Exception("param missing");

                    } else {
                        $column[] = "".$key."";
                        $row[] = $value;
                        $row_escape[] = "?";
                    }
                }

                $column = implode(",",$column);
                $row_escape = implode(",",$row_escape);

                $sql = "insert into ".$table_name." (".$column.") values(".$row_escape.")";

            } else {
                foreach ($table_column as $key => $value) {
                    $set_escape[] = "".$key."=?";
                    $row[] = $value;
                }

                $set_escape = implode(",",$set_escape);
                $primary_key = [];

                foreach ($table_primary_key as $value) {
                    $primary_key[] = "".$value."='".$table_column[$value]."'";
                }

                $primary_key = implode(" and ",$primary_key);

                $sql = "update ".$table_name." set ".$set_escape." where ".$primary_key;
            }

            try {
                $prepare = $link->prepare($sql);
                $prepare->execute($row);

            } catch (Exception $error) {
                $this->clearData();

                throw new Exception($error);
            }

            $this->clearData();

            return $this;
        }

        public function update($set) {
            $table_name = $this->getTableName();
            $link = $this->getLink();
            $update_filter_sql_escape = $this->getUpdateFilterSqlEscape();
            $update_filter_sql_value_list = $this->getUpdateFilterSqlValueList();

            if (empty($set)) {
                $this->clearData();

                throw new Exception("param missing");
            }

            if (empty($update_filter_sql_escape) || empty($update_filter_sql_value_list)) {
                $this->clearData();

                throw new Exception("param missing");
            }

            $set_escape = [];
            $set_value_list = [];

            foreach ($set as $key => $value) {
                $set_escape[] = $table_name.".".$key."='".$value."'";
            }

            $set_escape = implode(",",$set_escape);

            try {
                $sql = $update_filter_sql_escape;
                $prepare = $link->prepare($sql);
                $prepare->execute($update_filter_sql_value_list);
                $result = $prepare->fetchAll(PDO::FETCH_BOTH);

            } catch (Exception $error) {
                $this->clearData();

                throw new Exception($error);
            }

            if (empty($result)) {
                $this->clearData();
  
                throw new Exception("query is empty for update.");
            }

            $id_list = [];

            foreach ($result as $key => $value) {
                $id_list[] = $value["id"];
            }

            $id_list = implode(",",$id_list);

            try {
                $sql = "update ".$table_name." set ".$set_escape." where ".$table_name.".id in(".$id_list.")";
                $prepare = $link->prepare($sql);
                $prepare->execute();
                $result = $prepare->rowCount();
  
            } catch (Exception $error) {
                $this->clearData();

                throw new Exception($error);
            }

            $this->clearData();
            $this->setUpdateResult($result);

            return $this;
        }

        public function rowCount() {
            return $this->getUpdateResult();
        }

        protected function clearData() {
            $this->setTableName($this->name());
            $this->setTableColumn($this->column());
            $this->setTablePrimaryKey($this->primaryKey());
            $this->setField("*");
            $this->setOrderBy(null);
            $this->setPage(1);
            $this->setLimit("limit ".QUERY_LIMIT_ROW." offset 0");
            $this->setRelated(null);
            $this->setUpdateResult(null);
            $this->setUpdateFilterSqlEscape(null);
            $this->setUpdateFilterSqlValueList(null);
            $this->setQueryValue(null);
        }

        public function __destruct() {
            unset($this);
        }
    }
}

?>