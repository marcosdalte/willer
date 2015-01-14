<?php

namespace Core {
    use \Exception as Exception;
    use \Core\DAO\Transaction;
    use \Core\DAO\DataManipulationLanguage;

    abstract class Model extends DataManipulationLanguage {
        public function __construct() {}

        protected function column() {
            return get_object_vars($this);
        }

        private function filterRule($rule,$value) {
            $flag_null = false;

            if (!empty($rule)) {
                foreach ($rule as $key => $value_) {
                    if (!in_array($key,["null","length"])) {
                        throw new Exception("parameter value incorrect(option).");

                    } else if ($key == "null") {
                        if (!($value_ === 0 || $value_ === 1)) {
                            throw new Exception("parameter value incorrect(null).");

                        } else if ($value_ == 0) {
                            if (is_null($value)) {
                                throw new Exception("field can not be null.");

                            }

                        } else if ($value == 1) {
                            if (is_null($value)) {
                                $flag_null = true;

                            }
                        }

                    } else if ($key == "length") {
                        if (strlen($value) > $value_) {
                            throw new Exception("field length incorrect.");
                        }
                    }
                }
            }

            return (object) [
                "flag_null" => $flag_null,
            ];
        }

        protected function primaryKey($rule = [],$value = null,$flag = false) {
            if (empty($flag)) {
                return (object) [
                    "method" => __function__,
                    "rule" => $rule];

            } else {
                try {
                    $filter_rule = $this->filterRule($rule,$value);

                } catch (Exception $error) {
                    throw new Exception($error);

                }

                if (empty($filter_rule->flag_null)) {
                    $option = [
                        "options" => [
                            "default" => null],
                        "flags" => []];

                    if (!filter_var($value,FILTER_VALIDATE_INT,$option)) {
                        throw new Exception("integerfield value incorrect.");
                    }
                }

                return true;
            }
        }

        protected function char($rule = [],$value = null,$flag = false) {
            if (empty($flag)) {
                return (object) [
                    "method" => __function__,
                    "rule" => $rule];

            } else {
                try {
                    $filter_rule = $this->filterRule($rule,$value);

                } catch (Exception $error) {
                    throw new Exception($error);

                }

                if (empty($filter_rule->flag_null)) {
                    $option = [
                        "options" => [
                            "default" => null,
                            "regexp" => "/^[\w\W\d\D]+$/"],
                        "flags" => []];

                    if (!filter_var($value,FILTER_VALIDATE_REGEXP,$option)) {
                        throw new Exception("charfield value incorrect.");
                    }
                }

                return true;
            }
        }

        protected function text($rule = [],$value = null,$flag = false) {
            if (empty($flag)) {
                return (object) [
                    "method" => __function__,
                    "rule" => $rule];

            } else {
                try {
                    $filter_rule = $this->filterRule($rule,$value);

                } catch (Exception $error) {
                    throw new Exception($error);

                }

                if (empty($filter_rule->flag_null)) {
                    $option = [
                        "options" => [
                            "default" => null,
                            "regexp" => "/^[\w\W\d\D]+$/"],
                        "flags" => []];

                    if (!filter_var($value,FILTER_VALIDATE_REGEXP,$option)) {
                        throw new Exception("textfield value incorrect.");
                    }
                }

                return true;
            }
        }

        protected function integer($rule = [],$value = null,$flag = false) {
            if (empty($flag)) {
                return (object) [
                    "method" => __function__,
                    "rule" => $rule];

            } else {
                try {
                    $filter_rule = $this->filterRule($rule,$value);

                } catch (Exception $error) {
                    throw new Exception($error);

                }

                if (empty($filter_rule->flag_null)) {
                    $option = [
                        "options" => [
                            "default" => null],
                        "flags" => []];

                    if (!filter_var($value,FILTER_VALIDATE_INT,$option)) {
                        throw new Exception("integerfield value incorrect.");
                    }
                }

                return true;
            }
        }

        protected function boolean($rule = [],$value = null,$flag = false) {
            if (empty($flag)) {
                return (object) [
                    "method" => __function__,
                    "rule" => $rule];

            } else {
                try {
                    $filter_rule = $this->filterRule($rule,$value);

                } catch (Exception $error) {
                    throw new Exception($error);

                }

                if (empty($filter_rule->flag_null)) {
                    $option = [
                        "options" => [
                            "default" => null],
                        "flags" => []];

                    if (!filter_var($value,FILTER_VALIDATE_BOOLEAN,$option)) {
                        throw new Exception("booleanfield value incorrect.");
                    }
                }

                return true;
            }
        }

