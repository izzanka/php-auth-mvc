<?php


use MVC\PHP\Domain\User;
use MVC\PHP\Domain\Session;
use MVC\PHP\Config\Database;
use PHPUnit\Framework\TestCase;
use MVC\PHP\Service\SessionService;
use MVC\PHP\Controller\HomeController;
use MVC\PHP\Repository\UserRepository;
use MVC\PHP\Repository\SessionRepository;


class HomeControllerTest extends TestCase
{
    private HomeController $homeController;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp():void
    {
        $this->homeController = new HomeController();
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testGuest()
    {
        $this->homeController->index();

        $this->expectOutputRegex("[Login Management]");
    }

    public function testUserLogin()
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

        $this->homeController->index();

        $this->expectOutputRegex("[Hello Eko]");
    }

}
