<?php

    namespace App\Models;

use App\Exceptions\InvalidSyntaxException;
use App\Exceptions\InvalidValueException;
use App\Utils\Validator;
use Exception;

    class Pessoa extends AbstractModel
    {
        const TABLENAME = 'tb_pessoas';

        // Declarar os campos do banco de dados
        protected $id = null;
        protected $apelido = null;
        protected $nome = null;
        protected $nascimento = null;
        protected $stack = [];

        public function getId() {
            return $this->id;
        }

        public function getApelido() {
            return $this->apelido;
        }

        public function getNome() {
            return $this->nome;
        }

        public function getNascimento() {
            return $this->nascimento;
        }

        public function getStack() {
            if(empty($this->stack)) {
                return null;
            }
            return $this->stack;
        }

        public function setId($value) {
            $this->id = $value;
        }

        public function setApelido($value) {
            
            if(Validator::isNull($value)) {
                throw new InvalidValueException("apelido");
            }

            if(strlen($value) > 32) {
                throw new InvalidValueException("apelido");
            }

            $this->apelido = $value;
        }

        public function setNome($value) {
            
            if(Validator::isNull($value)) {
                throw new InvalidValueException("nome");
            }

            if (!Validator::isString($value)) {
                throw new InvalidSyntaxException("nome");
            }

            if(strlen($value) > 100) {
                throw new InvalidValueException("nome");
            }

            $this->nome = $value;
        }

        public function setNascimento($value) {
            if (!Validator::isDateTime($value)) {
                throw new InvalidValueException("nascimento");
            }
            $this->nascimento = $value;
        }

        public function addSkill($value) {
            if (!Validator::isString($value)) {
                throw new InvalidSyntaxException("stack item");
            }
            $this->stack[] = $value;
        }

        public function setStack($value) {
            if ($value == null) {
                $this->stack = [];
            } else {
                $this->stack = explode(',',$value);
            }
        }

        public function toJson() {
            return json_encode([
                'id' => $this->getId(),
                'apelido' => $this->getApelido(),
                'nome' => $this->getNome(),
                'nascimento' => $this->getNascimento(),
                'stack' => $this->getStack()
            ]);
        }

        public function toArray() {
            return [
                'id' => $this->getId(),
                'apelido' => $this->getApelido(),
                'nome' => $this->getNome(),
                'nascimento' => $this->getNascimento(),
                'stack' => $this->getStack()
            ];
        }

        public static function fromArray($data) {
            $pessoa = new Pessoa;
            $pessoa->setId($data['id'] ?? null);
            $pessoa->setApelido($data['apelido'] ?? null);
            $pessoa->setNome($data['nome'] ?? null);
            $pessoa->setNascimento($data['nascimento'] ?? null);
            $skills = $data['stack'] ?? [];
            foreach($skills as $skill) {
                $pessoa->addSkill($skill);
            }
            
            return $pessoa;
        }
    }