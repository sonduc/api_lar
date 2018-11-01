<?php

namespace App\Http\Transformers\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use League\Fractal\ParamBag;

trait FilterTrait
{
    /**
     * Lọc dữ liệu transform với limit và order
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param        $params
     * @param null   $data
     * @param int    $limit
     * @param string $orderCol
     * @param string $orderBy
     *
     * @return null
     */
    public function limitAndOrder(
        $params, $data = null, $skip = 0, $limit = 25, $orderCol = 'created_at',
        $orderBy = 'desc'
    )
    {
        if ($params->get('page') && is_numeric($params->get('page')[0]) && is_numeric($params->get('page')[1])) {
            list($limit, $skip) = $params->get('page');

        }
        if ($params->get('order')) {
            list($orderCol, $orderBy) = $params->get('order');
        }
        $data = $data->skip($skip)->take($limit)->orderBy($orderCol, $orderBy);
        return $data;
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param ParamBag              $params
     * @param HasMany|BelongsToMany $data
     * @param array                 $columns
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function pagination(ParamBag $params, $data = null, $columns = ['*'])
    {
        $orderCol = 'created_at';
        $orderBy  = 'desc';
        $page     = 1;
        $limit    = 25;
        if ($params->get('page') && is_numeric($params->get('page')[0])) {
            $page = (int)$params->get('page')[0];
        }

        if ($params->get('limit') && is_numeric($params->get('limit')[0])) {
            $limit = (int)$params->get('limit')[0];
        }

        if ($params->get('order')) {
            list($orderCol, $orderBy) = $params->get('order');
        }
        return $data->orderBy($orderCol, $orderBy)->paginate($limit, $columns, 'page', $page);
    }
}
