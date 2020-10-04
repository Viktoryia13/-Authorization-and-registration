<?php
class Users_Model extends Model
{
  public $dbUsers;

  public $id;
  public $login;
  public $email;
  public $password;
  public $confirm_password;
  public $name;

  public function __construct()
  {
    parent::__construct();
    $this->dbUsers = Database::getTable("users");
  }

  // Регистрация пользователя
  public function signUp()
  {
    // инъекция 
    $login = trim(htmlspecialchars(strip_tags($this->login)));
    $name = trim(htmlspecialchars(strip_tags($this->name)));
    $email = trim(htmlspecialchars(strip_tags($this->email)));
    $password = trim(htmlspecialchars(strip_tags($this->password)));
    $confirm_password = trim(htmlspecialchars(strip_tags($this->confirm_password)));

    $errors = $this->validateSignUp($login, $password, $confirm_password, $email, $name);

    if ($errors !== true) {
      // если есть ошибки валидации, возвращаем массив ошибок
      return $errors;
    }
    $id = $this->generateUserId();
    $newUser = $this->dbUsers->addChild('User');
    $newUser->addChild('login', $login);
    $newUser->addChild('email', $email);
    $newUser->addChild('name', $name);

    $salt = $this->generateRandomSalt();
    $newUser->addChild('password', $this->generatePasswordWithSalt($password, $salt));
    $newUser->addChild('salt', $salt);
    $newUser->addChild('user_id', $id);

    // сохраняем нового пользователя в таблицу users
    Database::saveTable($this->dbUsers, "users");
    return true;
  }
  /**
   * Авторизация пользователя, возвращает массив с ошибками ко всем полям, при успешной авторизации возвращает true
   * @param string $login
   * @param string $password
   * @return array|bool
   */
  public function signIn($login, $password)
  {
    $login = trim(htmlspecialchars(strip_tags($login)));
    $password = trim(htmlspecialchars(strip_tags($password)));

    //проверка на ошибки
    $errors = $this->validateSignIn($login, $password);
    if ($errors !== true) {
      // если есть ошибки валидации, возвращаем массив ошибок
      return $errors;
    }

    return true;
  }

  /**
   * Добавление key cookie пользователя в бд, при отсутствии юзера с таким логинон в бд возвращает false
   * @param string $login
   * @param string $key
   * @return bool
   */
  public function addCookie($key, $login)
  {
    $user = $this->SearchByLogin($login);
    if ($user == false) {
      return false;
    }
    $xml = $user->xpath('//key');

    if ($user->xpath('//key')) {
      $xml[0][0] = $key;
    } else {
      $user->addChild('key', $key);
    }
    Database::saveTable($this->dbUsers, "users");
    return true;
  }

  /**
   * Проверка key Cookie пользователя с базой данных
   * @param string $login
   * @param string $key
   * @return bool
   */
  public function checkCookieKey($login, $key)
  {
    $user = $this->SearchByLogin($login);
    if ($user == false) {
      return false;
    }
    if ($user->key == $key) {
      return true;
    }
    return false;
  }

  /**
   * Проверка введенного пароля с пароле в БД
   * @param string $password
   * @param object $user
   * @return bool
   */
  public function checkPassword($password, $user)
  {
    $password_with_salt = $this->generatePasswordWithSalt($password, $user->salt);
    if ($user->password == $password_with_salt) {
      return true;
    }
    return false;
  }

  /**
   * Валидация всех данных при регистрации, возвращает массив с ошибками ко всем полям, при успешной валидации возвращает true
   * @param string $login
   * @param string $password
   * @param string $confirm_password
   * @param string $email
   * @param string $name
   * @return array
   */
  public function validateSignUp($login, $password, $confirm_password, $email, $name)
  {
    $error = false;  // наличие ошибок
    $errors = [];

    if (($error_message = $this->validateLogin($login)) !== true) {
      $error = true;
      $errors['login'] = $error_message;
    }
    if (($error_message = $this->validatePassword($password)) !== true) {
      $error = true;
      $errors['password'] = $error_message;
    }
    if (($error_message = $this->ConfirmPassword($password, $confirm_password)) !== true) {
      $error = true;
      $errors['confirm_password'] = $error_message;
    }
    if (($error_message = $this->validateEmail($email)) !== true) {
      $error = true;
      $errors['email'] = $error_message;
    }
    if (($error_message = $this->validateName($name)) !== true) {
      $error = true;
      $errors['name'] = $error_message;
    }
    if ($error === true) {
      return $errors;
    }
    return true;
  }

