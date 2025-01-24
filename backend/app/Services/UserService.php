<?php

namespace App\Services;

use App\Models\User;
use App\Services\BaseService;
use App\Repositories\UserRepository;
use App\Repositories\RoleRepository;
use Illuminate\Support\Facades\Hash;
use App\Contracts\Service\UserServiceContract;

/**
 * Class UserService
 *
 * This service handles all business logic related to user management.
 * It includes user creation, updating, deletion, and login operations.
 * It also interacts with the UserRepository for database operations and utilizes the RoleRepository if needed.
 *
 * @package App\Services
 */
class UserService extends BaseService implements UserServiceContract
{
    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * UserService constructor.
     *
     * @param \App\Repositories\UserRepository $repository The repository to be used for user operations.
     */
    public function __construct(UserRepository $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Create a new user.
     *
     * This method creates a new user by hashing the provided password and storing the user in the database.
     *
     * @param array $request The request data for creating the user, including the password.
     * @return \App\Models\User The created user model.
     */
    public function createUser(array $request): User
    {    
        $request['password'] = Hash::make($request['password']);
        return $this->repository->createUser($request);
    }

    /**
     * Update an existing user.
     *
     * This method updates a user’s data with the provided request data, excluding the user ID.
     *
     * @param array $request The request data for updating the user.
     * @return \App\Models\User The updated user model.
     */
    public function updateUser(array $request): User
    {
        $id = $request['id'];
        unset($request['id']);
        return $this->repository->updateUser($id, $request);
    }

    /**
     * Delete a user.
     *
     * This method deletes a user by their ID.
     *
     * @param array $request The request data containing the user ID to be deleted.
     * @return \App\Models\User The deleted user model.
     */
    public function deleteUser(array $request): User
    {    
        return $this->repository->deleteUser($request['id']);
    }

    /**
     * Select a user by their ID.
     *
     * This method retrieves a user by their ID.
     *
     * @param array $request The request data containing the user ID.
     * @return \App\Models\User The user model.
     */
    public function selectUser(array $request): User
    {
        return $this->repository->selectUser($request['id']);
    }

    /**
     * List all users.
     *
     * This method retrieves a paginated list of users.
     *
     * @param array $request The request data, potentially including filters for the user list.
     * @return array The list of users.
     */
    public function listUsers(array $request)
    {    
        return $this->repository->listUsers($request);
    }

    /**
     * Handle user login.
     *
     * This method checks the user's credentials (email and password) and returns the user model if valid.
     *
     * @param array $request The request data containing the user's email and password.
     * @return \App\Models\User The authenticated user model.
     * @throws \Exception Throws an exception if the credentials are invalid.
     */
    public function login(array $request): User
    {    
        $user = $this->repository->selectUserByEmail($request['email']);
      
        if (!$user || !Hash::check($request['password'], $user->password)) {
            throw new \Exception('Invalid credentials', 401);
        }
        return $user;
    }
}
