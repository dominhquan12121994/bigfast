<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Systems\Controllers\Admin;

use App\Modules\Systems\Models\Entities\SystemLog;
use Illuminate\Http\Request;
use App\Modules\Systems\Models\Entities\User;
use App\Modules\Orders\Models\Entities\OrderShop;

class SystemLogController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //crud: create, read, update, delete
        $create = SystemLog::create([
            'log_name' => 'quando',
            'description' => 'quando',
            'user_id' => 'quando',
            'user_type' => 'quando',
            'method' => 'quando',
            'request' => 'quando',
            'data' => 'quando',
            'ip' => 'quando',
            'agent' => 'quando',
            'date' => 'quando',
            'order_id' => 'quando',
        ]);
        dd(create);
        $read = SystemLog::where('ip', '=', 'quando');
        $update = SystemLog::where('ip', '=', 'quando')
            ->update(array(
                'data' => 'updated'
            ));
        $delete = SystemLog::where('ip', '=', 'quando')
            ->delete();






        $limit = 10;
        $search['user_type'] = '';
        $search['method'] = $request->input('method', '');
        $search['log_name'] = $request->input('log_name', null);
        $search['description'] = $request->input('description', null);
        $shop = null;
        $user = null;

        $log_system = SystemLog::whereBetween('date', [ (int)date('Ymd', strtotime('-700 day')), (int)date('Ymd') ]);
        if ( $request->has('user_type') ) {
            $search['user_type'] = $request->input('user_type', 'user');
            $log_system = $log_system->where('user_type', $search['user_type']);

            if ( $request->has('user_id') && $search['user_type'] == 'user' ) {
                $id = (int)$request->input('user_id', '');
                $log_system = $log_system->where('user_id', $id );
                $getUser = User::find($id);
                $user = [
                    'id' => $id,
                    'name' => $getUser ? $getUser->name : 'N/A',
                ];
            }

            if ( $request->has('shop_id') && $search['user_type'] == 'shop' ) {
                $id = (int)$request->input('shop_id', '');
                $log_system = $log_system->where('user_id',  (int)$request->input('shop_id', '') );
                $getShop = OrderShop::find($id);
                $shop = [
                    'id' => $id,
                    'name' => $getShop ? $getShop->name : 'N/A',
                ];
            }
        }
        if ( $search['method'] != '' ) {
            $log_system = $log_system->where('method', $search['method']);
        }
        if ($request->filled('log_name')) {
            $log_system = $log_system->where('log_name', $search['log_name']);
        }
        if ($request->filled('description')) {
            $log_system = $log_system->where('description','LIKE', '%'.$search['description'].'%');
        }
        $log_system = $log_system->orderBy('created_at', 'desc')->paginate($limit);

        return view('Systems::logs.index', [
            'log_system' => $log_system,
            'search' => $search,
            'user' => $user,
            'shop' => $shop,
        ]);
    }
}
