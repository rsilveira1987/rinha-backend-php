<?php

// bootstrap
require '/var/www/html/bootstrap.php';

use App\Exceptions\InvalidSyntaxException;
use App\Services\PessoaService;
use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;

use Slim\App;

$host = getenv('HOST');
$hostname = getenv('HOSTNAME');
$port = getenv('PORT');

$server = new Server($host, $port);

// a swoole server is evented just like express
$server->on('start', function (Server $server) use ($hostname, $port) {
    echo sprintf('Swoole http server is started at http://%s:%s' . PHP_EOL, $hostname, $port);
});

// handle all requests with this response
$server->on('request', function (Request $req, Response $res) {
    
    // populate the global state with the request info
    $_SERVER['REQUEST_URI'] = $req->server['request_uri'];
    $_SERVER['REQUEST_METHOD'] = $req->server['request_method'];
    $_SERVER['REMOTE_ADDR'] = $req->server['remote_addr'];

    $_GET = $req->get ?? [];
    $_POST = $req->post ?? $req->rawContent();
    $_FILES = $req->files ?? [];

    //---------------------------------------------------
    // 1. Create Slim App
    //---------------------------------------------------
    $configuration = [
        'settings' => [
            'displayErrorDetails' => true,
        ],
    ];
    $c = new \Slim\Container($configuration);

    // initialize slim app
    // each request should create a new App()
    $app = new \Slim\App($c);

    //----------------------------------------------------
    // 2. Configure Slim App
    //----------------------------------------------------
    // Slim container 
    // $container = $app->getContainer();
    
    // $container['ApiController'] = function($container) {
    //     return new \App\Controllers\ApiController($container);
    // };

    //---------------------------------------------------------------------------------------
    // 3. Routes
    //---------------------------------------------------------------------------------------  

    # GET /
    $app->get( '/', function($request, $response, $args) {        
        return $response->withJson(['Rinha Backend 2023' => 'PHP'], 200);
    });

    # POST /pessoas
    $app->post('/pessoas', function ($request, $response, $args) use($req){
        
        $body = $req->post ?? $req->rawContent();
        $data = json_decode($body,1);
        
        if( !is_array($data) ){
            // we have a problem with the body
            return $response->withJson(['400 Bad Request'], 400);
        }

        // Cache client
        $cache = new Predis\Client([
            'scheme' => 'tcp',
            'host'   => 'cache',
            'port'   => 6379,
        ]);

        // Check cache
        $blacklist = $cache->get('blacklist');
        $blacklist = explode(',',$blacklist);

        if(in_array($data['nome'], $blacklist) || in_array($data['apelido'], $blacklist)) {
            return $response->withJson(['422 Unprocessable Entity/Content'], 422);
        }
        
        try {
            
            $pessoa = PessoaService::save($data);
            $id = $pessoa->getId();

            # Add to cache
            $blacklist[] = $pessoa->getNome();
            $blacklist[] = $pessoa->getApelido();
            $cache->set('blacklist', implode(',',$blacklist));

            // Old cache...            
            // hashmap
            // $cache->set($id, $pessoa->toJson());

        } catch (InvalidSyntaxException $e) {
            return $response->withJson(['400 Bad Request'], 400);
        } catch (Exception $e) {
            return $response->withJson(['422 Unprocessable Entity/Content'], 422);
        }

        return $response->withAddedHeader('Location',"/pessoas/$id")->withJson(['201 created'], 201);

    });

    # GET /pessoas/aea4f053-0bbb-45f1-a46d-50ec8a7b6eb9
    $app->get('/pessoas/{uuid}', function ($request, $response, $args) {
        // grab uuid
        $uuid = $args['uuid'];
        
        $pessoa = PessoaService::findById($uuid);
        if(!$pessoa) {
            return $response->withJson("404 not found", 404);    
        }
        return $response->withJson($pessoa->toArray(), 200);

        // //
        // // Cache client
        // //
        // $cache = new Predis\Client([
        //     'scheme' => 'tcp',
        //     'host'   => 'cache',
        //     'port'   => 6379,
        // ]);

        // $pessoaJson = $cache->get($uuid);

        // if($pessoaJson == null) {
        //     return $response->withJson("404 not found", 404);
        // }

        // $pessoa = Pessoa::fromArray(json_decode($pessoaJson,1));
        // return $response->withJson($pessoa->toArray(), 200);        
    });

    # GET /pessoas?t=<termo>
    $app->get('/pessoas', function ($request, $response, $args) use($req) {
        // grab t param
        $params = $req->get ?? [];
        
        if(!isset($params['t'])) {
            return $response->withJson(['400 Bad Request'], 400);
        }

        $criteria = $params['t'];
        if (strlen($criteria) == 0) {
            return $response->withJson([], 200);  
        }

        $pessoas = PessoaService::search($criteria);

        return $response->withJson($pessoas, 200);
    });

    # GET /contagem-pessoas
    $app->get( '/contagem-pessoas', function( $request, $response, $args) {
        $count = PessoaService::count();
        return $response->withStatus(200)->getBody()->write($count);
    });

    //-----------------------------------------------------------------------
    // 4. Run app
    //-----------------------------------------------------------------------
    // suppress output by passing "true"
    $slim = $app->run(true);

    // transfer the Slim headers to the Swoole app
    foreach ($slim->getHeaders() as $key => $value) {
        // content length is set when calling "end"
        if ($key !== 'Content-Length') {
            $res->header($key, $value[0]);
        }
    }

    // slim status code
    $res->status($slim->getStatusCode());

    // write the output
    $res->end($slim->getBody());

    // // example of a JSON response
    // $app->get('/type/json', function ($request, $response, $args) {
    //     return $response->withJson([
    //         'status' => 'ok',
    //         'message' => 'hey!'
    //     ]);
    // });

    // // normal text/html response
    // $app->get('/[{name}]', function ($request, $response, $args) {
    //     $name = $args['name'] ?? 'world!';

    //     return $response
    //         ->getBody()
    //         ->write(sprintf('<p>Hello, %s</p>', $name));
    // });

    // // figure out if we are running in HTTPS
    // $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443);

    // // just add a make pretend cookie here
    // $res->cookie(explode('.', 'docker.local')[0], '1', strtotime('+1 day'), '/', getenv('HOSTNAME'), $secure , true);

    // // write the output
    // $res->end($slim->getBody());
});

$server->start();