<?php

namespace App\Repositories\Referrals;

use App\Repositories\BaseRepository;

class ReferralRepository extends BaseRepository implements ReferralRepositoryInterface
{
    /**
     * Referral model.
     * @var Model
     */
    protected $model;

    /**
     * ReferralRepository constructor.
     * @param Referral $referral
     */
    public function __construct(Referral $referral)
    {
        $this->model = $referral;
    }

    public function storeReferralUser($refer_id, $user_id, $ref_type)
    {
        $data = ['user_id' => $user_id,'refer_id' => $refer_id, 'status' => Referral::PENDING, 'type' => $ref_type];
        return parent::store($data);
    }

    public function getAllReferralUser($user_id = null, $ref_id = null, $ref_type = null)
    {
        $query = $this->model->where('status', Referral::PENDING);
        if ($ref_type != null) {
            $query = $query->where('type', $ref_type);
        }
        if ($user_id != null) {
            return $query->where('user_id', $user_id)->pluck('refer_id');
        }
        if ($ref_id != null) {
            return $query->where('refer_id', $ref_id)->get()->toArray();
        }
        return $query->pluck('refer_id');
    }

    public function updateStatusReferral($uid, $refer_id, $ref_type = null)
    {
        $this->model->where('user_id', $uid)->where('refer_id', $refer_id)->where('type', $ref_type)->update([
            "status" => Referral::AWARDED
        ]);
    }
}
