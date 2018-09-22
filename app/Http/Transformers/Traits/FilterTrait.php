<?php

namespace App\Http\Transformers\Traits;

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
    public function limitAndOrder($params, $data = null, $skip = 0, $limit = 25, $orderCol = 'created_at', $orderBy = 'desc')
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
    
}