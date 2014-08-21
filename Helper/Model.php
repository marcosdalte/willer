<?php

namespace Helper {
	use \Exception as Exception;
	use \DAO\DataManipulationLanguage;

	abstract class Model extends DataManipulationLanguage {
		protected $resource;
		private $flag_null = 0;
		private $flag_blank = 0;

		public function __construct($database = null) {
			$this->resource =& $GLOBALS["PERSIST"];

			if (!empty($database)) {
				if (!array_key_exists($database,$GLOBALS["DATABASE_INFO"])) {
					throw new Exception("database_not_found_in_DATABASE_INFO");
				}

				try {
					$this->resource->databaseUse($database)->connect();

				} catch (Exception $error) {
					throw new Exception($error);
				}
			}

			$resource = $this->resource->getResource();

			if (empty($resource)) {
				try {
					$this->resource->connect();

				} catch (Exception $error) {
					throw new Exception($error);
				}
			}
		}

		protected function column() {
            return get_object_vars($this);
        }

		private function filterRule($rule,$value) {
			if (!empty($rule)) {
				foreach ($rule as $key => $value_) {
					if (!in_array($key,["null","blank","length"])) {
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
								$this->flag_null = 1;

							}
						}

					} else if ($key == "blank") {
						if (!($value_ === 0 || $value_ === 1)) {
							throw new Exception("parameter value incorrect(blank).");

						} else if ($value_ == 0) {
							if (empty($value)) {
								throw new Exception("field can not be empty.");

							}

						} else if ($value == 1) {
							if (empty($value)) {
								$this->flag_blank = 1;

							}
						}

					} else if ($key == "length") {
						if (strlen($value) > $value_) {
							throw new Exception("field length incorrect.");
						}
					}
				}
			}

			return true;
		}

		protected function charField($rule = [],$value = null,$flag = false) {
			if (empty($flag)) {
				return [
					"method" => __function__,
					"rule" => $rule];

			} else {
				try {
					$filter_rule = $this->filterRule($rule,$value);

				} catch (Exception $error) {
					throw new Exception($error);

				}

				if ($this->flag_null === 0 && $this->flag_blank === 0) {
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

		protected function textField($rule = [],$value = null,$flag = false) {
			if (empty($flag)) {
				return [
					"method" => __function__,
					"rule" => $rule];

			} else {
				try {
					$filter_rule = $this->filterRule($rule,$value);

				} catch (Exception $error) {
					throw new Exception($error);

				}

				if ($this->flag_null === 0 && $this->flag_blank === 0) {
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

		protected function integerField($rule = [],$value = null,$flag = false) {
			if (empty($flag)) {
				return [
					"method" => __function__,
					"rule" => $rule];

			} else {
				try {
					$filter_rule = $this->filterRule($rule,$value);

				} catch (Exception $error) {
					throw new Exception($error);

				}

				if ($this->flag_null === 0 && $this->flag_blank === 0) {
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

		protected function booleanField($rule = [],$value = null,$flag = false) {
			if (empty($flag)) {
				return [
					"method" => __function__,
					"rule" => $rule];

			} else {
				try {
					$filter_rule = $this->filterRule($rule,$value);

				} catch (Exception $error) {
					throw new Exception($error);

				}

				if ($this->flag_null === 0 && $this->flag_blank === 0) {
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

		protected function datetimeField($rule = [],$value = null,$flag = false) {
			if (empty($flag)) {
				return [
					"method" => __function__,
					"rule" => $rule];

			} else {
				try {
					$filter_rule = $this->filterRule($rule,$value);

				} catch (Exception $error) {
					throw new Exception($error);

				}

				if ($this->flag_null === 0 && $this->flag_blank === 0) {
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

		protected function dateField($rule = [],$value = null,$flag = false) {
			if (empty($flag)) {
				return [
					"method" => __function__,
					"rule" => $rule];

			} else {
				try {
					$filter_rule = $this->filterRule($rule,$value);

				} catch (Exception $error) {
					throw new Exception($error);

				}

				if ($this->flag_null === 0 && $this->flag_blank === 0) {
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

		protected function timeField($rule = [],$value = null,$flag = false) {
			if (empty($flag)) {
				return [
					"method" => __function__,
					"rule" => $rule];

			} else {
				try {
					$filter_rule = $this->filterRule($rule,$value);

				} catch (Exception $error) {
					throw new Exception($error);

				}

				if ($this->flag_null === 0 && $this->flag_blank === 0) {
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

		protected function floatField($rule = [],$value = null,$flag = false) {
			if (empty($flag)) {
				return [
					"method" => __function__,
					"rule" => $rule];

			} else {
				try {
					$filter_rule = $this->filterRule($rule,$value);

				} catch (Exception $error) {
					throw new Exception($error);

				}

				if ($this->flag_null === 0 && $this->flag_blank === 0) {
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

		protected function emailField($rule = [],$value = null,$flag = false) {
			if (empty($flag)) {
				return [
					"method" => __function__,
					"rule" => $rule];

			} else {
				try {
					$filter_rule = $this->filterRule($rule,$value);

				} catch (Exception $error) {
					throw new Exception($error);

				}

				if ($this->flag_null === 0 && $this->flag_blank === 0) {
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

		protected function ipField($rule = [],$value = null,$flag = false) {
			if (empty($flag)) {
				return [
					"method" => __function__,
					"rule" => $rule];

			} else {
				try {
					$filter_rule = $this->filterRule($rule,$value);

				} catch (Exception $error) {
					throw new Exception($error);

				}

				if ($this->flag_null === 0 && $this->flag_blank === 0) {
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

		protected function urlField($rule = [],$value = null,$flag = false) {
			if (empty($flag)) {
				return [
					"method" => __function__,
					"rule" => $rule];

			} else {
				try {
					$filter_rule = $this->filterRule($rule,$value);

				} catch (Exception $error) {
					throw new Exception($error);

				}

				if ($this->flag_null === 0 && $this->flag_blank === 0) {
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