<?php

namespace App\Repositories\Languages;

use App\Repositories\BaseRepository;

class LanguageRepository extends BaseRepository
{
    /**
     * Role model.
     * @var Model
     */
    protected $model;

    /**
     * RoleRepository constructor.
     * @param Role $role
     */
    public function __construct(Role $role)
    {
        $this->model = $role;
    }

    public function getBySlug($slug)
    {
        return $this->model->where('slug', $slug)->first();
    }
}
