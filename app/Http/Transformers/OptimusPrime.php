<?php

namespace App\Http\Transformers;

use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection as EloquentList;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class OptimusPrime
{
    protected $m;

    public function __construct(Manager $manager)
    {
        $this->m = $manager;
        $this->setRecursionLimit();
        $this->parseIncludes();
    }

    /**
     * Lấy param include
     */
    public function parseIncludes()
    {
        if (isset($_GET['include'])) {
            $this->m->parseIncludes($_GET['include']);
        }
    }

    public function transform($entity, $transformer, $include = null)
    {
        if ($include) {
            $this->setIncludes($include);
        }

        if ($entity instanceof AbstractPaginator) {
            $resource = new Collection($entity->getCollection(), $transformer);

            $queryParams = array_diff_key($_GET, array_flip(['page']));
            $entity->appends($queryParams);
            $resource->setPaginator(new IlluminatePaginatorAdapter($entity));
        } else if ($entity instanceof EloquentList) {
            $resource = new Collection($entity, $transformer);
        } else {
            $resource = new Item($entity, $transformer);
        }


        return $this->m->createData($resource)->toArray();
    }

    /**
     * Chỉ định các thành phần include trong Transformer
     *
     * @param $include
     */
    public function setIncludes($include)
    {
        $this->m->parseIncludes($include);
    }

    /**
     * Set the level of embedding includes
     * @author HarikiRito <nxh0809@gmail.com>
     *
     */
    public function setRecursionLimit()
    {
        $this->m->setRecursionLimit(5);
    }
}
