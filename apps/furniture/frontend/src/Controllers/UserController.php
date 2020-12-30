<?php


namespace IntelCost\Apps\Furniture\Frontend\Controllers;
use Illuminate\Database\Capsule\Manager as Capsule;
use IntelCost\Furniture\Models\Role;
use IntelCost\Furniture\Models\RoleUser;
use IntelCost\Furniture\Models\User;
use IntelCost\Furniture\Models\Database;
use PDO;

class UserController extends BaseController
{
    public function __construct()
    {
    }

    public function index($request)
    {
        $connection = Database::getInstance();

        $database = $connection->getConnection();

        if(!is_null($database)){
            $stmt = $database->query('
            SELECT users.id, users.name, users.email, role_user.id, role_user.user_id, role_user.role_id, roles.key FROM users
            JOIN role_user ON users.id = role_user.user_id
            JOIN roles ON role_user.role_id = roles.id
            GROUP BY role_user.user_id
            ORDER BY role_user.user_id
        ');
            $users = $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_UNIQUE);
        }

        $data = ['users' => $users];

        $this->makeView("list_users", $data);
    }

    public function create($request)
    {
        $data = ['roles' => Role::all()->toArray()];
        $this->makeView("create_new_user", $data);
    }

    public function store($request){
        $user = new User();
        $user->name = $_POST['username'];
        $user->email = $_POST['email'];
        $user->password = $_POST['password'];
        $user->save();

        foreach ($_POST['roles'] as $role_id){
            $roleUser = new RoleUser();
            $roleUser->user_id = $user->id;
            $roleUser->role_id = $role_id;
            $roleUser->save();
        }

        header("Location: http://localhost:8035/users/list");
    }

}