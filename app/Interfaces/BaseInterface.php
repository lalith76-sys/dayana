<?php

namespace App\Interfaces;

interface BaseInterface
{
    public function getAll();
    public function getPaginated($perPage = 15, $filters = []);
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function search($term, $fields = ['name']);
}