<?php

namespace App\Http\Transformers;


use App\Http\Transformers\Traits\FilterTrait;
use League\Fractal\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Repositories\Promotions\Promotion;

class PromotionTransformer extends TransformerAbstract
{
   protected $availableIncludes = [
      'coupons',
   ];

   public function transform(Promotion $promotion = null)
   {
      if (is_null($promotion)) {
         return [];
      }

      return [
         'id'                    => $promotion->id,
         'name'                  => $promotion->name,
         'description'           => $promotion->description,
         'date_start'            => $promotion->date_start ? $promotion->date_start : null,
         'date_end'              => $promotion->date_end ? $promotion->date_end : null,
         'status'                => $promotion->status,
         'created_at'            => $promotion->created_at ? $promotion->created_at->format('Y-m-d H:i:s') : null,
         'updated_at'            => $promotion->updated_at ? $promotion->updated_at->format('Y-m-d H:i:s') : null,
      ];
   }

   public function includeCoupons(Promotion $promotion = null, ParamBag $params = null)
   {
      
   }
}
