<?php
namespace Application\Restaurant\Model {
    use \Core\Model;
    class Restaurant extends Model {
        public $id;
        public $place;
        public $serves_hot_dogs;
        public $serves_pizza;

        protected function schema() {
            return [
                'id' => Model::integer(),
                'place' => Model::char(['length' => 80]),
                'serves_hot_dogs' => Model::boolean(['null' => false]),
                'serves_pizza' => Model::boolean(),];
        }

        protected function name() {
            return "restaurant";
        }
    }
}
