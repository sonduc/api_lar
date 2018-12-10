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

if (!function_exists('trans2')) {
    function trans2($id = null, $replace = [])
    {
        $locale = getLocale();

        return trans($id, $replace, $locale);
    }
}

if (!function_exists('getLocale')) {
    function getLocale()
    {
        $locale = \Illuminate\Support\Facades\Cookie::get('locale');
        if (!array_key_exists($locale, config('languages'))) {
            $locale = config('app.locale');
        }
        return $locale;
    }
}

/**
 * resource router helper
 * @author SaturnLai <daolvcntt@gmail.com>
 * @date   2018-07-17
 *
 * @param  string                       $uri        enpoint url
 * @param  string                       $controller controller name
 * @param  Laravel\Lumen\Routing\Router $router     RouterObject
 */
function resource($uri, $controller, Laravel\Lumen\Routing\Router $router)
{
    $router->get($uri, $controller . '@index');
    $router->get($uri . '/{id}', $controller . '@show');
    $router->post($uri, $controller . '@store');
    $router->put($uri . '/{id}', $controller . '@update');
    $router->delete($uri . '/{id}', $controller . '@destroy');
}

function arrayToObject(array $arr = []): array
{
    $array_value = [];
    foreach ($arr as $key => $item) {
        $array_value[] = [
            'id'   => $key,
            'name' => $item,
        ];
    }

    return $array_value;
}

/**
 * Lấy ra khoảng cách giữa 2 điểm dựa vào kinh độ và vĩ độ
 * @author tuananh1402 <tuananhpham1402@gmail.com>
 * @date   2017-07-17
 *
 * @param  int $lat_1  First place latitude
 * @param  int $lat_2  Second place latitude
 * @param  int $long_1 First place longitude
 * @param  int $long_2 Second place longitude
 */
function getDistance($lat_1, $long_1, $lat_2, $long_2)
{
    $theta = $long_1 - $long_2;
    $miles = rad2deg(acos((sin(deg2rad($lat_1)) * sin(deg2rad($lat_2))) + (cos(deg2rad($lat_1)) * cos(deg2rad($lat_2)) * cos(deg2rad($theta)))));

    $kilometers = $miles * 1.609344;
    $meters     = $kilometers * 1000;
    $distance   = [
        "miles"      => $miles,
        "kilometers" => $kilometers,
        "meters"     => $meters,
    ];

    return $distance;
}
