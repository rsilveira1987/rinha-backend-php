<?php

    // bootstrap
    require '../bootstrap.php';

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
    $app = new \Slim\App($c);

    //----------------------------------------------------
    // 2. Configure Slim App
    //----------------------------------------------------
    // Slim container 
    $container = $app->getContainer();
    
    $container['ApiController'] = function($container) {
        return new \App\Controllers\ApiController($container);
    };

    //---------------------------------------------------------------------------------------
    // 3. Routes
    //---------------------------------------------------------------------------------------  
    // public routes
    $app->get( '/', 'ApiController:phpinfo');
    $app->post('/pessoas', 'ApiController:createPessoa')->setName('pessoas.create')->add( new \App\Middlewares\ApplicationJsonMiddleware($container) );
    $app->get( '/pessoas/{uuid}', 'ApiController:findPessoaByUuid')->setName('pessoas.retrieve');
    $app->get( '/pessoas', 'ApiController:searchPessoasByTerm')->setName('pessoas.search');
    $app->get( '/contagem-pessoas', 'ApiController:countPessoas')->setName('pessoas.count');
    
    //-----------------------------------------------------------------------
    // 4. Run app
    //-----------------------------------------------------------------------
    $app->run();

    //-----------------------------------------------------------------------
    // 5. Destroy ghost session
    //-----------------------------------------------------------------------
    // Destroy ghost session
    // if(!$container->auth->getCurrentUser() && !\App\Utils\Session::get('slimFlash')) {
    //     \App\Utils\Session::destroy();
    // }
    

    
   