<?php

namespace App\Http\Controllers\Api;


use App\Repositories\Rooms\RoomRepository;
use Illuminate\Http\Request;
use App\Http\Transformers\RoomTransformer;

class RoomController extends ApiController
{
    protected $validationRules = [
        'name'                      => 'required|min:10|max:255|v_title',
        'merchant_id'               => 'required|numeric|exists:users,id',
        'max_guest'                 => 'required|numeric|min:1',
        'max_additional_guest'      => 'numeric|nullable',
        'number_bed'                => 'required|numeric|min:1',
        'number_room'               => 'required|numeric|min:1',
        'city_id'                   => 'numeric|nullable|exists:cities,id',
        'district_id'               => 'numeric|nullable|exists:districts,id',
        'room_type_id'              => 'required|numeric',
        'checkin'                   => 'required|date_format:"H:i"',
        'checkout'                  => 'required|date_format:"H:i"',
        'price_day'                 => 'required|numeric',
        'price_hour'                => 'numeric|nullable',
        'price_after_hour'          => 'numeric|required_with:price_hour',
        'price_charge_guest'        => 'numeric|nullable',
        'cleaning_fee'              => 'numeric|nullable',
        'standard_point'            => 'numeric|nullable',
        'is_manager'                => 'numeric|nullable',
        'hot'                       => 'numeric',
        'new'                       => 'numeric',
        'latest_deal'               => 'numeric|nullable',
        'rent_type'                 => 'numeric',
        'rules'                     => 'required',
        'longitude'                 => 'required',
        'latitude'                  => 'required',
        'address'                   => 'required',
        'note'                      => 'nullable|v_title',
        'sale_id'                   => 'numeric|nullable|exists:users,id',
        'lang_id'                   => 'exists:languages,id',
        'status'                    => 'numeric',
        'description'               => 'v_title',
    ];
    protected $validationMessages = [
        'name.required'                     => 'Tên không được để trông',
        'name.min'                          => 'Tối thiểu 10 ký tự',
        'name.max'                          => 'Tối đa 255 ký tự',
        'name.alpha_num'                    => 'Chỉ cho phép chữ và số',
        'merchant_id.required'              => 'Chủ phòng không được để trống',
        'merchant_id.exists'                => 'Chủ phòng không tồn tại',
        'max_guest.required'                => 'Số khách tối đa không được để trống',
        'max_guest.numeric'                 => 'Trường số khách tối đa phải là kiểu số',
        'max_additional_guest.numeric'      => 'Số khách tối đa phải là kiểu số',
        'number_bed.required'               => 'Vui lòng điền số giường',
        'number_bed.min'                    => 'Tối thiểu 1 giường',
        'number_bed.numeric'                => 'Số giường phải là kiểu số',
        'number_room.required'              => 'Vui lòng điền số phòng',
        'number_room.min'                   => 'Tối thiểu 1 phòng',
        'number_room.numeric'               => 'Số phòng phải là kiểu số',
        'city_id.numeric'                   => 'Mã thành phố phải là kiểu số',
        'city_id.exists'                    => 'Thành phố không tồn tại',
        'district_id.numeric'               => 'Mã tỉnh phải là kiểu số',
        'district_id.exists'                => 'Tỉnh không tồn tại',
        'room_type_id.required'             => 'Kiểu phòng không được để trống',
        'room_type_id.numeric'              => 'Kiểu phòng phải là kiểu số',
        'checkin.required'                  => 'Thời gian checkin không được để trống',
        'checkin.date_format'               => 'Kiểu checkin không đúng định dạng H:i',
        'checkout.required'                 => 'Thời gian checkout không được để trống',
        'checkout.date_format'              => 'Kiểu checkout không đúng định dạng H:i',
        'price_day.required'                => 'Giá ngày không được để trống',
        'price_day.numeric'                 => 'Giá phải là kiểu số',
        'price_hour.numeric'                => 'Giá theo giờ phải là kiểu số',
        'price_after_hour.required_with'    => 'Giá theo giờ không được để trống',
        'price_after_hour.numeric'          => 'Giá theo giờ phải là kiểu số',
        'price_charge_guest.numeric'        => 'Giá khách thêm phải là kiểu số',
        'cleaning_fee.numeric'              => 'Giá dọn phòng phải là kiểu số',
        'standard_point.numeric'            => 'Điểm phải là kiểu số',
        'is_manager.numeric'                => 'Kiểu quản lý phải là kiểu số',
        'hot.numeric'                       => 'Nổi bật phải là kiểu số',
        'new.numeric'                       => 'Mới nhất phải là kiểu số',
        'latest_deal.numeric'               => 'Giá hạ sàn phải là kiểu số',
        'address.required'                  => 'Vui lòng điền địa chỉ',
        'rent_type.numeric'                 => 'Kiểu thuê phòng phải là dạng số',
        'rules.required'                    => 'Luật của phòng không được để trống',
        'longitude.required'                => 'Kinh độ không được để trống',
        'latitude.required'                 => 'Vĩ độ không được để trống',
        'sale_id.numeric'                   => 'Mã saler phải là kiểu số',
        'lang_id.numeric'                   => 'Mã ngôn ngữ phải là kiểu số',
        'lang_id.exists'                    => 'Ngôn ngữ không hợp lệ',
        'name.v_title'                      => 'Chỉ cho phép chữ và số',
        'note.v_title'                      => 'Chỉ cho phép chữ và số',
        'status.numeric'                    => 'Mã trạng thái phải là kiểu số',
        'description.v_title'               => 'Chú thích chỉ chứa chữ và số',
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
        $pageSize = $request->get('limit', 25);
        $this->trash = $this->trashStatus($request);

        return $this->successResponse($this->model->getByQuery($request->all(), $pageSize, $this->trash));
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

    public function store(Request $request)
    {
        try {
            $this->authorize('room.create');
            $this->validate($request, $this->validationRules, $this->validationMessages);

             $data = $this->model->store($request->all());

            return $this->successResponse($data, true, 'details');
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            return $this->errorResponse([
                'errors' => $validationException->validator->errors(),
                'exception' => $validationException->getMessage()
            ]);
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $this->authorize('room.update');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->update($id, $request->all());

            return $this->successResponse($data, true, 'details');
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            return $this->errorResponse([
                'errors' => $validationException->validator->errors(),
                'exception' => $validationException->getMessage()
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    public function destroy($id)
    {
        try {
            $this->authorize('room.delete');
            $this->model->deleteRoom($id);

            return $this->deleteResponse();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }
}
