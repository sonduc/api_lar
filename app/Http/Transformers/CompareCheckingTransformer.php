<?php

namespace App\Http\Transformers;

use App\Repositories\CompareCheckings\CompareChecking;
use League\Fractal\TransformerAbstract;
use App\Http\Transformers\Traits\FilterTrait;
use League\Fractal\ParamBag;
use Carbon\Carbon;

class CompareCheckingTransformer extends TransformerAbstract
{
    use FilterTrait;

    protected $availableIncludes
        = [
            'user'
        ];

    /**
     *
     * @param CompareChecking $compare
     *
     * @return array
     */
    public function transform(CompareChecking $compare = null)
    {
        if (is_null($compare)) {
            return [];
        }

        return [
            'id'                     => $compare->id,
            'date'                   => $compare->date ? Carbon::parse($compare->date)->toDateString() : null,
            'total_debit'            => $compare->total_debit,
            'total_credit'           => $compare->total_credit,
            'total_bonus'            => $compare->total_bonus,
            'total_compare_checking' => $compare->total_compare_checking,
            'status'                 => $compare->status,
            'status_txt'             => $compare->getCompareCheckingStatus(),
            'created_at'             => $compare->created_at ? $compare->created_at->format('Y-m-d H:i:s') : null,
            'updated_at'             => $compare->updated_at ? $compare->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }

    /**
     *
     * @author Tuan Anh <tuananhpham1402@gmail.com>
     *
     * @param CompareChecking $compare
     *
     * @return \League\Fractal\Resource\Item|\League\Fractal\Resource\NullResource
     */
    public function includeUser(CompareChecking $compare)
    {
        if (is_null($compare)) {
            return $this->null();
        }

        return $this->item($compare->user, new UserTransformer);
    }
}
