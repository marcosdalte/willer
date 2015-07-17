<?php

namespace Core\DAO {
    use \PDO as PDO;
    use \Exception as Exception;
    use \Util;
    use \DAO\Connection;
    class DataControlLanguage extends Connection {
        private $query_value;

        public function __construct() {
            $this->connect();
        }

        protected function getQueryValue() {
            return $this->query_value;
        }

        protected function setQueryValue($value) {
            $this->query_value = $value;
        }

        public function value() {
            return $this->getQueryValue();
        }

        private function flush() {
            $link = $this->getLink();

            $sql_consult = "FLUSH PRIVILEGES";

            try {
                $prepare = $link->prepare($sql_consult);
                $prepare->execute();
                $result = $prepare->rowCount();

            } catch (Exception $error) {
                throw new Exception($error);
            }

            return $this;
        }

        public function dbUserList() {
            $link = $this->getLink();

            $sql_consult = "select * from mysql.user";

            try {
                $prepare = $link->prepare($sql_consult);
                $prepare->execute();
                $result = $prepare->fetchAll(PDO::FETCH_CLASS);

            } catch (Exception $error) {
                throw new Exception($error);
            }

            $this->setQueryValue($result);

            return $this;
        }

        public function dbUserGrant($params = []) {
            $db_name = Util::get($params,"DB_NAME",null);
            $user_name = Util::get($params,"USER_NAME",null);
            $server = Util::get($params,"SERVER",null);
            $gran_select = Util::get($params,"GRAN_SELECT",null);
            $gran_insert = Util::get($params,"GRAN_INSERT",null);
            $gran_update = Util::get($params,"GRAN_UPDATE",null);
            $gran_delete = Util::get($params,"GRAN_DELETE",null);
            $gran_create = Util::get($params,"GRAN_CREATE",null);
            $gran_drop = Util::get($params,"GRAN_DROP",null);
            $gran_index = Util::get($params,"GRAN_INDEX",null);
            $gran_alter = Util::get($params,"GRAN_ALTER",null);
            $gran_locktables = Util::get($params,"GRAN_LOCKTABLES",null);
            $gran_createview = Util::get($params,"GRAN_CREATEVIEW",null);
            $gran_event = Util::get($params,"GRAN_EVENT",null);
            $gran_trigger = Util::get($params,"GRAN_TRIGGER",null);
            $gran_showview = Util::get($params,"GRAN_SHOWVIEW",null);
            $gran_createroutine = Util::get($params,"GRAN_CREATEROUTINE",null);
            $gran_alterroutine = Util::get($params,"GRAN_ALTERROUTINE",null);
            $gran_execute = Util::get($params,"GRAN_EXECUTE",null);

            if (empty($db_name) || empty($user_name) || empty($server)) {
                throw new Exception("param missing");
            }

            if (empty($gran_select)) {
                $gran_select = "SELECT,";

            } else {
                if ($gran_select == false) {
                    $gran_select = "";

                } else if ($gran_select == true) {
                    $gran_select = "SELECT,";

                } else {
                    $gran_select = "";
                }
            }

            if (empty($gran_insert)) {
                $gran_insert = "INSERT,";

            } else {
                if ($gran_insert == false) {
                    $gran_insert = "";

                } else if ($gran_insert == true) {
                    $gran_insert = "INSERT,";

                } else {
                    $gran_insert = "";
                }
            }

            if (empty($gran_update)) {
                $gran_update = "UPDATE,";

            } else {
                if ($gran_update == false) {
                    $gran_update = "";

                } else if ($gran_update == true) {
                    $gran_update = "UPDATE,";

                } else {
                    $gran_update = "";
                }
            }

            if (empty($gran_delete)) {
                $gran_delete = "DELETE,";

            } else {
                if ($gran_delete == false) {
                    $gran_delete = "";

                } else if ($gran_delete == true) {
                    $gran_delete = "UPDATE,";

                } else {
                    $gran_delete = "";
                }
            }

            if (empty($gran_create)) {
                $gran_create = "CREATE,";

            } else {
                if ($gran_create == false) {
                    $gran_create = "";

                } else if ($gran_create == true) {
                    $gran_create = "CREATE,";

                } else {
                    $gran_create = "";
                }
            }

            if (empty($gran_drop)) {
                $gran_drop = "DROP,";

            } else {
                if ($gran_drop == false) {
                    $gran_drop = "";

                } else if ($gran_drop == true) {
                    $gran_drop = "DROP,";

                } else {
                    $gran_drop = "";
                }
            }

            if (empty($gran_index)) {
                $gran_index = "INDEX,";

            } else {
                if ($gran_index == false) {
                    $gran_index = "";

                } else if ($gran_index == true) {
                    $gran_index = "INDEX,";

                } else {
                    $gran_index = "";
                }
            }

            if (empty($gran_alter)) {
                $gran_alter = "ALTER,";

            } else {
                if ($gran_alter == false) {
                    $gran_alter = "";

                } else if ($gran_alter == true) {
                    $gran_alter = "ALTER,";

                } else {
                    $gran_alter = "";
                }
            }

            if (empty($gran_locktables)) {
                $gran_locktables = "LOCK TABLES,";

            } else {
                if ($gran_locktables == false) {
                    $gran_locktables = "";

                } else if ($gran_locktables == true) {
                    $gran_locktables = "LOCK TABLES,";

                } else {
                    $gran_locktables = "";
                }
            }

            if (empty($gran_createview)) {
                $gran_createview = "CREATE VIEW,";

            } else {
                if ($gran_createview == false) {
                    $gran_createview = "";

                } else if ($gran_createview == true) {
                    $gran_createview = "CREATE VIEW,";

                } else {
                    $gran_createview = "";
                }
            }

            if (empty($gran_event)) {
                $gran_event = "EVENT,";

            } else {
                if ($gran_event == false) {
                    $gran_event = "";

                } else if ($gran_event == true) {
                    $gran_event = "EVENT,";

                } else {
                    $gran_event = "";
                }
            }

            if (empty($gran_trigger)) {
                $gran_trigger = "TRIGGER,";

            } else {
                if ($gran_trigger == false) {
                    $gran_trigger = "";

                } else if ($gran_trigger == true) {
                    $gran_trigger = "TRIGGER,";

                } else {
                    $gran_trigger = "";
                }
            }

            if (empty($gran_showview)) {
                $gran_showview = "SHOW VIEW,";

            } else {
                if ($gran_showview == false) {
                    $gran_showview = "";

                } else if ($gran_showview == true) {
                    $gran_showview = "SHOW VIEW,";

                } else {
                    $gran_showview = "";
                }
            }

            if (empty($gran_createroutine)) {
                $gran_createroutine = "CREATE ROUTINE,";

            } else {
                if ($gran_createroutine == false) {
                    $gran_createroutine = "";

                } else if ($gran_createroutine == true) {
                    $gran_createroutine = "CREATE ROUTINE,";

                } else {
                    $gran_createroutine = "";
                }
            }

            if (empty($gran_alterroutine)) {
                $gran_alterroutine = "ALTER ROUTINE,";

            } else {
                if ($gran_alterroutine == false) {
                    $gran_alterroutine = "";

                } else if ($gran_alterroutine == true) {
                    $gran_alterroutine = "ALTER ROUTINE,";

                } else {
                    $gran_alterroutine = "";
                }
            }

            if (empty($gran_execute)) {
                $gran_execute = "EXECUTE,";

            } else {
                if ($gran_execute == false) {
                    $gran_execute = "";

                } else if ($gran_execute == true) {
                    $gran_execute = "EXECUTE,";

                } else {
                    $gran_execute = "";
                }
            }

            $link = $this->getLink();

            $sql_consult = "GRANT ".$gran_select."".$gran_insert."".$gran_update."".$gran_delete."".$gran_create."".$gran_drop."".$gran_index."".$gran_alter."".$gran_locktables."".$gran_createview."".$gran_event."".$gran_trigger."".$gran_showview."".$gran_createroutine."".$gran_alterroutine."".$gran_execute." ON ".$db_name.".* TO ".$user_name."@".$server." WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0";

            try {
                $prepare = $link->prepare($sql_consult);
                $prepare->execute();
                $result = $prepare->rowCount();

            } catch (Exception $error) {
                throw new Exception($error);
            }

            $this->flush();
            $this->setQueryValue($result);

            return $this;
        }

        public function dbUserRevoke($params = []) {
            $db_name = Util::get($params,"DB_NAME",null);
            $user_name = Util::get($params,"USER_NAME",null);
            $server = Util::get($params,"SERVER",null);

            if (empty($db_name) || empty($user_name) || empty($server)) {
                throw new Exception("param missing");
            }

            $link = $this->getLink();

            $sql_consult = "REVOKE SELECT,INSERT,UPDATE,DELETE,CREATE,DROP,INDEX,ALTER,LOCK TABLES,CREATE VIEW,EVENT,TRIGGER,SHOW VIEW,CREATE ROUTINE,ALTER ROUTINE,EXECUTE ON ".$db_name.".* FROM ".$user_name."@".$server."";
            // $sql_consult = "REVOKE ALL PRIVILEGES, GRANT OPTION ON ".$db_name.".* FROM ".$user_name."@".$server."";

            try {
                $prepare = $link->prepare($sql_consult);
                $prepare->execute();
                $result = $prepare->rowCount();

            } catch (Exception $error) {
                throw new Exception($error);
            }

            $this->flush();
            $this->setQueryValue($result);

            return $this;
        }

        public function __destruct() {
            unset($this);
        }
    }
}

?>