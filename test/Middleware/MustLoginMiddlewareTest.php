<?php

namespace MVC\PHP\Middleware {

use MVC\PHP\Domain\User;
use MVC\PHP\Domain\Session;
use MVC\PHP\Config\Database;
use MVC\PHP\Service\SessionService;
use MVC\PHP\Repository\UserRepository;
use MVC\PHP\Repository\SessionRepository;
use MVC\PHP\Middleware\MustLoginMiddleware;

    require_once __DIR__ . '/../Helper/helper.php';

    use PHPUnit\Framework\TestCase;

    class MustLoginMiddlewareTest extends TestCase
    {

        private MustLoginMiddleware $middleware;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;

        protected function setUp():void
        {
            $this->middleware = new MustLoginMiddleware();
            putenv("mode=local");

            $this->userRepository = new UserRepository(Database::getConnection());
            $this->sessionRepository = new SessionRepository(Database::getConnection());

            $this->sessionRepository->deleteAll();
            $this->userRepository->deleteAll();
        }

        public function testBeforeGuest()
        {
            $this->middleware->before();
            $this->expectOutputRegex("[Location: /users/login]");
        }

        public function testBeforeLoginUser()
        {
            $user = new User();
            $user->id = "eko";
            $user->name = "Eko";
            $user->password = "rahasia";
            $this->userRepository->create($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->create($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->middleware->before();
            $this->expectOutputString("");
        }

    }
}


