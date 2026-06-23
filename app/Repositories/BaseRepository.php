<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

abstract class BaseRepository
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getPaginated($perPage = 15, $filters = [])
    {
        $query = $this->model->query();

        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                $query->where($field, $value);
            }
        }

        return $query->latest()->paginate($perPage);
    }

    public function findById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->model->create($data);
        });
    }

    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $record = $this->findById($id);
            $record->update($data);
            return $record;
        });
    }

    public function delete($id)
    {
        return DB::transaction(function () use ($id) {
            $record = $this->findById($id);
            return $record->delete();
        });
    }

    public function forceDelete($id)
    {
        return DB::transaction(function () use ($id) {
            $record = $this->findById($id);
            return $record->forceDelete();
        });
    }

    public function search($term, $fields = ['name'])
    {
        return $this->model->where(function ($query) use ($term, $fields) {
            foreach ($fields as $field) {
                $query->orWhere($field, 'LIKE', "%{$term}%");
            }
        })->get();
    }

    public function getModel()
    {
        return $this->model;
    }
}