<?php

namespace App\Services;

use App\Interfaces\BaseInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

abstract class BaseService
{
    protected $repository;

    public function __construct(BaseInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->getAll();
    }

    public function getPaginated($perPage = 15, $filters = [])
    {
        return $this->repository->getPaginated($perPage, $filters);
    }

    public function findById($id)
    {
        return $this->repository->findById($id);
    }

    public function create(array $data)
    {
        try {
            DB::beginTransaction();
            $result = $this->repository->create($data);
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Create failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function update($id, array $data)
    {
        try {
            DB::beginTransaction();
            $result = $this->repository->update($id, $data);
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $result = $this->repository->delete($id);
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function search($term, $fields = ['name'])
    {
        return $this->repository->search($term, $fields);
    }
}