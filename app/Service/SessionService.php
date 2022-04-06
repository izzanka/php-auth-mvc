<?php 

namespace MVC\PHP\Service;

use MVC\PHP\Domain\User;
use MVC\PHP\Domain\Session;
use MVC\PHP\Repository\SessionRepository;
use MVC\PHP\Repository\UserRepository;

class SessionService
{
    public static string $COOKIE_NAME = 'X-AUTH-SESSION';
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    public function __construct(SessionRepository $sessionRepository, UserRepository $userRepository)
    {
        $this->sessionRepository = $sessionRepository;
        $this->userRepository = $userRepository;
    }
    
    public function create(string $userId): Session
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = $userId;

        $this->sessionRepository->create($session);

        setcookie(self::$COOKIE_NAME, $session->id, time() + (60 * 60 * 24 * 7), "/");

        return $session;
    }

    public function destroy()
    {
        $sessionId = $_COOKIE[self::$COOKIE_NAME] ?? '';

        $this->sessionRepository->deleteById($sessionId);

        setcookie(self::$COOKIE_NAME, '', 1, "/");
    }

    public function current(): ?User
    {
        $sessionId = $_COOKIE[self::$COOKIE_NAME] ?? '';

        $session = $this->sessionRepository->findById($sessionId);

        if($session == null){
            return null;
        }

        return $this->userRepository->findById($session->userId);
    }
}

?>