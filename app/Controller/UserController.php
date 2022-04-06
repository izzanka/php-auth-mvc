<?php 

namespace MVC\PHP\Controller;

use MVC\PHP\App\View;
use MVC\PHP\Config\Database;
use MVC\PHP\Exception\ValidationException;
use MVC\PHP\Model\UserLoginRequest;
use MVC\PHP\Model\UserPasswordUpdateRequest;
use MVC\PHP\Model\UserProfileUpdateRequest;
use MVC\PHP\Model\UserRegisterRequest;
use MVC\PHP\Repository\SessionRepository;
use MVC\PHP\Repository\UserRepository;
use MVC\PHP\Service\SessionService;
use MVC\PHP\Service\UserService;

class UserController {

    private UserService $userService;
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $this->userService = new UserService($userRepository);
        
        $sessionRepository = new SessionRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function register()
    {
        View::render('User/register',[
            'title' => 'Register new user'
        ]);
    }

    public function postRegister()
    {
        $request = new UserRegisterRequest();
        $request->id = $_POST['id'];
        $request->name = $_POST['name'];
        $request->password = $_POST['password'];

        try {
            $this->userService->register($request);
            View::redirect('/users/login');
        } catch (ValidationException $exception) {
            View::render('User/register',[
                'title' => 'Register new user',
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function login()
    {
        View::render('/user/login', [
            'title' => 'Login user'
        ]);
    }

    public function postLogin()
    {
        $request = new UserLoginRequest();
        $request->id = $_POST['id'];
        $request->password = $_POST['password'];

        try {
            
            $response = $this->userService->login($request);
            $this->sessionService->create($response->user->id);
            View::redirect('/');

        } catch (ValidationException $exception) {
            View::render('User/login',[
                'title' => 'Login user',
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function logout()
    {
        $this->sessionService->destroy();
        View::redirect('/');
    }

    public function updateProfile()
    {   
        $user = $this->sessionService->current();

        View::render('User/profile',[
            'title' => 'Update user profile',
            'user' => [
                'id' => $user->id,
                'name' => $user->name
            ]
        ]);
    }

    public function postUpdateProfile()
    {
        $user = $this->sessionService->current();

        $request = new UserProfileUpdateRequest();
        $request->id = $user->id;
        $request->name = $_POST['name'];

        try {
            $this->userService->updateProfile($request);
            View::redirect('/');
        } catch (ValidationException $exception) {
            View::render('User/profile',[
                'title' => 'Update user profile',
                'error' => $exception->getMessage(),
                'user' => [
                    'id' => $user->id,
                    'name' => $_POST['name']
                ]
            ]);
        }
    }

    public function updatePassword()
    {
        $user = $this->sessionService->current();

        View::render('/User/password', [
            'title' => 'Update user password',
            'user' => [
                'id' => $user->id
            ]
        ]);
    }

    public function postUpdatePassword()
    {
        $user = $this->sessionService->current();

        $request = new UserPasswordUpdateRequest();
        $request->id = $user->id;
        $request->oldPassword = $_POST['oldPassword'];
        $request->newPassword = $_POST['newPassword'];
        
        try {
            $this->userService->updatePassword($request);
            View::redirect('/');
        } catch (ValidationException $exception) {
            View::render('/User/password',[
                'title' => 'Update user password',
                'error' => $exception->getMessage(),
                'user' => [
                    'id' => $user->id
                ]
            ]);
        }
    }
}

?>