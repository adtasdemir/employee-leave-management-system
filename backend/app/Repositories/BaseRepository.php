<?php

namespace App\Repositories;

use App\Contracts\Repository\BaseRepositoryContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseRepository
 *
 * This is a base repository class that provides basic CRUD operations and pagination functionality
 * for interacting with Eloquent models. It implements the BaseRepositoryContract.
 *
 * @package App\Repositories
 */
abstract class BaseRepository implements BaseRepositoryContract
{
    protected Model $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get a new query builder instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(): Builder
    {
        return $this->model::query();
    }

    /**
     * Get all the models from the database.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * Get a model by its ID.
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

   
    /**
     * Retrieve a model by its primary key or throw an exception if not found.
     *
     * @param int $id The primary key of the model to retrieve.
     * @return \Illuminate\Database\Eloquent\Model|null The model instance if found.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no model is found for the given ID.
     */
    public function findOrFail(int $id): ?Model
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Create a new model instance in the database.
     *
     * @param array $attributes The attributes to create the model with.
     * @return \Illuminate\Database\Eloquent\Model The model instance.
     */
    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    /**
     * Update the model with the given attributes.
     *
     * @param int $id The ID of the model to update.
     * @param array $attributes An array of attributes to update the model with.
     * @return bool Returns true if the update was successful, false otherwise.
     */
    public function update(int $id, array $attributes): bool
    {
        return (bool) $this->model->where('id', $id)->update($attributes);
    }

    /**
     * Delete the model with the given ID.
     *
     * @param int $id The ID of the model to delete.
     * @return void
     */
    public function delete(int $id): void
    {
        $this->model->destroy($id);
    }

    /**
     * Paginate the model and return a length-aware paginator instance.
     *
     * @param int $perPage The number of records to return per page.
     * @return mixed A length-aware paginator instance.
     */
    public function paginate(int $perPage = 10): mixed
    {
        return $this->model->paginate($perPage);
    }
}