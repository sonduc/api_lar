<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;
use App\Repositories\GuidebookCategories\GuidebookCategory;

class GuidebookCategoryTransformer extends TransformerAbstract
{
    protected $availableIncludes = [

    ];

    public function transform(GuidebookCategory $guidebookcategory = null)
    {
        if (is_null($guidebookcategory)) {
            return [];
        }

        return [
            'id'    => $guidebookcategory->id,
        ];
    }

}
