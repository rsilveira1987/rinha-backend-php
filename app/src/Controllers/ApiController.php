<?php

    namespace App\Controllers;

    use App\Exceptions\InvalidSyntaxException;
    use App\Exceptions\InvalidValueException;
    use App\Models\Pessoa;
    use App\Services\PessoaService;
    use Exception;
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Ramsey\Uuid\Uuid;

    class ApiController {
        
        private $container;

        public function __construct($container) {
            $this->container = $container;
        }

        public function phpinfo(Request $request, Response $response, $args) {

            ob_start();
            phpinfo();
            $output = ob_get_clean();
            
            return $response->getBody()->write($output);
        }

        public function createPessoa(Request $request, Response $response, $args) {

            // # DEBUG
            // $uuid = Uuid::uuid4();
            // return $response->withAddedHeader('Location',"/pessoas/$uuid")->withJson(['201 created'], 201);
            
            $body = $request->getParsedBody();
            
            try {
                $pessoa = PessoaService::save($body);
                $uuid = $pessoa->getUuid();
            } catch (InvalidSyntaxException $e) {
                return $response->withJson(['400 Bad Request'], 400);
            } catch (Exception $e) {
                return $response->withJson(['422 Unprocessable Entity/Content'], 422);
            }

            return $response->withAddedHeader('Location',"/pessoas/$uuid")->withJson(['201 created'], 201);
            
        }

        public function findPessoaByUuid(Request $request, Response $response, $args) {
            
            // # DEBUG
            // $pessoa = new Pessoa;
            // $pessoa->setId(1);
            // $pessoa->setUuid(Uuid::uuid4());
            // $pessoa->setApelido('rsilveira');
            // $pessoa->setNome('Ricardo');
            // $pessoa->setNascimento('1987-04-21');
            // $pessoa->setStack(null);
            // return $response->withJson($pessoa->toJson(), 200);

            $uuid = $args['uuid'];

            $pessoa = PessoaService::findByUuid($uuid);

            if(!$pessoa) {
                return $response->withJson("404 not found", 404);    
            }
            
            return $response->withJson($pessoa->toJson(), 200);
        }

        public function searchPessoasByTerm(Request $request, Response $response, $args) {

            // grab t query param
            $params = $request->getParams();

            if(!isset($params['t'])) {
                return $response->withJson(['400 Bad Request'], 400);
            }

            $criteria = $params['t'];
            if (strlen($criteria) == 0) {
                return $response->withJson([], 200);  
            }

            $pessoas = PessoaService::search($criteria);

            return $response->withJson($pessoas, 200);
        }

        public function countPessoas(Request $request, Response $response, $args) {
            $count = PessoaService::count();
            return $response->withStatus(200)->getBody()->write($count);
        }

    }