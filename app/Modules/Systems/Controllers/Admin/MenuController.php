<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Systems\Controllers\Admin;

use Illuminate\Http\Request;
use App\Modules\Systems\Models\Entities\Menulist;
use App\Modules\Systems\Models\Entities\Menus;

class MenuController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function index(Request $request){
        return view('Systems::editmenu.menu.index', array(
            'menulist'  => Menulist::all()
        ));
    }

    public function create(){
        return view('Systems::editmenu.menu.create',[]);
    }

    public function store(Request $request){
        $validatedData = $request->validate([
            'name' => 'required|min:1|max:64'
        ]);
        $menulist = new Menulist();
        $menulist->name = $request->input('name');
        $menulist->save();
        \Func::setToast('Thành công', 'Thêm thành công menu', 'notice');
        return redirect()->route('admin.menu.menu.create');
    }

    public function edit(Request $request){
        return view('Systems::editmenu.menu.edit',[
            'menulist'  => Menulist::where('id', '=', $request->input('id'))->first()
        ]);
    }

    public function update(Request $request){
        $validatedData = $request->validate([
            'id'   => 'required',
            'name' => 'required|min:1|max:64'
        ]);
        $menulist = Menulist::where('id', '=', $request->input('id'))->first();
        $menulist->name = $request->input('name');
        $menulist->save();
        \Func::setToast('Thành công', 'Cập nhật thành công menu', 'notice');
        return redirect()->route('admin.menu.menu.edit', ['id'=>$request->input('id')]);
    }

    /*
    public function show(Request $request){
        return view('Systems::editmenu.menu.show',[
            'menulist'  => Menulist::where('id', '=', $request->input('id'))->first()
        ]);
    }
    */

    public function delete(Request $request){
        $menus = Menus::where('menu_id', '=', $request->input('id'))->first();
        if(!empty($menus)){
            $request->session()->flash('message', "Can't delete. This menu have assigned menu elements");
            $request->session()->flash('back', 'admin.menu.menu.index');
            return view('Systems::shared.universal-info');
        }else{
            Menulist::where('id', '=', $request->input('id'))->delete();
            $request->session()->flash('message', 'Successfully deleted menu');
            $request->session()->flash('back', 'admin.menu.menu.index');
            return view('Systems::shared.universal-info');
        }
    }

}
