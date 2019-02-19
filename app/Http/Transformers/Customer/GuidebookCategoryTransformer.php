<?php

namespace App\Http\Transformers\Customer;

use App\Http\Transformers\Traits\FilterTrait;
use App\Repositories\GuidebookCategories\GuidebookCategory;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;

class GuidebookCategoryTransformer extends TransformerAbstract
{
    use FilterTrait;
    protected $availableIncludes = [
        'places',
    ];

    public function transform(GuidebookCategory $guidebookcategory = null)
    {
        if (is_null($guidebookcategory)) {
            return [];
        }

        return [
            'id'         => $guidebookcategory->id,
            'name'       => $guidebookcategory->name,
            'icon'       => $guidebookcategory->icon,
            'lang'       => $guidebookcategory->lang,
        ];
    }

    public function includePlaces(GuidebookCategory $guidebookcategory = null, ParamBag $params = null)
    {
        if (is_null($guidebookcategory)) {
            return $this->null();
        }

        $data = $this->pagination($params, $guidebookcategory->places());

        return $this->collection($data, new PlaceTransformer);
    }
}
