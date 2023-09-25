<?php

    namespace App\Services;

    use App\Database\SQLTransaction;
    use App\Models\Pessoa;
    use App\Repositories\PessoaRepository;
use Exception;
use Ramsey\Uuid\Uuid;

    class PessoaService {

        public static function save($data) {
            
            $repository = new PessoaRepository;
            $uuid = Uuid::uuid4();

            $pessoa = Pessoa::fromArray($data);
            $pessoa->setUuid($uuid->toString());

            // if($repository->contains($pessoa)) {
            //     throw new Exception("contains");
            // }
            
            $pessoa = $repository->save($pessoa);
            
            // SQLTransaction::open(CONFIG_DIR . '/db.ini');    
            // $pessoa = $repository->save($pessoa);
            // SQLTransaction::close();
            
            return $pessoa;
            
        }

        public static function findByUuid($uuid) {
            
            $repository = new PessoaRepository;

            // SQLTransaction::open(CONFIG_DIR . '/db.ini');    
            // $pessoa = $repository->findByUuid($uuid);
            // SQLTransaction::close();

            $pessoa = $repository->findByUuid($uuid);            

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
