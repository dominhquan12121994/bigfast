<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Operators\Controllers\Admin;

use Validator;
use Illuminate\Http\Request;

use App\Http\Controllers\Admin\AbstractAdminController;
use App\Modules\Operators\Models\Repositories\Contracts\ProvincesInterface;
use App\Modules\Systems\Events\CreateLogEvents;

class ProvincesController extends AbstractAdminController
{
    protected $_provincesInterface;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ProvincesInterface $provincesInterface)
    {
        parent::__construct();

        $this->_provincesInterface = $provincesInterface;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $you = auth('admin')->user();
        if (!$you->can('action_provinces_view'))
        {
            abort(403);
        }

        $provinces = $this->_provincesInterface->getMore(array(), array('with' => array('districts')));

        return view('Operators::provinces.list', ['provinces' => $provinces]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $you = auth('admin')->user();
        if (!$you->can('action_provinces_create'))
        {
            abort(403);
        }

        return view('Operators::provinces.create');
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
        if (!$you->can('action_provinces_create'))
        {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'zone' => 'required|in:bac,trung,nam',
            'code' => 'required|numeric|unique:zone_provinces|max:10',
            'name' => 'required|unique:zone_provinces|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }

        $request->merge(array('short_name' => $request->input('name')));
        $model = $this->_provincesInterface->create($request->all());

        //Thêm dữ liệu log
        $log_data[] = [
            'model' => $model,
        ];
        //Lưu log
        event(new CreateLogEvents($log_data, 'provinces', 'provinces_create'));

        \Func::setToast('Thành công', 'Thêm thành công tỉnh thành ' . $request->input('name'), 'notice');
        return redirect()->route('admin.provinces.index');
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
        $province = $this->_provincesInterface->getById($id);

        return view('Operators::provinces.show', ['province' => $province]);
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
        if (!$you->can('action_provinces_update'))
        {
            abort(403);
        }

        $province = $this->_provincesInterface->getById($id);

        return view('Operators::provinces.edit', ['province' => $province]);
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
        if (!$you->can('action_provinces_update'))
        {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'zone' => 'required|in:bac,trung,nam',
            'code' => 'required|numeric|max:10|unique:zone_provinces,code,' . $id,
            'name' => 'required|max:255|unique:zone_provinces,name,' . $id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }

        $request->merge(array('short_name' => $request->input('name')));

        $old_data = $this->_provincesInterface->getById($id);
        $this->_provincesInterface->updateById($id, $request->all());

        //Thêm dữ liệu log
        $log_data[] = [
            'old_data' => $old_data,
        ];
        //Lưu log
        event(new CreateLogEvents($log_data, 'provinces', 'provinces_update'));

        \Func::setToast('Thành công', 'Cập nhật thành công tỉnh thành: ' . $request->input('name'), 'notice');
        return redirect()->route('admin.provinces.index');
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
        if (!$you->can('action_provinces_delete'))
        {
            abort(403);
        }

        $province = $this->_provincesInterface->getById($id);
        if ($province) {
            //Thêm dữ liệu log
            $log_data[] = [
                'old_data' => $province,
            ];
            //Lưu log
            event(new CreateLogEvents($log_data, 'provinces', 'provinces_delete'));

            \Func::setToast('Thành công', 'Xoá thành công tỉnh thành: ' . $province->name, 'notice');
            $province->delete();
        }
        return redirect()->route('admin.provinces.index');
    }
}
