<?php 

namespace MVC\PHP\Middleware;

use MVC\PHP\App\View;
use MVC\PHP\Config\Database;
use MVC\PHP\Middleware\Middleware;
use MVC\PHP\Service\SessionService;
use MVC\PHP\Repository\UserRepository;
use MVC\PHP\Repository\SessionRepository;

class MustNotLoginMiddleware implements Middleware
{
    private SessionService $sessionService;

    public function __construct()
    {
       $sessionRepository = new SessionRepository(Database::getConnection());
       $userRepository = new UserRepository(Database::getConnection());
       $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function before(): void
    {
        $user = $this->sessionService->current();
        if($user != null){
            View::redirect('/');
        }
    }
}

?>