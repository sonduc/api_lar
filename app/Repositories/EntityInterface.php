<?php

namespace App\Repositories;

interface EntityInterface
{
    public function getAll();
    public function getByQuery($params = [], $size = 25);
    public function getById($id);
    public function getByIdInTrash($id);
    public function store($data);
    public function storeArray($datas);
    public function update($id, $data, $excepts = [], $only = []);
    public function delete($id);
    public function destroy($id);
    public function restore($id);
}
