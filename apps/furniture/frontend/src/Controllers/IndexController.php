<?php


namespace IntelCost\Apps\Furniture\Frontend\Controllers;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\JsonResponse;
use IntelCost\Furniture\services\UserSession;
use Illuminate\Database\Capsule\Manager as Capsule;

class IndexController extends BaseController
{

    public function __construct()
    {
        if (UserSession::getCurrenEmailUser() == null){
            UserSession::init();
        }
    }

    public function index()
    {
        $data = ['title' => 'Formulario Login', 'id' => 10];
        $this->makeView("login", $data);
    }

    public function login()
    {
        if (UserSession::getCurrenEmailUser()) {
            header("Location: http://localhost:8035/home");
        }

        $user = Capsule::table('users')
            ->where('email', '=', $_POST['email'])
            ->where('password', '=', $_POST['password'])
            ->first();

        if (!isset($user->email)) {
            $data = ['errors' => ['Datos incorrectos al intentar inisiar sesion']];
            $this->makeView("login", $data);
        }else{
            UserSession::setCurrentEmailUser($user->email);
            header("Location: http://localhost:8035/home");
        }
    }

    public function create($request)
    {
        echo "Entro al create";
    }

    public function show($request)
    {
        /*header('Content-Type: application/json');
        echo json_encode($data);*/

        $id = $request->getAttribute('id');
        echo $id;
        $response = new Response();
        $response->getBody()->write("You asked for blog entry {$id}.");
        return $response;
    }

    public function home($request)
    {
        if (UserSession::getCurrenEmailUser())
        {
            $data = [];
            $this->makeView("home", $data);
        }else{
            $data = ['errors' => ['Debe iniciar sesion'], 'email' => UserSession::getCurrenEmailUser()];
            $this->makeView("login", $data);
        }
    }

    public function logout()
    {
        if (!UserSession::getCurrenEmailUser()){
            echo "Error al cerrar la session, verifique que su usuario tenga sesion activa";
            exit();
        }
        UserSession::close();
        header("Location: http://localhost:8035/");
    }
}