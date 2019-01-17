<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/17/2019
 * Time: 3:43 AM
 */

namespace App\Http\Transformers;


use App\Repositories\Settings\Setting;
use App\Http\Transformers\Traits\FilterTrait;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;

class SettingTransformers extends TransformerAbstract
{

    use FilterTrait;
    protected $availableIncludes = [

    ];

    public function transform(Setting $setting = null)
    {
        if (is_null($setting)) {
            return [];
        }

        return [
            'id'                => $setting->id,
            'name'              => $setting->name,
            'address'           => $setting->address,
            'homepage_image'    => $setting->homepage_image,
            'bank_account'      => $setting->bank_account,
            'image_logo'        => $setting->image_logo,
            'description'       => $setting->description,
            'status'            => $setting->status,
            'contact_hotline'   => json_decode($setting->contact_hotline),
            'contact_email'     => json_decode($setting->contact_email),
            'created_at'        => $setting->created_at ? $setting->created_at->format('Y-m-d H:i:s') : null,
            'updated_at'        => $setting->updated_at ? $setting->updated_at->format('Y-m-d H:i:s') : null
        ];
    }

}