<?php

namespace App\Http\Controllers\ApiCustomer;

use App\Http\Transformers\Customer\BlogTransformer;
use App\Repositories\Blogs\Blog;
use App\Repositories\Blogs\BlogLogic;
use App\Repositories\Blogs\BlogRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class BlogController extends ApiController
{
    protected $validationRules
        = [
            'hot'                       => 'numeric|between:0,1|filled',
            'status'                    => 'numeric|between:0,1|filled',
            'new'                       => 'numeric|between:0,1|filled',
            // 'image'                     =>'image|mimes:jpeg,bmp,png,jpg',
            'category_id'               => 'required|numeric|exists:categories,id',
            'title'                     => 'required|v_title',
            // 'lang'                      => 'required',
            'content'                   => 'required',
            'description'               => 'required',
            'tags.*.*.name'             => 'v_title',
        ];
    protected $validationMessages
        = [
            //type=status
            'status.integer'             => 'Mã trạng thái phải là kiểu số',
            'status.between'             => 'Mã trạng thái không phù hợp',
            'status.filled'              => 'Vui lòng nhập mã trạng thái ',
            //type=hot
            'hot.integer'                => 'Mã nổi bật phải là kiểu số',
            'hot.between'                => 'Mã nổi bật không phù hợp',
            'hot.filled'                 => 'Vui lòng nhập mã trạng thái',
            //type=new
            'new.integer'                => 'Danh mục mới nhất phải là kiểu số',
            'new.between'                => 'Mã danh mục mới nhất không phù hợp',
            'new.filled'                 => 'Vui lòng nhập mã trạng thái',
            'category_id.required'       => 'Vui lòng chọn danh mục',
            'category_id.numeric'        => 'Mã danh mục phải là kiểu số',
            'category_id.exists'         => 'Danh mục không tồn tại',
            //'image.image'                       =>'Định dạng không phải là hình ảnh',
            //'image.mimes'                       => 'Hình ảnh phải thuộc kiểu jpg,bmp,jpeg,png',
            'title.required' => 'Tiêu đề không được để trông',
            // 'title.unique'   => 'Tên này đã tồn tại',
            'title.v_title'  => 'Tên tiêu đề không hợp lê',
            'tags.*.*.name.v_title'      => "Từ khóa không hơp lệ",
        ];

    /**
     * BlogController constructor.
     *
     * @param BlogRepository $blog
     */
    public function __construct(BlogLogic $blog)
    {
        $this->model = $blog;
        $this->setTransformer(new BlogTransformer);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        DB::enableQueryLog();
        try {
            $pageSize    = $request->get('limit', 25);
            $this->trash = $this->trashStatus($request);
            $data        = $this->model->getByQuery($request->all(), $pageSize, $this->trash);
            // dd(DB::getQueryLog());
            return $this->successResponse($data);
        } catch (AuthorizationException $f) {
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            $trashed = $request->has('trashed') ? true : false;
            $data    = $this->model->getById($id, $trashed);
            return $this->successResponse($data);
        } catch (AuthorizationException $f) {
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * Lấy ra các Trạng thái bài viết (theo hot)
     * @author 0ducchien612 <0ducchien612@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function hotList()
    {
        try {
            $this->authorize('blog.view');
            $data = $this->simpleArrayToObject(Blog::BLOG_HOT);
            return response()->json($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Lấy ra các Trạng thái bài viết (theo new)
     * @author 0ducchien612 <0ducchien612@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function newList()
    {
        try {
            $this->authorize('blog.view');
            $data = $this->simpleArrayToObject(Blog::BLOG_NEW);
            return response()->json($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
