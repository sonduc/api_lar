<?php

namespace App\Http\Controllers\Api;


use App\Repositories\Rooms\RoomRepository;
use Illuminate\Http\Request;
use App\Http\Transformers\RoomTransformer;
use Illuminate\Support\Facades\DB;

class RoomController extends ApiController
{
    protected $validationRules = [
        'details.*.*.name'                                  => 'required|min:10|max:255|v_title',
        'comforts.*'                                        => 'nullable|numeric|exists:comforts,id|distinct',
        'merchant_id'                                       => 'required|numeric|exists:users,id',
        'max_guest'                                         => 'required|numeric|min:1',
        'max_additional_guest'                              => 'numeric|nullable',
        'number_bed'                                        => 'required|numeric|min:1',
        'number_room'                                       => 'required|numeric|min:1',
        'city_id'                                           => 'numeric|nullable|exists:cities,id',
        'district_id'                                       => 'numeric|nullable|exists:districts,id',
        // 'room_type_id'                                   => 'required|numeric',
        'checkin'                                           => 'required|date_format:"H:i"',
        'checkout'                                          => 'required|date_format:"H:i"',
        'price_day'                                         => 'required|numeric',
        'price_hour'                                        => 'numeric|nullable',
        'price_after_hour'                                  => 'numeric|required_with:price_hour',
        'price_charge_guest'                                => 'numeric|nullable',
        'cleaning_fee'                                      => 'numeric|nullable',
        'standard_point'                                    => 'numeric|nullable',
        'is_manager'                                        => 'numeric|nullable',
        'hot'                                               => 'numeric',
        'new'                                               => 'numeric',
        'latest_deal'                                       => 'numeric|nullable',
        'rent_type'                                         => 'numeric',
        // 'longitude'                                      => 'required',
        // 'latitude'                                       => 'required',
        'details.*.*.address'                               => 'required|v_title',
        'note'                                              => 'nullable|v_title',
        'sale_id'                                           => 'numeric|nullable|exists:users,id',
        'lang_id'                                           => 'numeric|exists:languages,id',
        'status'                                            => 'numeric',
        'weekday_price.*.price_day'                         => 'numeric|nullable',
        'weekday_price.*.price_hour'                        => 'numeric|nullable',
        'weekday_price.*.price_after_hour'                  => 'numeric|nullable|required_with:weekday_price.*.price_hour',
        'weekday_price.*.price_charge_guest'                => 'numeric|nullable',
        'weekday_price.*.status'                            => 'boolean|nullable',
        'weekday_price.*.weekday'                           => 'required|numeric|distinct|between:1,7',
        'optional_prices.days.*'                            => 'nullable|date_format:Y-m-d|distinct',
        'optional_prices.price_day'                         => 'numeric|nullable',
        'optional_prices.price_hour'                        => 'numeric|nullable',
        'optional_prices.price_after_hour'                  => 'numeric|nullable|required_with:optional_prices.price_hour',
        'optional_prices.price_charge_guest'                => 'numeric|nullable',
        'optional_prices.status'                            => 'boolean|nullable',
        'room_time_blocks.*'                                => 'date_format:Y-m-d|distinct'
    ];

