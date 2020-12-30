<?php


ini_set('display_errors', 1);
ini_set('display_starup_error', 1);
error_reporting(E_ALL);

require_once '../../../../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use IntelCost\Furniture\Models\Role;
use Laminas\Diactoros\ServerRequestFactory;

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'intel-cost-mysql',
   // 'port'      => '3367',
    'database'  => 'furniture',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

//Make this Capsule instance available globally.
$capsule->setAsGlobal();

// Setup the Eloquent ORM.
$capsule->bootEloquent();
$capsule->bootEloquent();

if (empty(Capsule::schema()->hasTable('users'))) {
    Capsule::schema()->create('users', function ($table) {
        $table->increments('id');
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password');
        $table->timestamps();
    });
}

if (empty(Capsule::schema()->hasTable('roles'))){
    Capsule::schema()->create('roles', function ($table) {
        $table->increments('id');
        $table->string('name');
        $table->string('key')->unique();
        $table->timestamps();
    });

    $roles = [
        ['key' => 'admin', 'name' => 'Admin'],
        ['key' => 'client', 'name' => 'Client'],
        ['key' => 'employee', 'name' => 'Employee'],
    ];

    foreach ($roles as $role){
        $roleMdel = new Role();
        $roleMdel->key = $role['key'];
        $roleMdel->name = $role['name'];
        $roleMdel->save();
    }
}

if (empty(Capsule::schema()->hasTable('role_user'))){
    Capsule::schema()->create('role_user', function($table)
    {
        $table->increments('id');
        $table->integer('user_id')->unsigned();
        $table->integer('role_id')->unsigned()->nullable();
        $table->timestamps();

        $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        $table->foreign('role_id')->references('id')->on('roles')->onUpdate('cascade')->onDelete('cascade');
    });
}

if (!empty(Capsule::schema()->hasTable('users')) && !empty(Capsule::schema()->hasTable('roles')) && !empty(Capsule::schema()->hasTable('role_user'))){

    $user = Capsule::table('users')
        ->where('email', '=', 'admin@example.com')
        ->first();

    if (empty($user->email)){
        $userModel = new \IntelCost\Furniture\Models\User();
        $userModel->name = "Administrador";
        $userModel->email = "admin@example.com";
        $userModel->password = '123456789';
        $userModel->save();

        $role = Capsule::table('roles')
            ->where('key', '=', 'admin')
            ->first();

        $roleUser = new \IntelCost\Furniture\Models\RoleUser();
        $roleUser->user_id = $userModel->id;
        $roleUser->role_id = $role->id;
        $roleUser->save();
    }
}


$request = ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

// create the router container and get the routing map
$routerContainer = new Aura\Router\RouterContainer();
$map = $routerContainer->getMap();

$map->get('blog', '/', [
    'controller' => 'IntelCost\Apps\Furniture\Frontend\Controllers\IndexController',
    'action' => 'index'
]);

$map->get('blog.create', '/create', [
    'controller' => 'IntelCost\Apps\Furniture\Frontend\Controllers\IndexController',
    'action' => 'create'
]);

$map->get('blog.id', '/show/{id}', [
    'controller' => 'IntelCost\Apps\Furniture\Frontend\Controllers\IndexController',
    'action' => 'show'
]);

$map->get('blog.home', '/home', [
    'controller' => 'IntelCost\Apps\Furniture\Frontend\Controllers\IndexController',
    'action' => 'home'
]);

$map->post('blog.login', '/login', [
    'controller' => 'IntelCost\Apps\Furniture\Frontend\Controllers\IndexController',
    'action' => 'login'
]);

$map->get('blog.logout', '/logout', [
    'controller' => 'IntelCost\Apps\Furniture\Frontend\Controllers\IndexController',
    'action' => 'logout'
]);


$map->get('blog.list.user', '/users/list', [
    'controller' => 'IntelCost\Apps\Furniture\Frontend\Controllers\UserController',
    'action' => 'index'
]);


$map->get('blog.create.user', '/create/user', [
    'controller' => 'IntelCost\Apps\Furniture\Frontend\Controllers\UserController',
    'action' => 'create'
]);

$map->post('blog.store.user', '/store/user', [
    'controller' => 'IntelCost\Apps\Furniture\Frontend\Controllers\UserController',
    'action' => 'store'
]);




// get the route matcher from the container ...
$matcher = $routerContainer->getMatcher();

// .. and try to match the request to a route.
$route = $matcher->match($request);

if(!$route){
    echo 'Route not Found';
    exit;
}else{

    // add route attributes to the request
    foreach ($route->attributes as $key => $val) {
        echo "{$key} => {$val}";
        $request = $request->withAttribute($key, $val);
    }

    $handlerData = $route->handler;
    $controllerName = $handlerData['controller'];
    $actionName = $handlerData['action'];

    $controller = new $controllerName;
    $response = $controller->$actionName($request);

    /*foreach($response->getHeaders()as $name => $values)
    {
        foreach($values as $value)
        {
            header(sprintf('%s: %s' , $name , $value), false);
        }

    }
    http_response_code($response->getStatusCode());
    echo $response->getBody();*/
}

