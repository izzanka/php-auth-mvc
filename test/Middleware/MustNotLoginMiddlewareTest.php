<?php

namespace MVC\PHP\Middleware {

    use MVC\PHP\Domain\User;
    use MVC\PHP\Domain\Session;
    use MVC\PHP\Service\SessionService;
    use MVC\PHP\Config\Database;
    use MVC\PHP\Repository\SessionRepository;
    use MVC\PHP\Repository\UserRepository;

    require_once __DIR__ . '/../Helper/helper.php';

    use PHPUnit\Framework\TestCase;

    class MustNotLoginMiddlewareTest extends TestCase
    {

        private MustNotLoginMiddleware $middleware;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;

        protected function setUp():void
        {
            $this->middleware = new MustNotLoginMiddleware();
            putenv("mode=test");

            $this->userRepository = new UserRepository(Database::getConnection());
            $this->sessionRepository = new SessionRepository(Database::getConnection());

            $this->sessionRepository->deleteAll();
            $this->userRepository->deleteAll();
        }

        public function testBeforeGuest()
        {
            $this->middleware->before();
            $this->expectOutputString("");
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
            $this->expectOutputRegex("[Location: /]");

        }

    }
}


