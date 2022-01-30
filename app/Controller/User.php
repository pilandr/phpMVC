<?php
namespace App\Controller;

use App\Model\User as UserModel;
use Base\AbstractController;

class User extends AbstractController
{
    public function index(): string
    {
        if ($this->getUser()) {
            $this->redirect('/blog/index');
        }
        return $this->view->render('User/register.phtml');
    }

    public function login(): string
    {
        $password = ($_POST['password']);
        $email = ($_POST['email']);

        if ($password && $email) {
            $user = UserModel::getByEmail($email);
            if (!$user) {
                $this->view->assign('error', 'Неверный логин и пароль');
            }

            if ($user) {
                if ($user->getPassword() != UserModel::getPasswordHash($password)) {
                    $this->view->assign('error', 'Неверный логин и пароль');
                } else {
                    $_SESSION['id'] = $user->getId();
                    $this->redirect('/blog/index');
                }
            }
        } else {
            $this->view->assign('error', 'Заполните все поля');
        }

        return $this->view->render('User/register.phtml');
    }

    public function register(): string
    {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $password2 = trim($_POST['password2']);

        $success = true;
        if (isset($_POST['email'])) {

            if (!$name) {
                $this->view->assign('error', 'Имя не может быть пустым');
                $success = false;
            }

            if (!$password || !$password2) {
                $this->view->assign('error', 'Пароль не может быть пустым');
                $success = false;
            } elseif ($password !== $password2) {
                $this->view->assign('error', 'Пароли не совпадают');
                $success = false;
            } elseif (strlen($password) < 4) {
                $this->view->assign('error', 'Длина пароля меньше 4 символов');
                $success = false;
            }

            if (!$email) {
                $this->view->assign('error', 'Email не может быть пустым');
                $success = false;
            }


            $user = UserModel::getByEmail($email);
            if ($user) {
                $this->view->assign('error', 'Пользователь с таким email уже существует');
                $success = false;
            }

            if ($success) {
                $user = (new UserModel())
                    ->setName($name)
                    ->setPassword(UserModel::getPasswordHash($password))
                    ->setEmail($email);

                $user->save();

                $_SESSION['id'] = $user->getId();
                $this->setUser($user);

                $this->redirect('/blog/index');
            }
        }

//        return $this->view->render('User/register.phtml', [
//            'user' => UserModel::getById((int) $_GET['id'])
//        ]);
        return $this->view->render('User/register.phtml');
    }

    public function profile(): string
    {
        return $this->view->render('User/profile.phtml', [
            'user' => UserModel::getById((int) $_GET['id'])
        ]);

    }

    public function logout(): void
    {
        session_destroy();

        $this->redirect('/');

    }
}