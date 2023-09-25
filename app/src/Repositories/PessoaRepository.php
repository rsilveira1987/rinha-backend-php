<?php

    namespace App\Repositories;

	use App\Database\SQLConnection;
	use App\Models\Pessoa;
	use PDO;

    class PessoaRepository {
        
        public function save(Pessoa $pessoa) {
            
            // data
			$data = $pessoa->toArray();

			// id is autoincrement
			unset($data['id']);

			// json data
			$data['stack'] = json_encode($data['stack']);

			// cria uma instrucao SQL para INSERT
			$colString = implode(', ', array_keys($data));
			$placeholders = [];
			$values = [];
			foreach ($data as $key => $value) {
				$placeholders[] = ":{$key}";
				$values[":{$key}"] = $value;
			}
			$placeholderString = implode(', ', $placeholders);

			// build sql
            $entity = Pessoa::getEntity();
			$sql = "INSERT INTO {$entity} ( {$colString} ) VALUES ( {$placeholderString} )";

			$conn = SQLConnection::open(CONFIG_DIR . '/db.ini');
			$stmt = $conn->prepare($sql);
			$ret = $stmt->execute($values);
			/* close connection */
			$stmt->closeCursor();
			$conn = null;

			return $pessoa;
			
			// // obtem a transacao ativa
			// if ($conn = SQLTransaction::getInstance()) {
			// 	$stmt = $conn->prepare($sql);
			// 	$ret = $stmt->execute($values);
			// 	// retorna o objeto
            //     // $id = $conn->lastInsertId();
			// 	// return $this->find($id);
            //     return $pessoa;
			// } else {
			// 	throw new Exception('Nao existe transacao ativa');
			// }
        }

		public function searchByCriteria($criteria) {
			// build sql
            $entity = Pessoa::getEntity();
			$sql = "SELECT * FROM {$entity} WHERE apelido LIKE :criteria OR (stack)::TEXT LIKE :criteria LIMIT 50";

			// obtem a transacao ativa
			$conn = SQLConnection::open(CONFIG_DIR . '/db.ini');
			$stmt = $conn->prepare($sql);
			$ret = $stmt->execute([
				':criteria' => "%$criteria%"
			]);
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			$pessoas = array_map(function($data) {
				$data['stack'] = json_decode($data['stack']);
				$pessoa = Pessoa::fromArray($data);
				return $pessoa->toJson();
			},$rows);

			/* close connection */
			$stmt->closeCursor();
			$conn = null;
			
			return $pessoas;

		}

        public function findByUuid($uuid) {
            // build sql
            $entity = Pessoa::getEntity();
			$sql = "SELECT * FROM {$entity} WHERE uuid = :uuid";

			// obtem a conexao
			$conn = SQLConnection::open(CONFIG_DIR . '/db.ini');
			
			$stmt = $conn->prepare($sql);
			$ret = $stmt->execute([
				':uuid' => $uuid
			]);
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			$data['stack'] = json_decode($data['stack']);
			
			/* close connection */
			$stmt->closeCursor();
			$conn = null;

			return Pessoa::fromArray($data);
			
        }

        public function count() {
			// build sql
            $entity = Pessoa::getEntity();
			
			$sql = "SELECT count(id) FROM {$entity}";

			// obtem a conexao
			$conn = SQLConnection::open(CONFIG_DIR . '/db.ini');

			$stmt = $conn->prepare($sql);
			$ret = $stmt->execute();
			$data = $stmt->fetch(PDO::FETCH_ASSOC);

			/* close connection */
			$stmt->closeCursor();
			$conn = null;

			return $data['count'];

        }
        
    }