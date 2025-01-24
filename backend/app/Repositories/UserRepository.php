<?php

namespace App\Repositories;

use App\Models\User;
use App\Enums\UserStatus;
use App\Contracts\Repository\UserRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


/**
 * Class UserRepository
 *
 * This repository handles the operations related to User models.
 * It extends the BaseRepository to leverage common repository methods and implements the UserRepositoryContract.
 * It includes methods to create, delete, update, and list users, as well as select users by their ID or email.
 *
 * @package App\Repositories
 */
class UserRepository extends BaseRepository implements UserRepositoryContract
{
    /**
     * UserRepository constructor.
     *
     * @param User $model The User model instance to be used by the repository.
     */

    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Create a new User
     *
     * @param string $name
     *
     * @return \App\Models\User
     */
    public function createUser(array $data): User
    {
        return $this->create($data);
    }


    /**
     * Delete a User
     *
     * @param int $id
     *
     * @return bool
     */
    public function deleteUser(int $id): User
    {
        $User = $this->query()->find($id);
        $User->delete();
        return $User;
    }

    
    /**
     * List Users
     *
     * Retrieve a paginated list of all registered users from the database,
     * ordered in descending order by ID.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listUsers() :LengthAwarePaginator
    {
        return $this->query()->orderBy('id', 'desc')->paginate();
    }
 
    /**
     * Select a User by id
     * 
     * @param int $id
     * 
     * @return \App\Models\User
     */
    public function selectUser(int $id): User
    {
        return $this->query()->find($id);
    }

    /**
     * Select a User by Email
     * 
     * @param int $id
     * 
     * @return \App\Models\User
     */
    public function selectUserByEmail(string $email): User
    {
        $user = $this->query()->where('email', $email)
        ->where('status', UserStatus::ACTIVE->value)->first();
        if (!$user) {
            throw new \Exception('unauthorized', 404);
        }
        return $user;
    }
    
    /**
     * Update a User
     * 
     * @param int $id
     * @param string $name
     * 
     * @return \App\Models\User
     */
    public function updateUser(int $id, array $data)
    {
        $User = $this->query()->find($id);
        $User->update($data);
        return $User;
    }
  
}