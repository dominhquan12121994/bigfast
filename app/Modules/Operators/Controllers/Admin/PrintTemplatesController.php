<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Operators\Controllers\Admin;

use Illuminate\Validation\Rule;
use Auth;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AbstractAdminController;

use App\Modules\Operators\Models\Repositories\Contracts\PrintTemplatesInterface;

use App\Modules\Operators\Constants\PrintTemplatesConstant;

use App\Modules\Operators\Models\Services\PrintTemplatesServices;

use Picqer\Barcode\BarcodeGeneratorHTML;
use App\Modules\Systems\Events\CreateLogEvents;

class PrintTemplatesController extends AbstractAdminController
{

    protected $_printTemplatesInterface;
    protected $_printTemplatesServices;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PrintTemplatesInterface $printTemplatesInterface, PrintTemplatesServices $printTemplatesServices)
    {
        parent::__construct();
        $this->_printTemplatesInterface = $printTemplatesInterface;
        $this->_printTemplatesServices = $printTemplatesServices;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_print_template_view'))
        {
            abort(403);
        }

        $size = PrintTemplatesConstant::size;
        $filter['page'] = isset($request->page) ? $request->page : array_key_first($size);
        $filter['type'] = 'doc';
        if (isset($request->type)) {
            $filter['type'] = $request->type;
        }
        $setting = $this->_printTemplatesInterface->getOne(array('page_size' => $filter['page'], 'type' => $filter['type'] ));
        if ($setting) $setting = $this->_printTemplatesServices->convertCode($setting);
        $listCode = $this->_printTemplatesServices->listCode();
        if (!isset($setting['data'][0])) {
            return abort(404);
        }

        return view('Operators::print-templates.index', [
            'types'     => $size,
            'key_words' => PrintTemplatesConstant::key_word,
            'setting'   => $setting['data'][0],
            'dataSize' => PrintTemplatesConstant::size[$filter['page']][$setting['data'][0]['type']],
            'listCode'  => $listCode,
            'filter'    => $filter
        ]);
    }

    public function update(Request $request)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_print_template_update'))
        {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'page_size' => [
                'required',
                Rule::in(array_keys(PrintTemplatesConstant::size)),
            ],
            'type' => [
                'required',
            ],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $conditions = array('page_size' => $request->page_size, 'type' => $request->type);
        $filldata = $request->only(['page_size', 'html']);

        $setting = $this->_printTemplatesInterface->updateByCondition($conditions, $filldata);

        //Thêm dữ liệu log
        $log_data[] = [
            'old_data' => $setting,
        ];
        //Lưu log 
        event(new CreateLogEvents($log_data, 'print_template', 'print_template_update'));

        \Func::setToast('Thành công', 'Sửa mẫu in thành công !', 'notice');
        return redirect()->back();
    }

    public function preview(Request $request) {
        $you = auth('admin')->user();
        if (!$you->can('action_print_template_view'))
        {
            abort(403);
        }

        if (!isset($request->pageSize) || !isset($request->type) || !isset($request->htmlnew)) {
            abort(404);
        }

        return view('Operators::print-templates.preview', [
            'html' => $request->htmlnew, 
            'dataSize' => PrintTemplatesConstant::size[$request->pageSize][$request->type] 
        ]);
    }

    public function print(Request $request) {
        $you = auth('admin')->user();
        if (!$you->can('action_orders_print'))
        {
            abort(403);
        }

        $per_page = 1;
        $type = 'doc';
        if (isset($request->per_page)) {
            $per_page = $request->per_page;
        }
        if (isset($request->type)) {
            $type = $request->type;
        }
        if (!isset($request->page_size) || !isset($request->order_id)) {
            return abort(404);
        }
        
        $filter = $request->only(['page_size', 'order_id']);
        
        //Xử lý lấy mẫu in tương ứng với page_size, type, per_page
        if ( !isset ( PrintTemplatesConstant::per_page[$filter['page_size']][$per_page]) ) {
            return abort(404);
        }
        $print_info = PrintTemplatesConstant::per_page[$filter['page_size']][$per_page][$type];
        $page_size = $print_info['kho_giay'];
        $size = $print_info['mau_in'];
        $page_landscape = $print_info['xoay'];

        $print_size = $this->_printTemplatesInterface->getOne(array('page_size' => $size, 'type' => $type ));
        if (!$print_size) {
            return abort(404);
        }
        $print_data = $this->_printTemplatesServices->convertCode($print_size, $filter['order_id'], true);
        $number_order = count($print_data['data']);

        //Xử lý phân bản in mỗi trang
        $print_html = [];
        $indexAry = 0;
        foreach ($print_data['data'] as $key => $val) {
            $print_html[$indexAry][] = $val;
            if ( count($print_html[$indexAry]) == $per_page) {
                $indexAry ++;
            }
        }

        //Xử lý lấy độ dài độ rộng của mẫu và con của mẫu
        $cssPage = PrintTemplatesConstant::size[$request->page_size][$page_size];
        $css['page'] = [
            'width' => (int)$cssPage['width'],
            'height' => (int)$cssPage['height'],
        ];
        $cssPageChild = PrintTemplatesConstant::size[$size][$type];
        $css['page_children'] = [
            'width' => (int)$cssPageChild['width'],
            'height' => (int)$cssPageChild['height'],
        ];

        return view('Operators::print-templates.print', [
            'html' => $print_html, 
            'css' => $css,
            'per_page' => PrintTemplatesConstant::per_page,
            'current_per_page' => $per_page,
            'current_type' => $type,
            'number_order' => $number_order,
            'page_landscape' => $page_landscape,
            'filter' => $filter
        ]);
    }
}
