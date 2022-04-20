<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Operators\Controllers\Admin;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AbstractAdminController;

use App\Modules\Operators\Models\Repositories\Contracts\WardsInterface;
use App\Modules\Operators\Models\Repositories\Contracts\DistrictsInterface;
use App\Modules\Operators\Models\Repositories\Contracts\ProvincesInterface;
use App\Modules\Systems\Events\CreateLogEvents;

class WardsController extends AbstractAdminController
{

    protected $_provincesInterface;
    protected $_districtsInterface;
    protected $_wardsInterface;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ProvincesInterface $provincesInterface,
                                DistrictsInterface $districtsInterface,
                                WardsInterface $wardsInterface)
    {
        parent::__construct();

        $this->_provincesInterface = $provincesInterface;
        $this->_districtsInterface = $districtsInterface;
        $this->_wardsInterface = $wardsInterface;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_wards_view'))
        {
            abort(403);
        }

        $districts = array();
        $arrFilter = array('p_id' => 0, 'd_id' => 0, 'name' => '');
        if ($request->has('p')) {
            $arrFilter['p_id'] = (int)$request->input('p');
            if ($arrFilter['p_id'] > 0) {
                $districts = $this->_districtsInterface->getMore(array('p_id' => $arrFilter['p_id']));
            }
        }
        if ($request->has('d')) {
            $arrFilter['d_id'] = (int)$request->input('d');
        }
        if ($request->has('n')) {
            $arrFilter['name'] = $request->input('n');
        }

        $provinces = $this->_provincesInterface->getMore();
        $wards = $this->_wardsInterface->getMore($arrFilter, array('with' => array('provinces', 'districts')), 10);

        return view('Operators::wards.list', [
            'wards' => $wards,
            'provinces' => $provinces,
            'districts' => $districts,
            'arrFilter' => $arrFilter
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $you = auth('admin')->user();
        if (!$you->can('action_wards_create'))
        {
            abort(403);
        }

        $provinces = $this->_provincesInterface->getMore();
        $districts = $this->_districtsInterface->getMore(array('p_id' => $provinces[0]->id));

        return view('Operators::wards.create', ['provinces' => $provinces, 'districts' => $districts]);
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
        if (!$you->can('action_wards_create'))
        {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'p_id' => 'required',
            'd_id' => 'required',
            'code' => 'required|max:10|unique:zone_wards,code',
            'name' => 'required|max:255'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $model = $this->_wardsInterface->create($request->all());

        //Thêm dữ liệu log
        $log_data[] = [
            'model' => $model,
        ];
        //Lưu log
        event(new CreateLogEvents($log_data, 'wards', 'wards_create'));

        \Func::setToast('Thành công', 'Thêm mới thành công phường xã: ' . $request->input('name'), 'notice');
        return redirect()->route('admin.wards.index');
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
        $ward = $this->_wardsInterface->getById($id);

        return view('Operators::wards.show', ['ward' => $ward]);
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
        if (!$you->can('action_wards_update'))
        {
            abort(403);
        }

        $ward = $this->_wardsInterface->getById($id);
        $provinces = $this->_provincesInterface->getMore();
        $districts = $this->_districtsInterface->getMore(array('p_id' => $ward->p_id));

        return view('Operators::wards.edit', ['ward' => $ward, 'provinces' => $provinces, 'districts' => $districts]);
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
        if (!$you->can('action_wards_update'))
        {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'p_id' => 'required',
            'd_id' => 'required',
            'code' => 'required|max:10|unique:zone_wards,code,' . $id,
            'name' => 'required|max:255'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $old_data = $this->_wardsInterface->getById($id);
        $this->_wardsInterface->updateById($id, $request->all());

        //Thêm dữ liệu log
        $log_data[] = [
            'old_data' => $old_data,
        ];
        //Lưu log
        event(new CreateLogEvents($log_data, 'wards', 'wards_update'));

        \Func::setToast('Thành công', 'Cập nhật thành công phường xã: ' . $request->input('name'), 'notice');
        return redirect()->route('admin.wards.index');
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
        if (!$you->can('action_wards_delete'))
        {
            abort(403);
        }

        $ward = $this->_wardsInterface->getById($id);
        if ($ward) {
            //Thêm dữ liệu log
            $log_data[] = [
                'old_data' => $ward,
            ];
            //Lưu log
            event(new CreateLogEvents($log_data, 'wards', 'wards_delete'));

            \Func::setToast('Thành công', 'Xoá thành công phường xã: ' . $ward->name, 'notice');
            $ward->delete();
        }
        return redirect()->route('admin.wards.index');
    }
}
