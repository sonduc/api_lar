<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/28/2019
 * Time: 3:21 PM
 */

namespace App\Http\Transformers;


use App\Repositories\HostReviews\HostReview;
use App\Http\Transformers\Traits\FilterTrait;

class HostReviewTranformer
{
    use FilterTrait;
    protected $availableIncludes = [

    ];

    public function transform(HostReview $hostReview = null)
    {
        if (is_null($hostReview)) {
            return [];
        }

        return [
            'id'                    => $hostReview->id,
            'room_id'               => $hostReview->room_id,
            'booking_id'            => $hostReview->booking_id,
            'customer_id'           => $hostReview->customer_id,
            'merchant_id'           => $hostReview->merchant_id,
            'status'                => $hostReview->status,
            'avg_rating'            => $hostReview->avg_rating,
            'cleanliness'           => $hostReview->cleanliness,
            'friendly'              => $hostReview->friendly,
            'comment'               => $hostReview->comment,
            'recommend'             => $hostReview->recommend,
            'house_rules_observe'   => $hostReview->house_rules_observe,
            'checkin'               => $hostReview->checkin,
            'checkout'              => $hostReview->checkout,
            'created_at'            => $hostReview->created_at ? $hostReview->created_at->format('Y-m-d H:i:s') : null,
            'updated_at'            => $hostReview->updated_at ? $hostReview->updated_at->format('Y-m-d H:i:s') : null
        ];
    }

}