<?php
    
	namespace App\Models;
    use Exception;

	abstract class AbstractModel {
		
        // public function __get($prop) {
		// 	// normalizar
		// 	$attr = ucfirst(strtolower($prop));

		// 	// verifica se existe um getter no objeto concreto
		// 	if (method_exists($this,'get'.$attr)) {
		// 		// executa o metodo getAttributo
		// 		return call_user_func([$this,'get'.$attr]);
		// 	}

        //     throw new Exception("Invalid attribute: $prop.");
            
		// }

        // public function __set($prop, $value) {
		// 	// normalize
		// 	$attr = ucfirst(strtolower($prop));

		// 	// verifica se existe um setter no objeto concreto
		// 	if (method_exists($this, 'set'. $attr)) {
		// 		// executa o metodo set_<propriedade>
		// 		call_user_func([$this,'set'.$attr],$value);
		// 	} else {
        //         throw new Exception("Invalid attribute: $prop.");
        //     }

		// }

		// public function __isset($prop) {
        //     return (isset($this->$prop)) ? true : false;
        // }

        public static function getEntity() {
            $class = get_called_class();	        // obtem o nome da class
            return constant("{$class}::TABLENAME");	// retorna a constante de classe TABLENAME
        }

        public function getProperties() {
            $reflection = new \ReflectionObject($this);
            $properties = $reflection->getProperties(\ReflectionProperty::IS_PROTECTED); // maneira para acessar as propriedades do modelo
			
			$fields = [];
			foreach($properties as $prop) {
				$fields[] = $prop->name;
			}

			return $fields;
        }

    }