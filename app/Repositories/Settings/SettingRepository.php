<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/16/2019
 * Time: 5:24 PM
 */

namespace App\Repositories\Settings;

use App\Repositories\BaseRepository;

class SettingRepository extends BaseRepository implements SettingRepositoryInterface
{
    /**
     * @var Blog
     */
    protected $model;

    /**
     * BlogRepository constructor.
     *
     * @param Blog $blog
     */
    public function __construct(
        Setting $setting
    ) {
        $this->model         = $setting;
    }


    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param array $data
     * @return \App\Repositories\Eloquent
     */
    public function store($data = [])
    {
//        $result= parent::getAll()->toArray();
//
//        if (!empty($result))
//        {
//            throw new \Exception('Bạn phải xóa cài đặt trước đó thì mới có kiểu tao mới');
//        }


        if (!empty($data)) {
            $data['homepage_image']  = rand_name();
            $data['image_logo']      = rand_name();
            $data['contact_email']   = isset($data['contact_email']) ? json_encode($data['contact_email']) : '';
            $data['contact_hotline'] = isset($data['contact_hotline']) ? json_encode($data['contact_hotline']) : '';
            $data['bank_account']    = isset($data['bank_account']) ? json_encode($data['bank_account']): '';
            $data['meta_keywords']   = isset($data['meta_keywords']) ? $data['meta_keywords'] : '';
        }

        return parent::store($data);
    }

    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param array $data
     * @return \App\Repositories\Eloquent
     */
    public function updateSettings($id, $data = [], $excepts = [], $only = [])
    {
        if (!empty($data)) {
            $data['homepage_image'] = rand_name();
            $data['image_logo']    = rand_name();
            if (isset($data['contact_email'])) {
                $data['contact_email'] = json_encode($data['contact_email']);
            }

            if (isset($data['contact_hotline'])) {
                $data['contact_hotline'] = json_encode($data['contact_hotline']);
            }

            if (isset($data['bank_account'])) {
                $data['bank_account'] = json_encode($data['bank_account']);
            }

            if (isset($data['meta_keywords'])) {
                $data['meta_keywords'] = json_encode($data['meta_keywords']);
            }
        }
        return parent::update($id, $data, $excepts, $only); // TODO: Change the autogenerated stub
    }
}
