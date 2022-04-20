<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Shops\Controllers\Web;

use Illuminate\Validation\Rule;
use Auth;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Web\AbstractWebController;

use App\Modules\Operators\Models\Repositories\Contracts\PrintTemplatesInterface;

use App\Modules\Operators\Constants\PrintTemplatesConstant;

use App\Modules\Operators\Models\Services\PrintTemplatesServices;

use Picqer\Barcode\BarcodeGeneratorHTML;

class PrintTemplatesController extends AbstractWebController
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

    public function print(Request $request) {
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

        return view('Shops::print-templates.print', [
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
