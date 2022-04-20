<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Operators\Controllers\Admin;

use Validator;
use Illuminate\Http\Request;

use App\Http\Controllers\Admin\AbstractAdminController;

use App\Modules\Operators\Models\Repositories\Contracts\ProvincesInterface;
use App\Modules\Operators\Models\Repositories\Contracts\DistrictsInterface;
use App\Modules\Systems\Events\CreateLogEvents;

class DistrictsController extends AbstractAdminController
{

    protected $_provincesInterface;
    protected $_districtsInterface;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ProvincesInterface $provincesInterface,
                                DistrictsInterface $districtsInterface)
    {
        parent::__construct();

        $this->_provincesInterface = $provincesInterface;
        $this->_districtsInterface = $districtsInterface;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_districts_view'))
        {
            abort(403);
        }

        $arrFilter = array('p_id' => 0, 'name' => '');
        if ($request->has('p')) {
            $arrFilter['p_id'] = (int)$request->input('p');
        }
        if ($request->has('n')) {
            $arrFilter['name'] = $request->input('n');
        }

        $provinces = $this->_provincesInterface->getMore();
        $districts = $this->_districtsInterface->getMore($arrFilter, array('with' => array('provinces', 'wards')), 10);

        return view('Operators::districts.list', ['provinces' => $provinces, 'districts' => $districts, 'arrFilter' => $arrFilter]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $you = auth('admin')->user();
        if (!$you->can('action_districts_create'))
        {
            abort(403);
        }

        $provinces = $this->_provincesInterface->getMore();

        return view('Operators::districts.create', ['provinces' => $provinces]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_districts_create'))
        {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:noi,ngoai,huyen',
            'code' => 'required|max:10|unique:zone_districts',
            'name' => 'required|max:255'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

       $model =  $this->_districtsInterface->create($request->all());

        //Thêm dữ liệu log
        $log_data[] = [
            'model' => $model,
        ];
        //Lưu log
        event(new CreateLogEvents($log_data, 'districts', 'districts_create'));

        \Func::setToast('Thành công', 'Thêm mới thành công quận huyện: ' . $request->input('name'), 'notice');
        return redirect()->route('admin.districts.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort(404);
        $district = $this->_districtsInterface->getById($id);

        return view('Operators::districts.show', ['district' => $district]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_districts_update'))
        {
            abort(403);
        }

        $provinces = $this->_provincesInterface->getMore();
        $district = $this->_districtsInterface->getById($id);

        return view('Operators::districts.edit', [
            'provinces' => $provinces,
            'district' => $district
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_districts_update'))
        {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:noi,ngoai,huyen',
            'code' => 'required|max:10|unique:zone_districts,code,' . $id,
            'name' => 'required|max:255'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $old_data = $this->_districtsInterface->getById($id);
        $this->_districtsInterface->updateById($id, $request->all());

        //Thêm dữ liệu log
        $log_data[] = [
            'old_data' => $old_data,
        ];
        //Lưu log
        event(new CreateLogEvents($log_data, 'districts', 'districts_update'));

        \Func::setToast('Thành công', 'Cập nhật thành công quận huyện: ' . $request->input('name'), 'notice');
        return redirect()->route('admin.districts.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_districts_delete'))
        {
            abort(403);
        }

        $district = $this->_districtsInterface->getById($id);
        if ($district) {
            //Thêm dữ liệu log
            $log_data[] = [
                'old_data' => $district,
            ];
            //Lưu log
            event(new CreateLogEvents($log_data, 'districts', 'districts_delete'));

            \Func::setToast('Thành công', 'Xoá thành công quận huyện: ' . $district->name, 'notice');
            $district->delete();
        }
        return redirect()->route('admin.districts.index');
    }
}
