<?php

namespace App\Repositories\Categories;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;
    const AVAILABLE    = 0;
    const UNAVAILABLE  = 1;
    const CATEGORY_STATUS    = [
        self::AVAILABLE      => 'HOẠT ĐỘNG',
        self::UNAVAILABLE    => 'KHOÁ',
    ];
    const CATEGORY_HOT    = [
        self::AVAILABLE      => 'NỔI BẬT',
        self::UNAVAILABLE    => 'KHÔNG NỔI BẬT',
    ];

   // protected $table = 'categories';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'hot','status','image',
        ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    //protected $casts = ['permissions' => 'array'];


    /**
     * relation ship voi catagories_translate
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categoryTrans()
    {
        return $this->hasMany(\App\Repositories\Categories\CategoryTranslate::class, 'category_id');
    }

    /**
     * relation ship voi blogs
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function blogs()
    {
        return $this->hasMany(\App\Repositories\Blogs\Blog::class,'category_id');
    }


}
