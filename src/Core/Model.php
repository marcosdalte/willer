<?php

namespace Core {
    use \Exception as Exception;
    use \Core\DAO\Transaction;
    use \Core\DAO\DataManipulationLanguage;

    abstract class Model extends DataManipulationLanguage {
        public function __construct(Transaction $transaction = null) {
            $this->definePrimaryKey(null);

            if (!empty($transaction)) {
                parent::__construct($transaction);
            }
        }

        public function __debugInfo() {
            return $this->column();
        }

        protected function className() {
            return get_class($this);
        }

        protected function column() {
            return get_object_vars($this);
        }

        private static function filterRule($rule_list,$value,$function_name,$function_filter) {
            if (empty($rule_list)) {
                if ($function_name != 'boolean' && empty($value)) {
                    throw new Exception('field value is missing');
                }

            } else {
                $rule_null = null;
                $rule_length = null;
                $rule_table = null;

                foreach ($rule_list as $rule_name => $rule_value) {
                    if (!in_array($rule_name,['null','length','table'])) {
                        throw new Exception('rule incorrect');

                    } else if ($rule_name == 'null') {
                        $rule_null = $rule_value;

                    } else if ($rule_name == 'length') {
                        $rule_length = $rule_value;

                    } else if ($rule_name == 'table') {
                        $rule_table = $rule_value;
                    }
                }

                if (empty($rule_null)) {
                    if (empty($value)) {
                        throw new Exception('value can not be null');
                    }
                }

                switch ($function_name) {
                    case 'foreignKey':
                        if (empty($rule_table)) {
                            throw new Exception('foreignkey require one instance');
                        }

                        break;
                }

                if (!empty($rule_table)) {
                    if (empty($value)) {
                        if (empty($rule_null)) {
                            throw new Exception('foreign key object is missing');
                        }

                    } else {
                        if (!is_object($rule_table)) {
                            throw new Exception('foreign key not an object');
                        }

                        if (!$value instanceof $rule_table) {
                            throw new Exception('foreign key is not a valid instance');
                        }
                    }

                } else if (!empty($rule_length)) {
                    if (empty($value)) {
                        if (empty($rule_null)) {
                            throw new Exception('field value is missing');
                        }

                    } else {
                        if (!is_numeric($rule_length)) {
                            throw new Exception('field value dont numeric');
                        }

                        if (strlen($value) > $rule_length) {
                            throw new Exception('field value length is incorrect');
                        }
                    }
                }
            }

            if (empty($rule_null)) {
                try {
                    $value = $function_filter($value);

                } catch (Exception $error) {
                    throw new Exception($error);
                }

                return $value;

            } else if (!empty($rule_null) && !empty($value)) {
                try {
                    $value = $function_filter($value);

                } catch (Exception $error) {
                    throw new Exception($error);
                }

                return $value;
            }

            return null;
        }

        protected static function primaryKey($rule = [],$value = null,$flag = false) {
            if (empty($flag)) {
                return (object) [
                    'method' => __function__,
                    'rule' => $rule];

            } else {
                try {
                    $filter_rule = self::filterRule($rule,$value,__function__,function($value) {
                        $filter_var_option = [
                            'options' => [
                                'default' => null],
                            'flags' => []];

                        if (!filter_var($value,FILTER_VALIDATE_INT,$filter_var_option)) {
                            throw new Exception('primarykey value incorrect');
                        }

                        return $value;
                    });

                } catch (Exception $error) {
                    throw new Exception($error);
                }

                return $filter_rule;
            }
        }

        protected static function foreignKey($rule = [],$value = null,$flag = false) {
            if (empty($flag)) {
                return (object) [
                    'method' => __function__,
                    'rule' => $rule];

            } else {
                try {
                    $filter_rule = self::filterRule($rule,$value,__function__,function($value) {
                        $get_primary_key = $value->getPrimaryKey();

                        if (empty($get_primary_key)) {
                            throw new Exception('foreignkey not defined');
                        }

                        return $value->$get_primary_key;
                    });

                } catch (Exception $error) {
                    throw new Exception($error);
                }

                return $filter_rule;
            }
        }

