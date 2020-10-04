<?php
class Users extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->loadModel("users"); // подключение класса модели users
        session_start();
        //Проверка авторизации, если успешно, то редирект на страницу пользователя
        if ($this->CheckAuth() === true) {
            $this->redirect("profile/index");
        }
    }

    public function index()
    {
        $this->view->render('user/index');
    }

    public function Signup()
    {
        $user = new Users_Model();
        $user->login = $_POST['login'];
        $user->password = $_POST['password'];
        $user->confirm_password = $_POST['confirm_password'];
        $user->email = $_POST['email'];
        $user->name = $_POST['name'];

        $registration = $user->signUp();

        if ($registration === true) {
            $this->responseJson([
                'success' => true,
                'message' => 'Спасибо за регистрацию, теперь вы можете авторизоваться',
            ]);
        } else {
            $this->responseJson($registration);
        }
        $this->view->render('user/index');
    }


    public function SignIn()
    {
        $login = $_POST['login'];
        $password = $_POST['password'];

        $user = new Users_Model();
        $authorization = $user->signIn($login, $password);

        //Вывод ошибок
        if ($authorization !== true) {
            $this->responseJson($authorization);
        }
        //поиск пользователя по логину в БД
        $userObj = $user->searchByLogin($login);
        $id = $userObj->user_id;

        $_SESSION['auth'] = 'true';
        $_SESSION['id'] = (string)$userObj->login;
        $_SESSION['login'] = (string)$userObj->user_id;
        $_SESSION['name'] = (string)$userObj->name;

        //Проверяем, что была нажата галочка 'Запомнить меня'
        if (isset($_POST['remember'])) {
            $key = $user::generateRandomSalt(); //случайная строка
            setcookie('login', (string)$userObj->login, time() + 60 * 60 * 24 * 30, '/'); //логин
            setcookie('key', $key, time() + 60 * 60 * 24 * 30, '/'); //случайная строка   
            $user->addCookie($key, $login);
        }

    

        $this->responseJson([
            'success' => true,
        ]);
    }


    public static function responseJson($param = [])
    {
        header('Content-type: application/json');
        echo json_encode($param);
        exit;
    }

    /**
     * Проверка авторизации пользователя
     * @return bool
     */
    public function CheckAuth()
    {
        if (isset($_SESSION['auth']) && $_SESSION['auth']) {
            return true;
        }
        if (empty($_SESSION['auth']) or $_SESSION['auth'] == false) {
            if (!empty($_COOKIE['login']) and !empty($_COOKIE['key'])) {
                $login = $_COOKIE['login'];
                $key = $_COOKIE['key'];

                $user = new Users_Model();
                $userObj = $user->searchByLogin($login);

                $result = $user->checkCookieKey($login, $key);
                if ($result === true) {
                    $_SESSION['auth'] = true;
                    $_SESSION['id'] = (string)$userObj->user_id;
                    $_SESSION['login'] = (string)$userObj->login;
                    $_SESSION['name'] = (string)$userObj->name;
                    return true;
                }
                return false;
            }
            return false;
        }
    }
}
