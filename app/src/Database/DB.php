<?php

    namespace App\Database;

    use Exception;
    use PDO;

    class DB {

        public static function insertRow($sql, $values = []) {
			      			
            // obtem a transacao ativa
			if ($conn = SQLTransaction::getInstance()) {
			
				$stmt = $conn->prepare($sql);

				// bindings
				foreach ($values as $key => $value) {
					
                    if (is_int($value)) {
						$pdoParam = PDO::PARAM_INT;
					} elseif (is_resource($value)) {
						$pdoParam = PDO::PARAM_LOB;
					} elseif (is_bool($value)) {
						$pdoParam = PDO::PARAM_BOOL;
					} else {
						$pdoParam = PDO::PARAM_STR;
					}

					$stmt->bindValue(
						is_string($key) ? $key : $key + 1,
						$value,
						$pdoParam
					);
				}

				// $ret = $stmt->execute($values);
				$ret = $stmt->execute();

			} else {
				throw new Exception('There is no active transaction');
			}

        }
    }