        protected static function char($rule = [],$value = null,$flag = false) {
            if (empty($flag)) {
                return (object) [
                    'method' => __function__,
                    'rule' => $rule];

            } else {
                try {
                    $filter_rule = self::filterRule($rule,$value,__function__,function($value) {
                        $filter_var_option = [
                            'options' => [
                                'default' => null,
                                'regexp' => '/^[\w\W\d\D]+$/'],
                            'flags' => []];

                        if (!filter_var($value,FILTER_VALIDATE_REGEXP,$filter_var_option)) {
                            throw new Exception('charfield value incorrect');
                        }

                        return $value;
                    });

                } catch (Exception $error) {
                    throw new Exception($error);
                }

                return $filter_rule;
            }
        }

        protected static function text($rule = [],$value = null,$flag = false) {
            if (empty($flag)) {
                return (object) [
                    'method' => __function__,
                    'rule' => $rule];

            } else {
                try {
                    $filter_rule = self::filterRule($rule,$value,__function__,function($value) {
                        $filter_var_option = [
                            'options' => [
                                'default' => null,
                                'regexp' => '/^[\w\W\d\D]+$/'],
                            'flags' => []];

                        if (!filter_var($value,FILTER_VALIDATE_REGEXP,$filter_var_option)) {
                            throw new Exception('textfield value incorrect');
                        }

                        return $value;
                    });

                } catch (Exception $error) {
                    throw new Exception($error);
                }

                return $filter_rule;
            }
        }

        protected static function integer($rule = [],$value = null,$flag = false) {
            if (empty($flag)) {
                return (object) [
                    'method' => __function__,
                    'rule' => $rule];

            } else {
                try {
                    $filter_rule = self::filterRule($rule,$value,__function__,function($value) {
                        $filter_var_option = [
                            'options' => [
                                'default' => null],
                            'flags' => []];

                        if (!filter_var($value,FILTER_VALIDATE_INT,$filter_var_option)) {
                            throw new Exception('integerfield value incorrect');
                        }

                        return $value;
                    });

                } catch (Exception $error) {
                    throw new Exception($error);
                }

                return $filter_rule;
            }
        }

        protected static function boolean($rule = [],$value = null,$flag = false) {
            if (empty($flag)) {
                return (object) [
                    'method' => __function__,
                    'rule' => $rule];

            } else {
                try {
                    $filter_rule = self::filterRule($rule,$value,__function__,function($value) {
                        $filter_var_option = [
                            'options' => [
                                'default' => null],
                            'flags' => FILTER_NULL_ON_FAILURE];

                        $filter_var = filter_var($value,FILTER_VALIDATE_BOOLEAN,$filter_var_option);

                        if ($filter_var === null) {
                            throw new Exception('booleanfield value incorrect');
                        }

                        return $value;
                    });

                } catch (Exception $error) {
                    throw new Exception($error);
                }

                return $filter_rule;
            }
        }

        protected static function datetime($rule = [],$value = null,$flag = false) {
            if (empty($flag)) {
                return (object) [
                    'method' => __function__,
                    'rule' => $rule];

            } else {
                try {
                    $filter_rule = self::filterRule($rule,$value,__function__,function($value) {
                        $filter_var_option = [
                            'options' => [
                                'default' => null,
                                'regexp' => '/^([0-9]{4}-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01]))[ ]([01][0-9]|2[0123]):([012345][0-9]):([012345][0-9])$|^((0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-[0-9]{4})[ ]([01][0-9]|2[0123]):([012345][0-9]):([012345][0-9])$/'],
                            'flags' => []];

                        if (!filter_var($value,FILTER_VALIDATE_REGEXP,$filter_var_option)) {
                            throw new Exception('datetimefield value incorrect');
                        }

                        return $value;
                    });

                } catch (Exception $error) {
                    throw new Exception($error);
                }

                return $filter_rule;
            }
        }

