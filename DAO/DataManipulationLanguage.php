<?php

namespace DAO {
    use \PDO as PDO;
    use \Exception as Exception;
	use \Helper\Util;

    abstract class DataManipulationLanguage {
        private $table_name;
        private $table_column;
        private $table_primary_key;
		private $field;
        private $related;
		private $exclude;
		private $exclude_escape;
        private $page;
        private $order_by;
        private $query_value;
        private $update_filter_sql_escape;
        private $update_filter_sql_value_list;
        private $update_result;

        public function __construct() {
            $this->clearData();
        }

		protected function clearData() {
            $this->setTableName($this->name());
            $this->setTableColumn($this->column());
            $this->setTablePrimaryKey($this->schema());
            $this->field(null);
            $this->orderBy(null);
            $this->page(1);
            $this->setUpdateResult(null);
            $this->setUpdateFilterSqlEscape(null);
            $this->setUpdateFilterSqlValueList(null);
            $this->setQueryValue(null);
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

		protected function getExcludeEscape() {
            return $this->exclude_escape;
        }

		protected function setExcludeEscape($value) {
            $this->exclude_escape = $value;
        }

		protected function getExclude() {
            return $this->exclude;
        }

        protected function setExclude($value) {
            $this->exclude = $value;
        }

        protected function getPage() {
            return $this->page;
        }

        protected function setPage($value) {
            $this->page = $value;
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

		public function field($field = null) {
            if (!empty($field)) {
				$this->setField($field);

			} else {
				$field = $this->getTableColumn();
				$table_name = $this->getTableName();
				$list = [];

				foreach ($field as $key => $value) {
					$list[] = vsprintf("%s.%s %s__%s",[$table_name,$key,$table_name,$key]);

				}

				$this->setField($list);
			}
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

		public function related($foreign_Key = null,$field = [],$list = []) {
			$table_name = $this->getTableName();
			$table_primary_key = $this->getTablePrimaryKey();
			
			if (!empty($foreign_Key)) {
				$table_foreign_Key = $foreign_Key->getTableForeignKey();

			} else {
				$table_foreign_Key = $this->getTableForeignKey();
			}

			if (!empty($table_foreign_Key)) {
				$list = [];

				foreach ($table_foreign_Key as $key => $value) {
					$table_index = $key;
					$foreign_Key = $value["index"];
                    $field = array_merge($field,$foreign_Key->getField());

					$table_join = $foreign_Key->name();
					$table_join_index = $foreign_Key->primaryKey();
					$table_join_index_null = $value["null"];

					$join_type = "inner";

					if (!empty($table_join_index_null)) {
						$join_type = "left";
					}

					$list[] = vsprintf("%s join %s on %s.%s=%s.%s",[$join_type,$table_join,$table_join,$table_join_index,$table_name,$table_index]);

                    $get_table_foreign_key = $foreign_Key->getTableForeignKey();

					if (!empty($get_table_foreign_key)) {
						$this->related($foreign_Key,$field,$list);
					}
				}

                $this->setField(array_merge($this->getField(),$field));

				$value = implode(" ",$list);
				$this->setRelated($value);
			}

            return $this;
        }

		public function exclude($exclude = []) {
			if (!empty($exclude)) {
				$list = [];
				
				foreach ($exclude as $key => $value) {
					$this->setExcludeEscape($value);
					
					$list[] = vsprintf("%s not in(?)",[$key,]);
				}

				$list = implode(" and ",$list);

				$this->setExclude($list);
			}

			return $this;
        }
		/*refatorar*/
        public function filter($where = []) {
			$link = $this->getLink();
            $table_name = $this->getTableName();
			$table_primary_key = $this->getTablePrimaryKey();
			$related = $this->getRelated();
			$exclude = $this->getExclude();
			$exclude_escape = $this->getExcludeEscape();
            $order_by = $this->getOrderBy();
            $page = $this->getPage();
            $field = $this->getField();
            $field = implode(", ",$field);

            $sql_consult_id = vsprintf("select %s.%s from %s %s",[$table_name,$table_primary_key,$table_name,$related]);
            $sql_consult = vsprintf("select %s from %s %s",[$field,$table_name,$related]);

            $sql_value_list = [];

            if (empty($where)) {
                $sql_consult .= vsprintf(" %s %s",[$order_by,$page]);

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
                $where_escape = vsprintf("where %s",[$where_escape,]);

                $sql_consult_id .= vsprintf(" %s",[$where_escape]);
                $sql_consult .= vsprintf(" %s %s %s",[$where_escape,$order_by,$page]);
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

        public function __destruct() {
            unset($this);
        }
    }
}

?>