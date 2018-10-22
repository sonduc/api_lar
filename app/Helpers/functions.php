<?php

/**
 * Slug string
 * @author HarikiRito <nxh0809@gmail.com>
 *
 * @param $str
 *
 * @return null|string|string[]
 */
function to_slug($str)
{
    $str = trim(mb_strtolower($str));
    $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
    $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
    $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
    $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
    $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
    $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
    $str = preg_replace('/(đ)/', 'd', $str);
    $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
    $str = preg_replace('/([\s]+)/', '-', $str);
    return $str;
}

/**
 * Log activity
 * @author HarikiRito <nxh0809@gmail.com>
 *
 * @param string $name
 * @param null   $log
 * @param array  $data
 */
if (!function_exists('logs')) {
    function logs($name = 'default', $log = null, $data = [])
    {
        if (!is_array($data)) {
            $data = $data->toArray();
        }
        $user = \Auth::user() ? \Auth::user()->name : null;
        activity($name)->withProperties($data)->log($user . " {$log}");
    }
}

if (!function_exists('rand_name')) {
    function rand_name($name = null)
    {
        $name = $name ?? str_random();

        $imgName = date('Y_m_d') . '_' . str_shuffle(time() . substr(md5($name), rand(0, 20), 10));

        return $imgName;
    }
}

if (!function_exists('translate')) {
    function translate($id = null, $replace = [])
    {
        $locale = \Illuminate\Support\Facades\Cookie::get('locale') ?? config('app.locale');
        return trans($id, $replace, $locale);
    }
}
