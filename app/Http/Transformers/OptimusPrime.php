<?php

namespace App\Http\Transformers;

use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Illuminate\Support\Collection as EloquentList;
use Illuminate\Pagination\AbstractPaginator;

class OptimusPrime
{
    protected $m;

    public function __construct(Manager $manager)
    {
        $this->m = $manager;
        $this->parseIncludes();
    }

    public function parseIncludes()
    {
        if (isset($_GET['include'])) {
            $this->m->parseIncludes($_GET['include']);
        }
    }

    public function setIncludes($include)
    {
        $this->m->parseIncludes($include);
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
        } elseif ($entity instanceof EloquentList) {
            $resource = new Collection($entity, $transformer);
        } else {
            $resource = new Item($entity, $transformer);
        }


        return $this->m->createData($resource)->toArray();
    }
}