        protected static function date($rule = [],$value = null,$flag = false) {
            if (empty($flag)) {
                return (object) [
                    'method' => __function__,
                    'rule' => $rule];

            } else {
                try {
                    $filter_rule = self::filterRule($rule,$value,__function__,function($value) {
                        $filter_var_option = [
                            'options' => [
                                'default' => null,
                                'regexp' => '/^([0-9]{4}-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01]))$|^((0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-[0-9]{4})$/'],
                            'flags' => []];

                        if (!filter_var($value,FILTER_VALIDATE_REGEXP,$filter_var_option)) {
                            throw new Exception('datefield value incorrect');
                        }

                        $value;
                    });

                } catch (Exception $error) {
                    throw new Exception($error);
                }

                return $filter_rule;
            }
        }

        protected static function time($rule = [],$value = null,$flag = false) {
            if (empty($flag)) {
                return (object) [
                    'method' => __function__,
                    'rule' => $rule];

            } else {
                try {
                    $filter_rule = self::filterRule($rule,$value,__function__,function($value) {
                        $filter_var_option = [
                            'options' => [
                                'default' => null,
                                'regexp' => '/^([01][0-9]|2[0123]):([012345][0-9]):([012345][0-9])$/'],
                            'flags' => []];

                        if (!filter_var($value,FILTER_VALIDATE_REGEXP,$filter_var_option)) {
                            throw new Exception('timefield value incorrect');
                        }

                        return $value;
                    });

                } catch (Exception $error) {
                    throw new Exception($error);
                }

                return $filter_rule;
            }
        }

        protected static function float($rule = [],$value = null,$flag = false) {
            if (empty($flag)) {
                return (object) [
                    'method' => __function__,
                    'rule' => $rule];

            } else {
                try {
                    $filter_rule = self::filterRule($rule,$value,__function__,function($value) {
                        $filter_var_option = [
                            'options' => [
                                'default' => null,
                                'decimal' => '.'],
                            'flags' => []];

                        if (!filter_var($value,FILTER_VALIDATE_FLOAT,$filter_var_option)) {
                            throw new Exception('floatfield value incorrect');
                        }

                        return $value;
                    });

                } catch (Exception $error) {
                    throw new Exception($error);
                }

                return $filter_rule;
            }
        }

        protected static function email($rule = [],$value = null,$flag = false) {
            if (empty($flag)) {
                return (object) [
                    'method' => __function__,
                    'rule' => $rule];

            } else {
                try {
                    $filter_rule = self::filterRule($rule,$value,__function__,function($value) {
                        $filter_var_option = [
                            'options' => [
                                'default' => null,],
                            'flags' => []];

                        if (!filter_var($value,FILTER_VALIDATE_EMAIL,$filter_var_option)) {
                            throw new Exception('emailfield value incorrect');
                        }

                        return $value;
                    });

                } catch (Exception $error) {
                    throw new Exception($error);
                }

                return $filter_rule;
            }
        }

        protected static function ip($rule = [],$value = null,$flag = false) {
            if (empty($flag)) {
                return (object) [
                    'method' => __function__,
                    'rule' => $rule];

            } else {
                try {
                    $filter_rule = self::filterRule($rule,$value,__function__,function($value) {
                        $filter_var_option = [
                            'options' => [
                                'default' => null,],
                            'flags' => []];

                        if (!filter_var($value,FILTER_VALIDATE_IP,$filter_var_option)) {
                            throw new Exception('ipfield value incorrect');
                        }

                        return $value;
                    });

                } catch (Exception $error) {
                    throw new Exception($error);
                }

                return $filter_rule;
            }
        }

        protected static function url($rule = [],$value = null,$flag = false) {
            if (empty($flag)) {
                return (object) [
                    'method' => __function__,
                    'rule' => $rule];

            } else {
                try {
                    $filter_rule = self::filterRule($rule,$value,__function__,function($value) {
                        $filter_var_option = [
                            'options' => [
                                'default' => null,],
                            'flags' => []];

                        if (!filter_var($value,FILTER_VALIDATE_URL,$filter_var_option)) {
                            throw new Exception('urlfield value incorrect');
                        }

                        return $value;
                    });

                } catch (Exception $error) {
                    throw new Exception($error);
                }

                return $filter_rule;
            }
        }
    }
}
