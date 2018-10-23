<?php

namespace App\Repositories\Blogs;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;
    // Định nghĩa trạng thái bài viết
    const AVAILABLE    = 0;
    const UNAVAILABLE  = 1;
    const BLOG_STATUS    = [
        self::AVAILABLE      => 'ĐÃ DUYỆT',
        self::UNAVAILABLE    => 'ĐANG CHỜ DUYỆT',
    ];
    const BLOG_HOT    = [
        self::AVAILABLE      => 'NỔI BẬT',
        self::UNAVAILABLE    => 'KHÔNG NỔI BẬT',
    ];

    const TBLOG_NEW    = [
        self::AVAILABLE      => 'MỚI',
        self::UNAVAILABLE    => 'CŨ',
    ];

    protected $table = 'blogs';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'image','status','hot','user_id','category_id'
        ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];


    /**
     * relation ship voi blogs_translate
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function blogTrans()
    {
        return $this->hasMany(BlogTranslate::class, 'blog_id');
    }

    /**
     *
     * @author ducchien612 <0ducchien612@gmail.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'blog_tags', 'blog_id','tag_id');
    }

    /**
     *
     * @author ducchien612 <0ducchien612@gmail.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsTo(\App\Repositories\Categories\Category::class,'category_id');
    }

    /**
     *
     * @author ducchien612 <0ducchien612@gmail.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsTo(\App\User::class,'user_id');
    }





}
