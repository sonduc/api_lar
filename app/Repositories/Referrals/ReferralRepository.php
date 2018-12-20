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

    public function storeReferralUser($refer_id, $user_id)
    {
        $data = ['user_id' => $user_id,'refer_id' => $refer_id, 'status' => Referral::PENDING];
        return parent::store($data);
    }

    public function getAllReferralUser()
    {
        return $this->model->select('refer_id')->where('status', Referral::PENDING)->get();
    }

    public function updateStatusReferral($uid, $refer_id)
    {
        $this->model->where('user_id', $uid)->where('refer_id', $refer_id)->update([
            "status" => Referral::AWARDED
        ]);
    }
}