    protected $validationMessages = [
        'details.*.*.name.required'                         => 'Tên không được để trông',
        'details.*.*.name.min'                              => 'Tối thiểu 10 ký tự',
        'details.*.*.name.max'                              => 'Tối đa 255 ký tự',
        'details.*.*.name.alpha_num'                        => 'Chỉ cho phép chữ và số',
        'details.*.*.name.v_title'                          => 'Chỉ cho phép chữ và số',
        'comforts.*.numeric'                                => 'Mã dịch vụ phải là kiểu số',
        'comforts.*.exists'                                 => 'Mã dịch vụ không tồn tại trong hệ thống',
        'comforts.*.distinct'                               => 'Mã dịch vụ bị trùng lặp',
        'merchant_id.required'                              => 'Chủ phòng không được để trống',
        'merchant_id.exists'                                => 'Chủ phòng không tồn tại',
        'max_guest.required'                                => 'Số khách tối đa không được để trống',
        'max_guest.numeric'                                 => 'Trường số khách tối đa phải là kiểu số',
        'max_additional_guest.numeric'                      => 'Số khách tối đa phải là kiểu số',
        'number_bed.required'                               => 'Vui lòng điền số giường',
        'number_bed.min'                                    => 'Tối thiểu 1 giường',
        'number_bed.numeric'                                => 'Số giường phải là kiểu số',
        'number_room.required'                              => 'Vui lòng điền số phòng',
        'number_room.min'                                   => 'Tối thiểu 1 phòng',
        'number_room.numeric'                               => 'Số phòng phải là kiểu số',
        'city_id.numeric'                                   => 'Mã thành phố phải là kiểu số',
        'city_id.exists'                                    => 'Thành phố không tồn tại',
        'district_id.numeric'                               => 'Mã tỉnh phải là kiểu số',
        'district_id.exists'                                => 'Tỉnh không tồn tại',
        'room_type_id.required'                             => 'Kiểu phòng không được để trống',
        'room_type_id.numeric'                              => 'Kiểu phòng phải là kiểu số',
        'checkin.required'                                  => 'Thời gian checkin không được để trống',
        'checkin.date_format'                               => 'Kiểu checkin không đúng định dạng H:i',
        'checkout.required'                                 => 'Thời gian checkout không được để trống',
        'checkout.date_format'                              => 'Kiểu checkout không đúng định dạng H:i',
        'price_day.required'                                => 'Giá ngày không được để trống',
        'price_day.numeric'                                 => 'Giá phải là kiểu số',
        'price_hour.numeric'                                => 'Giá theo giờ phải là kiểu số',
        'price_after_hour.required_with'                    => 'Giá theo giờ không được để trống',
        'price_after_hour.numeric'                          => 'Giá theo giờ phải là kiểu số',
        'price_charge_guest.numeric'                        => 'Giá khách thêm phải là kiểu số',
        'weekday_price.*.price_day.numeric'                 => 'Giá phải là kiểu số',
        'weekday_price.*.price_hour.numeric'                => 'Giá theo giờ phải là kiểu số',
        'weekday_price.*.price_after_hour.required_with'    => 'Giá theo giờ không được để trống',
        'weekday_price.*.price_after_hour.numeric'          => 'Giá theo giờ phải là kiểu số',
        'weekday_price.*.price_charge_guest.numeric'        => 'Giá khách thêm phải là kiểu số',
        'weekday_price.*.status.boolean'                    => 'Mã trạng thái phải là kiểu số 0 hoặc 1',
        'weekday_price.*.weekday.required'                  => 'Vui lòng chọn thứ trong ngày hợp lệ',
        'weekday_price.*.weekday.numeric'                   => 'Mã thứ phải là kiểu số',
        'weekday_price.*.weekday.distinct'                  => 'Mã thứ không được phép trùng nhau',
        'weekday_price.*.weekday.between'                   => 'Mã thứ phải trong khoảng từ 1 đến 7',

        'optional_prices.days.*.date_format'                => 'Định dạng của ngày phải là Y-m-d',
        'optional_prices.days.*.distinct'                   => 'Ngày không được phép trùng nhau',
        'optional_prices.price_day.numeric'                 => 'Giá phải là kiểu số',
        'optional_prices.price_hour.numeric'                => 'Giá theo giờ phải là kiểu số',
        'optional_prices.price_after_hour.required_with'    => 'Giá theo giờ không được để trống',
        'optional_prices.price_after_hour.numeric'          => 'Giá theo giờ phải là kiểu số',
        'optional_prices.price_charge_guest.numeric'        => 'Giá khách thêm phải là kiểu số',
        'optional_prices.status.boolean'                    => 'Mã trạng thái phải là kiểu số 0 hoặc 1',

        'cleaning_fee.numeric'                              => 'Giá dọn phòng phải là kiểu số',
        'standard_point.numeric'                            => 'Điểm phải là kiểu số',
        'is_manager.numeric'                                => 'Kiểu quản lý phải là kiểu số',
        'hot.numeric'                                       => 'Nổi bật phải là kiểu số',
        'new.numeric'                                       => 'Mới nhất phải là kiểu số',
        'latest_deal.numeric'                               => 'Giá hạ sàn phải là kiểu số',
        'details.*.*.address.required'                      => 'Vui lòng điền địa chỉ',
        'rent_type.numeric'                                 => 'Kiểu thuê phòng phải là dạng số',
        'longitude.required'                                => 'Kinh độ không được để trống',
        'latitude.required'                                 => 'Vĩ độ không được để trống',
        'sale_id.numeric'                                   => 'Mã saler phải là kiểu số',
        'lang_id.numeric'                                   => 'Mã ngôn ngữ phải là kiểu số',
        'lang_id.exists'                                    => 'Ngôn ngữ không hợp lệ',
        'note.v_title'                                      => 'Chỉ cho phép chữ và số',
        'status.numeric'                                    => 'Mã trạng thái phải là kiểu số',
        'room_time_blocks.*.date_format'                    => 'Ngày không đúng định dạng Y-m-d',
        'room_time_blocks.*.distinct'                       => 'Ngày không được phép trùng nhau',
    ];
    /**
     * RoleController constructor.
     * @param RoleRepository $role
     */
    public function __construct(RoomRepository $room)
    {
        $this->model = $room;
        $this->setTransformer(new RoomTransformer);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('room.view');
        $pageSize       = $request->get('limit', 25);
        $this->trash    = $this->trashStatus($request);

        $data           = $this->model->getByQuery($request->all(), $pageSize, $this->trash);

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
            $this->authorize('room.view');
            $trashed = $request->has('trashed') ? true : false;
            $data = $this->model->getById($id, $trashed);
            return $this->successResponse($data);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * Tạo phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('room.create');
            $this->validate($request, $this->validationRules, $this->validationMessages);

            $data = $this->model->store($request->all());
            DB::commit();
            logs('room', 'tạo phòng mã '.$data->id, $data);
            return $this->successResponse($data, true, 'details');
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            DB::rollBack();
            return $this->errorResponse([
                'errors' => $validationException->validator->errors(),
                'exception' => $validationException->getMessage()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }

    /**
     * Sửa phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('room.update');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->update($id, $request->all());
//            dd(DB::getQueryLog());
            DB::commit();
            logs('room', 'sửa phòng mã '.$data->id, $data);
            return $this->successResponse($data, true, 'details');
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            return $this->errorResponse([
                'errors' => $validationException->validator->errors(),
                'exception' => $validationException->getMessage()
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

    /**
     * Xóa phòng (Soft Delete)
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->authorize('room.delete');
            $this->model->delete($id);

            DB::commit();
            logs('room', 'xóa phòng mã '.$data->id, $data);
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
     * Thay đổi trạng thái của phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function changeStatus(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $this->authorize('room.update');
            $data = $this->model->status($id, $request->all());

            DB::commit();
            logs('room', 'thay đổi trạng thái của phòng mã '.$data->id);
            return $this->successResponse($data);
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
     * Lấy ra kiểu phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function getRoomType()
    {
        try {
            $data = $this->model->getRoomType();
            return response()->json($data);
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }
}
