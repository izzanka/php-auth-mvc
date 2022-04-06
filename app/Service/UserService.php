<?php 

namespace MVC\PHP\Service;

use MVC\PHP\Domain\User;
use MVC\PHP\Config\Database;
use MVC\PHP\Model\UserRegisterRequest;
use MVC\PHP\Repository\userRepository;
use MVC\PHP\Model\UserRegisterResponse;
use MVC\PHP\Exception\ValidationException;
use MVC\PHP\Model\UserLoginRequest;
use MVC\PHP\Model\UserLoginResponse;
use MVC\PHP\Model\UserPasswordUpdateRequest;
use MVC\PHP\Model\UserPasswordUpdateResponse;
use MVC\PHP\Model\UserProfileUpdateRequest;
use MVC\PHP\Model\UserProfileUpdateResponse;

class UserService {

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function validateUserRegisterRequest(UserRegisterRequest $request)
    {
        if($request->id == null || $request->name == null || $request->password == null || 
            trim($request->id) == "" || trim($request->name) == "" || trim($request->password) == ""){
                throw new ValidationException("Id, Name and Password can not be null");
        }
    }

    public function register(UserRegisterRequest $request): UserRegisterResponse
    {
        $this->validateUserRegisterRequest($request);

        try
        {
            Database::beginTransaction();

            $user = $this->userRepository->findById($request->id);
            if($user != null){
                throw new ValidationException("User already exists");
            }
    
            $user = new User();
            $user->id = $request->id;
            $user->name = $request->name;
            $user->password = password_hash($request->password, PASSWORD_BCRYPT);
            $this->userRepository->create($user);
    
            $response = new UserRegisterResponse();
            $response->user = $user;

            Database::commitTransaction();
            return $response;

        }catch(\Exception $exception){

            Database::rollbackTransaction();
            throw $exception;
            
        }
    }

    public function validateUserLoginRequest(UserLoginRequest $request)
    {
        if($request->id == null || $request->password == null || trim($request->id) == "" || trim($request->password) == "")
        {
            throw new ValidationException("Id, Name and Password can not be null");
        }
    }

    public function login(UserLoginRequest $request): UserLoginResponse
    {
        $this->validateUserLoginRequest($request);

        $user = $this->userRepository->findById($request->id);
        if($user == null){
            throw new ValidationException("id or password is wrong");
        }

        if(password_verify($request->password, $user->password)){
            $response = new UserLoginResponse();
            $response->user = $user;
            return $response;
        }else{
            throw new ValidationException("id or password is wrong");
        }        
    }

    private function validateUserProfileUpdateRequest(UserProfileUpdateRequest $request)
    {
        if($request->id == null || $request->name == null || trim($request->id) == "" || trim($request->name) == "")
        {
            throw new ValidationException("Id, Name can not be null");
        }
    }

    public function updateProfile(UserProfileUpdateRequest $request): UserProfileUpdateResponse
    {
        $this->validateUserProfileUpdateRequest($request);
        try {
            Database::beginTransaction();

            $user = $this->userRepository->findById($request->id);

            if($user == null){
                throw new ValidationException("User not found");
            }

            $user->name = $request->name;

            $this->userRepository->create($user);

            Database::commitTransaction();

            $response = new UserProfileUpdateResponse();
            $response->user = $user;
            return $response;

        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;

        }
    }

    private function validateUserPasswordUpdateRequest(UserPasswordUpdateRequest $request)
    {
        if($request->id == null || $request->oldPassword == null || $request->newPassword == null || 
            trim($request->id) == "" || trim($request->oldPassword) == "" || trim($request->newPassword) == ""){
                throw new ValidationException("Id, Old Password and New Password can not be null");
        }
    }


    public function updatePassword(UserPasswordUpdateRequest $request): UserPasswordUpdateResponse
    {
        $this->validateUserPasswordUpdateRequest($request);

        try {
            Database::beginTransaction();

            $user = $this->userRepository->findById($request->id);
            if($user == null){
                throw new ValidationException("User not found");
            }

            if(!password_verify($request->oldPassword, $user->password)){
                throw new ValidationException("Old password is wrong");
            }

            $user->password = password_hash($request->newPassword, PASSWORD_BCRYPT);
            $this->userRepository->update($user);

            Database::commitTransaction();

            $response = new UserPasswordUpdateResponse();
            $response->user = $user;
            return $response;
            
        } catch (\Exception $exception) {
           Database::rollbackTransaction();
           throw $exception;
        }
    }
}

?>