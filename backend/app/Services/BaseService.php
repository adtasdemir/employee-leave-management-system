<?php

namespace App\Services;

use App\Contracts\Service\BaseServiceContract;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseService
 *
 * This is an abstract service class that serves as a base for all services.
 * It provides common methods for handling CRUD operations like retrieving, 
 * creating, updating, and deleting models. This class uses a repository 
 * to perform database operations and implements the BaseServiceContract.
 *
 * @package App\Services
 */
abstract class BaseService implements BaseServiceContract
{
    /**
     * The repository instance associated with the service.
     *
     * @var \App\Repositories\BaseRepository
     */
    public BaseRepository $repository;

    /**
     * BaseService constructor.
     *
     * @param \App\Repositories\BaseRepository $repository The repository to be used by the service.
     */
    public function __construct(BaseRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all models from the repository.
     *
     * This method retrieves all records from the model associated with the repository.
     *
     * @return \Illuminate\Database\Eloquent\Collection A collection of models.
     */
    public function all(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Get a model by its ID.
     *
     * This method retrieves a single model by its primary key (ID).
     *
     * @param int $id The ID of the model to retrieve.
     * @return \Illuminate\Database\Eloquent\Model|null The model instance or null if not found.
     */
    public function find(int $id): ?Model
    {
        return $this->repository->find($id);
    }

    /**
     * Retrieve a model by its primary key or throw an exception if not found.
     *
     * This method retrieves a model by its primary key (ID), or throws a 
     * `ModelNotFoundException` if the model is not found.
     *
     * @param int $id The ID of the model to retrieve.
     * @return \Illuminate\Database\Eloquent\Model|null The model instance if found.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the model is not found.
     */
    public function findOrFail(int $id): ?Model
    {
        return $this->repository->findOrFail($id);
    }

    /**
     * Create a new model in the repository.
     *
     * This method creates a new model instance using the provided attributes and saves it to the database.
     *
     * @param array $attributes The attributes to create the new model.
     * @return \Illuminate\Database\Eloquent\Model The created model instance.
     */
    public function create(array $attributes): Model
    {
        return $this->repository->create($attributes);
    }

    /**
     * Update an existing model with the given attributes.
     *
     * This method updates the specified model with the given attributes and returns a boolean indicating
     * whether the update was successful.
     *
     * @param int $id The ID of the model to update.
     * @param array $attributes The attributes to update the model with.
     * @return bool Returns true if the update was successful, false otherwise.
     */
    public function update(int $id, array $attributes): bool
    {
        return $this->repository->update($id, $attributes);
    }

    /**
     * Delete a model by its ID.
     *
     * This method deletes a model by its primary key (ID).
     *
     * @param int $id The ID of the model to delete.
     * @return void
     */
    public function delete(int $id): void
    {
        $this->repository->delete($id);
    }

    /**
     * Paginate the model results.
     *
     * This method retrieves a paginated list of models from the repository.
     *
     * @param int $perPage The number of records to return per page.
     * @return mixed The paginated result.
     */
    public function paginate(int $perPage): mixed
    {
        return $this->repository->paginate($perPage);
    }
}
