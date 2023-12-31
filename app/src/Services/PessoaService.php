<?php

    namespace App\Services;

    use App\Database\SQLTransaction;
    use App\Models\Pessoa;
    use App\Repositories\PessoaRepository;
    use Ramsey\Uuid\Uuid;
    use Exception;

    class PessoaService {

        public static function save($data) {
            
            $repository = new PessoaRepository;
            $uuid = Uuid::uuid4();

            $pessoa = Pessoa::fromArray($data);
            $pessoa->setId($uuid->toString());
            
            $pessoa = $repository->save($pessoa);

            if(!$pessoa) {
                throw new Exception("contains");
            }
            
            // SQLTransaction::open(CONFIG_DIR . '/db.ini');    
            // $pessoa = $repository->save($pessoa);
            // SQLTransaction::close();
            
            return $pessoa;
            
        }

        public static function findById($uuid) {
            
            $repository = new PessoaRepository;

            // SQLTransaction::open(CONFIG_DIR . '/db.ini');    
            // $pessoa = $repository->findByUuid($uuid);
            // SQLTransaction::close();

            $pessoa = $repository->findById($uuid);            

            return $pessoa;
        }

        public static function search($criteria) {
            $repository = new PessoaRepository;

            // SQLTransaction::open(CONFIG_DIR . '/db.ini');    
            // $pessoas = $repository->searchByCriteria($criteria);
            // SQLTransaction::close();

            $pessoas = $repository->searchByCriteria($criteria);            

            return $pessoas;
        }

        public static function count() {
            
            $repository = new PessoaRepository;

            // SQLTransaction::open(CONFIG_DIR . '/db.ini');    
            // $count = $repository->count();
            // SQLTransaction::close();

            $count = $repository->count();            

            return $count;
        }

    }
