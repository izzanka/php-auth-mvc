<?php 

namespace MVC\PHP\Controller;

use MVC\PHP\App\View;
use MVC\PHP\Config\Database;
use MVC\PHP\Repository\SessionRepository;
use MVC\PHP\Repository\UserRepository;
use MVC\PHP\Service\SessionService;

class HomeController
{
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $sessionRepository = new SessionRepository($connection);
        $userRepository = new UserRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    function index()
    {
        $user = $this->sessionService->current();

        if($user == null){
            View::render('Home/index', [
                'title' => 'auth mvc php'
            ]);
        }else{
            View::render('Home/dashboard', [
                'title' => 'Dashboard',
                'user' => [
                    'name' => $user->name
                ]
            ]);
        }
        
    }

}

?>