  /**
   * Валидация всех данных при авторизации, возвращает массив с ошибками ко всем полям, при успешной валидации возвращает true
   * @param string $login
   * @param string $password
   * @return array
   */
  public function validateSignIn($login, $password)
  {

    $user = $this->SearchByLogin($login);

    if ($user === false) {
      $errors['login'] = "Пользователь не найден";
      return $errors;
    }

    if ($this->CheckPassword($password, $user) === false) {

      $errors['password'] = "Неверный пароль";
      return $errors;
    }
    return true;
  }


  /**
   * Валидация логина и проверка на уникальность, возвращает true или ошибку
   * @param string $password
   * @return bool|string
   */
  public function validateLogin($login)
  {
    if (preg_match('/^[a-zA-Z0-9]{6,}$/', $login) == 0) {
      return "Логин должен состоять из минимум 6 символов , только буквы и цифры";
    } else {  //Проверка на уникальность
      if ($this->searchByLogin($login) !== false) {
        return "Пользователь с таким логином уже существует";
      } else {
        return true;
      }
    }
  }

  /**
   * Валидация пароля, возвращает true или ошибку
   * @param string $password
   * @return bool|string
   */
  public function validatePassword($password)
  {
    if (preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/', $password) == 0) {
      return "Пароль (минимум 6 символов) обязательно должен содержать цифру, буквы в разных регистрах и спец символ (знаки)";
    }
    return true;
  }
  /**
   * Валидация name, возвращает true или ошибку
   * @param string $name
   * @return bool|string
   */
  public function validateName($name)
  {
    if (preg_match('/^[a-zA-Z0-9]{2}$/', $name) == 0) {
      return "Имя должно состоять из 2 символов , только буквы и цифры";
    } else {
      return true;
    }
  }

  /**
   * Валидация email и проверка на уникальность, возвращает true или ошибку
   * @param string $email
   * @return bool|string
   */
  public function validateEmail($email)
  {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

      if ($this->searchEmail($email) !== false) {
        return "Пользователь с таким email уже существует";
      } else {
        return true;
      }
    } else {
      return "Email указан не правильно.";
    }
  }

  /**
   * Сравниваем пароль и подтверждение пароля, возвращает true или ошибку
   * @param string $password
   * @param string $confirm_password
   * @return bool|string
   */
  public function confirmPassword($password, $confirm_password)
  {
    if (strlen($confirm_password) == 0) {
      return 'Введите подтверждение пароля';
    }
    if ($password !== $confirm_password) {
      return 'Пароль и подтверждение не совпадают';
    }

    return true;
  }


  /**
   * ищет в бд Users по email если есть, то возвращает объект пользователя, если нет возвращает false
   * @param string $email
   * @return bool|object
   */
  public function searchEmail($email)
  {
    foreach ($this->dbUsers as $value) {

      if (trim($email) == trim($value->email)) {
        return $value;
      }
    }
    return false;
  }

  /**
   * ищет в бд Users по login если есть, то возвращает объект пользователя, если нет возвращает false
   * @param string $login
   * @return object|bool|
   */
  public function SearchByLogin($login)
  {
    foreach ($this->dbUsers as $value) {
      if (trim($login) == trim($value->login)) {
        return $value;
      }
    }
    return false;
  }

  public static function generateRandomSalt($length = 16)
  {
    return bin2hex(random_bytes($length));
  }

  public static function generatePasswordWithSalt($password, $salt)
  {
    return sha1($salt . $password);
  }

  public function generateUserID()
  {
    $result = (array)$this->dbUsers->xpath('//User');
    $count = count($result);
    if ($count > 0) {

      $id = ((array)$result[$count - 1])['user_id'];
      $id++;
      return (string)$id;
    }
    $id = "1";
    return $id;
  }
}
