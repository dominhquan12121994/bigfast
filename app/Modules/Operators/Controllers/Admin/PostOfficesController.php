<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Operators\Controllers\Admin;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AbstractAdminController;
use App\Rules\ExceptSpecialCharRule;

use App\Modules\Operators\Models\Repositories\Contracts\WardsInterface;
use App\Modules\Operators\Models\Repositories\Contracts\DistrictsInterface;
use App\Modules\Operators\Models\Repositories\Contracts\ProvincesInterface;
use App\Modules\Operators\Models\Repositories\Contracts\PostOfficesInterface;
use App\Modules\Systems\Events\CreateLogEvents;

class PostOfficesController extends AbstractAdminController
{
    protected $_postOfficesInterface;
    protected $_provincesInterface;
    protected $_districtsInterface;
    protected $_wardsInterface;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PostOfficesInterface $postOfficesInterface,
                                ProvincesInterface $provincesInterface,
                                DistrictsInterface $districtsInterface,
                                WardsInterface $wardsInterface)
    {
        parent::__construct();

        $this->_postOfficesInterface = $postOfficesInterface;
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
        if (!$you->can('action_post_offices_view'))
        {
            abort(403);
        }

        $postOffices = $this->_postOfficesInterface->getMore(array(), array(), 10);

        return view('Operators::post-offices.list', ['postOffices' => $postOffices]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $you = auth('admin')->user();
        if (!$you->can('action_post_offices_create'))
        {
            abort(403);
        }

        $provinces = $this->_provincesInterface->getMore();
        $districts = $this->_districtsInterface->getMore(array('p_id' => $provinces[0]->id));
        $wards = $this->_wardsInterface->getMore(array('d_id' => $districts[0]->id));

        return view('Operators::post-offices.create', [
            'provinces' => $provinces,
            'districts' => $districts,
            'wards' => $wards
        ]);
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
        if (!$you->can('action_post_offices_create'))
        {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'p_id' => 'required',
            'd_id' => 'required',
            'w_id' => 'required',
            'name' => array('required', 'max:255', new ExceptSpecialCharRule()),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $postOffices = $this->_postOfficesInterface->create($request->all());

        //Thêm dữ liệu log
        $log_data[] = [
            'model' => $postOffices,
        ];
        //Lưu log 
        event(new CreateLogEvents($log_data, 'post_offices', 'post_offices_create'));

        \Func::setToast('Thành công', 'Thêm mới thành công bưu cục', 'notice');
        return redirect()->route('admin.post-offices.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_post_offices_view'))
        {
            abort(403);
        }

        $postOffices = $this->_postOfficesInterface->getById($id);

        return view('Operators::post-offices.show', ['postOffices' => $postOffices]);
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
        if (!$you->can('action_post_offices_update'))
        {
            abort(403);
        }

        $postOffice = $this->_postOfficesInterface->getById($id);
        $provinces = $this->_provincesInterface->getMore();
        $districts = $this->_districtsInterface->getMore(array('p_id' => $postOffice->p_id));
        $wards = $this->_wardsInterface->getMore(array('d_id' => $postOffice->d_id));

        return view('Operators::post-offices.edit', [
            'postOffice' => $postOffice,
            'wards' => $wards,
            'provinces' => $provinces,
            'districts' => $districts
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
        if (!$you->can('action_post_offices_update'))
        {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'p_id' => 'required',
            'd_id' => 'required',
            'w_id' => 'required',
            'name' => array('required', 'max:255', new ExceptSpecialCharRule()),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $postOffices = $this->_postOfficesInterface->getById($id);
        $this->_postOfficesInterface->updateById($id, $request->all());

        //Thêm dữ liệu log
        $log_data[] = [
            'old_data' => $postOffices,
        ];
        //Lưu log 
        event(new CreateLogEvents($log_data, 'post_offices', 'post_offices_update'));

        \Func::setToast('Thành công', 'Cập nhật thành công bưu cục', 'notice');
        return redirect()->route('admin.post-offices.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_post_offices_delete'))
        {
            abort(403);
        }

        $ward = $this->_postOfficesInterface->getById($id);
        if ($ward) {
            //Thêm dữ liệu log
            $log_data[] = [
                'old_data' => $ward,
            ];
            //Lưu log 
            event(new CreateLogEvents($log_data, 'post_offices', 'post_offices_delete'));

            $ward->delete();
        }
        return redirect()->route('admin.post-offices.index');
    }
}
