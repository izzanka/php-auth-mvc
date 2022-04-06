<?php 

require_once __DIR__ . "/../vendor/autoload.php";

use MVC\PHP\App\Route;
use MVC\PHP\Config\Database;
use MVC\PHP\Controller\HomeController;
use MVC\PHP\Controller\UserController;
use MVC\PHP\Middleware\MustNotLoginMiddleware;
use MVC\PHP\Middleware\MustLoginMiddleware;

Database::getConnection('production');

Route::add('GET', '/', HomeController::class, 'index');

Route::add('GET', '/users/register', UserController::class, 'register',[MustNotLoginMiddleware::class]);
Route::add('POST', '/users/register', UserController::class, 'postRegister',[MustNotLoginMiddleware::class]);

Route::add('GET', '/users/login', UserController::class, 'login',[MustNotLoginMiddleware::class]);
Route::add('POST', '/users/login', UserController::class, 'postLogin',[MustNotLoginMiddleware::class]);

Route::add('GET', '/users/logout', UserController::class, 'logout',[MustLoginMiddleware::class]);

Route::add('GET', '/users/profile', UserController::class, 'updateProfile',[MustLoginMiddleware::class]);
Route::add('POST', '/users/profile', UserController::class, 'postUpdateProfile',[MustLoginMiddleware::class]);

Route::add('GET', '/users/password', UserController::class, 'updatePassword',[MustLoginMiddleware::class]);
Route::add('POST', '/users/password', UserController::class, 'postUpdatePassword',[MustLoginMiddleware::class]);

Route::run();