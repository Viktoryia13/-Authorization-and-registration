<?php

class Profile extends Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        $this->loadModel("users"); 

        if ($this->CheckAuth() === false) {
            $this->redirect('users/index');
        }
    }

    public function index()
    {
        $this->view->msg = "Hello " . $_SESSION['name'];
        $this->view->render('profile/index');
    }

    /**
     * удаляем куки и сессию и переходим на главную страницу
     */
    public function logout()
    {
        session_destroy(); //разрушаем сессию для пользователя

        //Удаляем куки авторизации путем установления времени их жизни на текущий момент:
        setcookie('login', '', time(), '/'); //удаляем логин
        setcookie('key', '', time(), '/'); //удаляем ключ

        $this->redirect('users/index');
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