        protected function datetime($rule = [],$value = null,$flag = false) {
            if (empty($flag)) {
                return (object) [
                    "method" => __function__,
                    "rule" => $rule];

            } else {
                try {
                    $filter_rule = $this->filterRule($rule,$value);

                } catch (Exception $error) {
                    throw new Exception($error);

                }

                if (empty($filter_rule->flag_null)) {
                    $option = [
                        "options" => [
                            "default" => null,
                            "regexp" => "/^([0-9]{4}-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01]))[ ]([01][0-9]|2[0123]):([012345][0-9]):([012345][0-9])$|^((0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-[0-9]{4})[ ]([01][0-9]|2[0123]):([012345][0-9]):([012345][0-9])$/"],
                        "flags" => []];

                    if (!filter_var($value,FILTER_VALIDATE_REGEXP,$option)) {
                        throw new Exception("datetimefield value incorrect.");
                    }
                }

                return true;
            }
        }

        protected function date($rule = [],$value = null,$flag = false) {
            if (empty($flag)) {
                return (object) [
                    "method" => __function__,
                    "rule" => $rule];

            } else {
                try {
                    $filter_rule = $this->filterRule($rule,$value);

                } catch (Exception $error) {
                    throw new Exception($error);

                }

                if (empty($filter_rule->flag_null)) {
                    $option = [
                        "options" => [
                            "default" => null,
                            "regexp" => "/^([0-9]{4}-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01]))$|^((0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-[0-9]{4})$/"],
                        "flags" => []];

                    if (!filter_var($value,FILTER_VALIDATE_REGEXP,$option)) {
                        throw new Exception("datefield value incorrect.");
                    }
                }

                return true;
            }
        }

        protected function time($rule = [],$value = null,$flag = false) {
            if (empty($flag)) {
                return (object) [
                    "method" => __function__,
                    "rule" => $rule];

            } else {
                try {
                    $filter_rule = $this->filterRule($rule,$value);

                } catch (Exception $error) {
                    throw new Exception($error);

                }

                if (empty($filter_rule->flag_null)) {
                    $option = [
                        "options" => [
                            "default" => null,
                            "regexp" => "/^([01][0-9]|2[0123]):([012345][0-9]):([012345][0-9])$/"],
                        "flags" => []];

                    if (!filter_var($value,FILTER_VALIDATE_REGEXP,$option)) {
                        throw new Exception("timefield value incorrect.");
                    }
                }

                return true;
            }
        }

        protected function float($rule = [],$value = null,$flag = false) {
            if (empty($flag)) {
                return (object) [
                    "method" => __function__,
                    "rule" => $rule];

            } else {
                try {
                    $filter_rule = $this->filterRule($rule,$value);

                } catch (Exception $error) {
                    throw new Exception($error);

                }

                if (empty($filter_rule->flag_null)) {
                    $option = [
                        "options" => [
                            "default" => null,
                            "decimal" => ","],
                        "flags" => []];

                    if (!filter_var($value,FILTER_VALIDATE_FLOAT,$option)) {
                        throw new Exception("floatfield value incorrect.");
                    }
                }

                return true;
            }
        }

        protected function email($rule = [],$value = null,$flag = false) {
            if (empty($flag)) {
                return (object) [
                    "method" => __function__,
                    "rule" => $rule];

            } else {
                try {
                    $filter_rule = $this->filterRule($rule,$value);

                } catch (Exception $error) {
                    throw new Exception($error);

                }

                if (empty($filter_rule->flag_null)) {
                    $option = [
                        "options" => [
                            "default" => null,],
                        "flags" => []];

                    if (!filter_var($value,FILTER_VALIDATE_EMAIL,$option)) {
                        throw new Exception("emailfield value incorrect.");
                    }
                }

                return true;
            }
        }

        protected function ip($rule = [],$value = null,$flag = false) {
            if (empty($flag)) {
                return (object) [
                    "method" => __function__,
                    "rule" => $rule];

            } else {
                try {
                    $filter_rule = $this->filterRule($rule,$value);

                } catch (Exception $error) {
                    throw new Exception($error);

                }

                if (empty($filter_rule->flag_null)) {
                    $option = [
                        "options" => [
                            "default" => null,],
                        "flags" => []];

                    if (!filter_var($value,FILTER_VALIDATE_IP,$option)) {
                        throw new Exception("ipfield value incorrect.");
                    }
                }

                return true;
            }
        }

        protected function url($rule = [],$value = null,$flag = false) {
            if (empty($flag)) {
                return (object) [
                    "method" => __function__,
                    "rule" => $rule];

            } else {
                try {
                    $filter_rule = $this->filterRule($rule,$value);

                } catch (Exception $error) {
                    throw new Exception($error);

                }

                if (empty($filter_rule->flag_null)) {
                    $option = [
                        "options" => [
                            "default" => null,],
                        "flags" => []];

                    if (!filter_var($value,FILTER_VALIDATE_URL,$option)) {
                        throw new Exception("urlfield value incorrect.");
                    }
                }

                return true;
            }
        }
    }
}

?>