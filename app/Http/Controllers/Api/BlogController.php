<?php

namespace App\Http\Controllers\Api;

use App\Http\Transformers\BlogTransformer;
use App\Repositories\Blogs\Blog;
use App\Repositories\Blogs\BlogRepository;
use DB;
use Illuminate\Http\Request;

class BlogController extends ApiController
{
    protected $validationRules
        = [
            'hot'                 => 'required|integer|between:0,1',
            'status'              => 'required|integer|between:0,1',
            //'image'                             =>'image|mimes:jpeg,bmp,png,jpg',
            'category_id'         => 'required|numeric|exists:categories,id',
            'user_id'             => 'required|numeric',
            'details.*.*.title'   => 'required|v_title|unique:blog_translates,title',
            'details.*.*.lang'    => 'required',
            'details.*.*.content' => 'required',
            'tags.*.*.name'       => 'required|v_title',
        ];
    protected $validationMessages
        = [
            'hot.required'               => 'Vui lòng chọn mã nổi bật',
            'hot.numeric'                => ' Mã nổi bật phải là kiểu số',
            'hot.between'                => 'Mã nổi bật không phù hợp',
            'status.required'            => 'Vui lòng chọn mã trạng thái',
            'status.numeric'             => 'Mã trạng thái phải là kiểu s',
            'status.between'             => 'Mã trạng thái không phhơp',
            'category_id.required'       => 'Vui lòng chọn danh mục',
            'category_id.numeric'        => 'Mã danh mục phải là kiểu số',
            'category_id.exists'         => 'Danh mục không tồn tại',
            'user_id.required'           => 'Vui lòng chọn người viết bài',
            'user_id.numeric'            => 'Mã người viết bài phải là kiểu số',
            //'image.image'                       =>'Định dạng không phải là hình ảnh',
            //'image.mimes'                       => 'Hình ảnh phải thuộc kiểu jpg,bmp,jpeg,png',
            'details.*.*.title.required' => 'Tiêu đề không được để trông',
            'details.*.*.title.unique'   => 'Tên này đã tồn tại',
            'details.*.*.title.v_title'  => 'Tên tiêu đề không hợp lê',
            'tags.*.*.name.required'     => "Từ khóa không được để trống",
            'tags.*.*.name.v_title'      => "Từ khóa không hơp lệ",
        ];

    /**
     * BlogController constructor.
     *
     * @param BlogRepository $blog
     */
    public function __construct(BlogRepository $blog)
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
        $this->authorize('blog.view');
        $pageSize    = $request->get('limit', 25);
        $this->trash = $this->trashStatus($request);
        $data        = $this->model->getByQuery($request->all(), $pageSize, $this->trash);
        // dd(DB::getQueryLog());
        return $this->successResponse($data);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            $this->authorize('blog.view');
            $trashed = $request->has('trashed') ? true : false;
            $data    = $this->model->getById($id, $trashed);
            return $this->successResponse($data);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('blog.create');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $model = $this->model->store($request->all());
            //dd(DB::getQueryLog());
            DB::commit();
            logs('blogs', 'taọ bài viết mã ' . $model->id, $model);
            return $this->successResponse($model, true, 'details');
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            DB::rollBack();
            return $this->errorResponse([
                'errors'    => $validationException->validator->errors(),
                'exception' => $validationException->getMessage(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('blog.update');
            $this->validationRules['details.*.*.title'] = "required|v_title";
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $model = $this->model->update($id, $request->all());
            DB::commit();
            logs('blogs', 'sửa bài viết mã ' . $model->id, $model);
            //dd(DB::getQueryLog());
            return $this->successResponse($model, true, 'details');
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            DB::rollBack();
            return $this->errorResponse([
                'errors'    => $validationException->validator->errors(),
                'exception' => $validationException->getMessage(),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('blog.delete');
            $this->model->destroyBlog($id);
            DB::commit();
            //dd(DB::getQueryLog());
            return $this->deleteResponse();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }

    /**
     * Thực hiện cập nhật hot, status
     * @author 0ducchien612 <0ducchien612@gmail.com>
     *
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function singleUpdate(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $this->authorize('blog.update');
            $avaiable_option = ['hot', 'status'];
            $option          = $request->get('option');
            if (!in_array($option, $avaiable_option)) throw new \Exception('Không có quyền sửa đổi mục này');

            $validate = array_only($this->validationRules, [
                $option,
            ]);

            $this->validate($request, $validate, $this->validationMessages);

            $data = $this->model->singleUpdate($id, $request->only($option));
            logs('blogs', 'sửa trạng thái của bài viết có code ' . $data->code, $data);
            DB::commit();
            return $this->successResponse($data);

        } catch (\Illuminate\Validation\ValidationException $validationException) {
            DB::rollBack();
            return $this->errorResponse([
                'errors'    => $validationException->validator->errors(),
                'exception' => $validationException->getMessage(),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse([
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }

    /**
     * Lấy ra các Trạng thái bài viết (theo status)
     * @author 0ducchien612 <0ducchien612@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function statusList()
    {
        try {
            $this->authorize('blog.view');
            $data = $this->simpleArrayToObject(Blog::BLOG_STATUS);
            return response()->json($data);
        } catch (\Exception $e) {
            throw $e;
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
        } catch (\Exception $e) {
            throw $e;
        }
    }


}
