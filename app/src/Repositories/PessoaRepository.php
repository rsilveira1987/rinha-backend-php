<?php

    namespace App\Repositories;

	use App\Database\SQLConnection;
	use App\Models\Pessoa;
	use PDO;

    class PessoaRepository {
        
        public function save(Pessoa $pessoa) {
						
			// data
			$dto = $pessoa->toArray();

			// id is autoincrement
			// unset($data['id']);

			// json data
			$dto['stack'] = ($dto['stack'] != null) ? implode(',',$dto['stack']) : '';
			
			// cria uma instrucao SQL para INSERT
			$colString = implode(', ', array_keys($dto));
			$values = [];
			// $placeholders = [];
			// foreach ($data as $key => $value) {						// old
			// 	$placeholders[] = ":{$key}"; 							// old
			// 	$values[":{$key}"] = $value;							// old
			// }														// old
			// $placeholderString = implode(', ', $placeholders);		// old
				
			foreach ($dto as $key => $value) {
				$values[] = "'{$value}'";
			}	
			$colValues = implode(',',$values);
			// build sql
			$entity = Pessoa::getEntity();
			// $sql = "INSERT INTO {$entity} ( {$colString} ) VALUES ( {$placeholderString} )";	// old
			$sql = "INSERT INTO {$entity} ( {$colString} ) VALUES ( $colValues )";
						
			// obtem a conexao
			$conn = SQLConnection::open(CONFIG_DIR . '/db.ini');
			// $stmt = $conn->prepare($sql);		// old
			// $ret = $stmt->execute($values);		// old
			// $stmt->closeCursor();				// old
			$conn->exec($sql);

			/* close connection */
			$conn = null;
									
			return $pessoa;	
		}

		public function searchByCriteria($criteria) {
			// build sql
            $entity = Pessoa::getEntity(); # . "_view";
			$sql = "SELECT * FROM {$entity} WHERE nome LIKE '%{$criteria}%' OR apelido LIKE '%{$criteria}%' OR stack LIKE '%{$criteria}%' LIMIT 50";

			// obtem a transacao ativa
			$conn = SQLConnection::open(CONFIG_DIR . '/db.ini');
			// $stmt = $conn->prepare($sql);
			// $ret = $stmt->execute([
			// 	':criteria' => "%$criteria%"
			// ]);
			// $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

			$stmt = $conn->query($sql, PDO::FETCH_ASSOC);
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			$pessoas = array_map(function($data) {
				$data['stack'] = ($data['stack'] == "") ? [] : explode(',',$data['stack']);
				$pessoa = Pessoa::fromArray($data);
				return $pessoa->toArray();
			},$rows);

			/* close connection */
			// $stmt->closeCursor();
			$conn = null;
			
			return $pessoas;

		}

        public function findByUuid($uuid) {
            return $this->findById($uuid);
        }

		public function findById($id) {
            // build sql
            $entity = Pessoa::getEntity(); # . "_view";
			//$sql = "SELECT * FROM {$entity} WHERE id = :id LIMIT 1";
			$sql = "SELECT * FROM {$entity} WHERE id = {$id} LIMIT 1";

			// obtem a conexao
			$conn = SQLConnection::open(CONFIG_DIR . '/db.ini');
			
			// $stmt = $conn->prepare($sql);
			// $ret = $stmt->execute([
			// 	':id' => $id
			// ]);
			// $data = $stmt->fetch(PDO::FETCH_ASSOC);
			$data = $conn->query($sql, PDO::FETCH_ASSOC);
			$data['stack'] = explode(',',$data['stack']);
			
			/* close connection */
			// $stmt->closeCursor();
			$conn = null;

			return Pessoa::fromArray($data);
			
        }

        public function count() {
			// build sql
            $entity = Pessoa::getEntity();
			
			$sql = "SELECT count(id) as count FROM {$entity}";

			// obtem a conexao
			$conn = SQLConnection::open(CONFIG_DIR . '/db.ini');
			
			$stmt = $conn->query($sql, PDO::FETCH_ASSOC);
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			return $data['count'];

        }
        
    }