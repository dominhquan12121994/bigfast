<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Reports\Models\Services;

use DB;
use Exception;
use Throwable;
use Illuminate\Support\Collection;
use Illuminate\Support\MessageBag;

use App\Modules\Orders\Models\Repositories\Contracts\ReportsByZoneInterface;
use App\Modules\Operators\Models\Repositories\Contracts\DistrictsInterface;
use App\Modules\Operators\Models\Repositories\Contracts\ProvincesInterface;

use App\Modules\Orders\Models\Entities\OrderShipAssigned;
use App\Modules\Orders\Models\Entities\ReportsByStatus;

use App\Modules\Orders\Constants\OrderConstant;

use App\Modules\Orders\Models\Entities\Orders;

class ReportServices
{
    protected $_reportsByZoneInterface;
    protected $_districtsInterface;
    protected $_provincesInterface;

    public function __construct(ReportsByZoneInterface $reportsByZoneInterface,
                                DistrictsInterface $districtsInterface,
                                ProvincesInterface $provincesInterface)
    {
        $this->_reportsByZoneInterface = $reportsByZoneInterface;
        $this->_districtsInterface = $districtsInterface;
        $this->_provincesInterface = $provincesInterface;
    }

    public function reportOrderByDistricts($shop_id = 0, $beginDate, $endDate)
    {
        try {
            //Lấy danh sách đơn theo ID người nhập đơn, ngày tạo
            $beginDate = date('Ymd', strtotime($beginDate));
            $endDate = date('Ymd', strtotime($endDate));
            $condition = [];
            if ( $shop_id != 0 ) {
                $condition['shop_id'] = $shop_id;
            }
            $condition['created_range'] = [(int)$beginDate, (int)$endDate];
            $static = $this->_reportsByZoneInterface->getMore($condition, array('with' => 'districts'));
            //Lấy danh sách Quận huyện
            $listD = $static->pluck('d_id');
            //Lấy danh sách Tỉnh (2 mảng //)
            $listP = $static->pluck('districts.provinces.id');
            $aryDistrics = [];
            $aryProvinces = [];
            foreach ( $listD as $key => $item ) {
                //Loại bỏ Quận huyện đã tính tổng
                if( isset( $aryDistrics[$item]) ) {
                    continue;
                }
                //Tính tổng đơn theo Quận huyện
                $aryDistrics[$item] = $static->sum(function ($product) use ($item) {
                    if ( $product['d_id'] == $item) {
                        return $product['count'];
                    } else {
                        return 0;
                    }
                });
                //Tính tổng đơn theo Tỉnh
                if (isset ($aryProvinces[$listP[$key]]) ) {
                        $aryProvinces[$listP[$key]] += $aryDistrics[$item];
                } else {
                    $aryProvinces[$listP[$key]] = $aryDistrics[$item];
                }
            };

            //Xếp các Quận/huyện, tỉnh lớn nhất
            arsort($aryDistrics);
            arsort($aryProvinces);

            $result = [];
            $rand = array('#dc3545', '#ffc107', '#117a8b', '#1e7e34', '#545b62');
            //Xử lý dữ liệu quận/huyện
            $top = 0;
            $another = 0;
            $data = [];
            $color = [];
            $charData = [];
            $total = 0;
            foreach ($aryDistrics as $k => $v) {
                $districts = $this->_districtsInterface->getById($k);
                if($districts) {
                    $data[$districts->name] = $v;
                    $getColor = $rand[4];
                    if ($top < 3) {
                        $getColor = $rand[$top];
                        $charData[$districts->name] = $v;
                        $color[$districts->name] = $getColor;
                    } else {
                        $another += (int)$v;
                        $charData['Khác'] = $another;
                        $color['Khác'] = $getColor;
                    }
                    $total += (int)$v;
                    $top++;
                }
            }
            if (count($data) > 1) {
                $new['Tổng'] = $total;
                $data = $new + $data;
            }
            $result['districs'] = [
                'list'      => $data,
                'dataChart' => $charData,
                'color'     => $color,
            ];
            //Xử lý dữ liệu tỉnh
            $top = 0;
            $another = 0;
            $data = [];
            $color = [];
            $charData = [];
            $total = 0;
            foreach ($aryProvinces as $k => $v) {
                $provinces = $this->_provincesInterface->getById($k);
                if($provinces) {
                    $data[$provinces->name] = $v;
                    $getColor = $rand[4];
                    if ($top < 3) {
                        $getColor = $rand[$top];
                        $charData[$provinces->name] = $v;
                        $color[$provinces->name] = $getColor;
                    } else {
                        $another += (int)$v;
                        $charData['Khác'] = $another;
                        $color['Khác'] = $getColor;
                    }
                    $total += (int)$v;
                    $top++;
                }
            }
            if (count($data) > 1) {
                $new['Tổng'] = $total;
                $data = $new + $data;
            }
            $result['provinces'] = [
                'list'      => $data,
                'dataChart' => $charData,
                'color'     => $color,
            ];

            $dataRes = new \stdClass();
            $dataRes->status = true;
            $dataRes->result = $result;

        } catch (\Throwable $e) {
            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->status = false;
            $dataRes->error = $messageBag;
        }

        return $dataRes;
    }

    public function reportOrderByShip($beginDate, $endDate, $statusDetail, $user_role, $user_id = null)
    {
        try {
            $beginDate = date('Ymd', strtotime($beginDate));
            $endDate = date('Ymd', strtotime($endDate));
            $aryDate = [(int)$beginDate, (int)$endDate];

            $data = [];
            $data = self::getAssignByStatus($data, $statusDetail, $aryDate, $user_role, $user_id);

            $dataRes = new \stdClass();
            $dataRes->status = true;
            $dataRes->result = $data;

        } catch (\Throwable $e) {
            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->status = false;
            $dataRes->result = [];
            $dataRes->error = $messageBag;
        }

        return $dataRes;
    }
    
    public function reportOrderByShipForAdmin($beginDate, $endDate, $status, $user_role)
    {
        try {
            $beginDate = date('Ymd', strtotime($beginDate));
            $endDate = date('Ymd', strtotime($endDate));
            $aryDate = [(int)$beginDate, (int)$endDate];

            $result = [];
            $total = OrderShipAssigned::select(DB::raw('user_id, COUNT(id) AS count'))
            ->with(['user','shop'])
            ->where('user_role', $user_role)
            ->where('status_detail', $status)
            ->whereBetween('time_assigned', $aryDate);

            $total = $total->groupBy(['user_id'])
                ->get();

            $success = OrderShipAssigned::select(DB::raw('user_id, COUNT(id) AS count'))
                ->where('user_role', $user_role)
                ->where('status_detail', $status)
                ->whereBetween('time_assigned', $aryDate)
                ->where('time_success','!=', 0);

            $success = $success->groupBy(['user_id'])
                ->get();

            $fail = OrderShipAssigned::select(DB::raw('user_id, COUNT(id) AS count'))
                ->where('user_role', $user_role)
                ->where('status_detail', $status)
                ->whereBetween('time_assigned', $aryDate)
                ->where('time_failed','!=', 0)
                ->where('time_success', 0);

            $fail = $fail->groupBy(['user_id'])
                ->get();

            foreach ($total as $item) {
                if (!isset($result[$item->user_id]['total'])) {
                    $result[$item->user_id]['total'] = $item->count;
                }
                if (!isset($result[$item->user_id]['success'])) {
                    $result[$item->user_id]['success'] = $success->sum(function($product) use ($item) {
                        if ($product->user_id == $item->user_id) {
                            return $product->count;
                        }
                    });
                }
                if (!isset($result[$item->user_id]['fail'])) {
                    $result[$item->user_id]['fail'] = $fail->sum(function($product) use ($item) {
                        if ($product->user_id == $item->user_id) {
                            return $product->count;
                        }
                    });
                }
                if (!isset($result[$item->user_id]['name'])) {
                    $result[$item->user_id]['name'] = $item->user->name;
                }
            }

            $dataRes = new \stdClass();
            $dataRes->status = true;
            $dataRes->result = $result;

        } catch (\Throwable $e) {
            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->status = false;
            $dataRes->result = [];
            $dataRes->error = $messageBag;
        }

        return $dataRes;
    }

    protected function getAssignByStatus($result, $status, $aryDate, $user_role, $user_id = null) {
        $total = OrderShipAssigned::select(DB::raw('user_id, shop_id, COUNT(id) AS count'))
            ->with(['user','shop'])
            ->where('user_role', $user_role)
            ->where('status_detail', $status)
            ->whereBetween('time_assigned', $aryDate);
        if ( $user_id ) {
            $total = $total->where('user_id', (int)$user_id);
        }
        $total = $total->groupBy(['user_id', 'shop_id'])
            ->get();

        $success = OrderShipAssigned::select(DB::raw('user_id, shop_id, COUNT(id) AS count'))
            ->where('user_role', $user_role)
            ->where('status_detail', $status)
            ->whereBetween('time_assigned', $aryDate)
            ->where('time_success','!=', 0);
        if ( $user_id ) {
            $success = $success->where('user_id', (int)$user_id);
        }
        $success = $success->groupBy(['user_id', 'shop_id'])
            ->get();

        $fail = OrderShipAssigned::select(DB::raw('user_id, shop_id, COUNT(id) AS count'))
            ->where('user_role', $user_role)
            ->where('status_detail', $status)
            ->whereBetween('time_assigned', $aryDate)
            ->where('time_failed','!=', 0)
            ->where('time_success', 0);
        if ( $user_id ) {
            $fail = $fail->where('user_id', (int)$user_id);
        }
        $fail = $fail->groupBy(['user_id', 'shop_id'])
            ->get();

        foreach ($total as $item) {
            if (!isset($result[$item->user_id][$item->shop_id]['total'])) {
                $result[$item->user_id][$item->shop_id]['total'] = $total->sum(function($product) use ($item) {
                    if ($product->user_id == $item->user_id && $product->shop_id == $item->shop_id) {
                        return $product->count;
                    }
                });
            }
            if (!isset($result[$item->user_id][$item->shop_id]['success'])) {
                $result[$item->user_id][$item->shop_id]['success'] = $success->sum(function($product) use ($item) {
                    if ($product->user_id == $item->user_id && $product->shop_id == $item->shop_id) {
                        return $product->count;
                    }
                });
            }
            if (!isset($result[$item->user_id][$item->shop_id]['fail'])) {
                $result[$item->user_id][$item->shop_id]['fail'] = $fail->sum(function($product) use ($item) {
                    if ($product->user_id == $item->user_id && $product->shop_id == $item->shop_id) {
                        return $product->count;
                    }
                });
            }
            if (!isset($result[$item->user_id][$item->shop_id]['name'])) {
                $result[$item->user_id][$item->shop_id]['name'] = $item->user->name;
            }
            if (!isset($result[$item->user_id][$item->shop_id]['shop'])) {
                $result[$item->user_id][$item->shop_id]['shop'] = $item->shop->name;
            }
        }

        return $result;
    }

    public function reportOrderByStatus($shop_id, $beginDate, $endDate) {
        try {
            $beginDate = (int)date('Ymd', strtotime($beginDate));
            $endDate = (int)date('Ymd', strtotime($endDate));
            $aryDate = [$beginDate, $endDate];
            $key = 'Report_status_' . $shop_id . '_' . $beginDate . '_' . $endDate;
            if (\Cache::has($key)) {
                $data = \Cache::get($key);
            } else {
                $data = [];
                $orders = Orders::select(DB::raw('shop_id, status_detail, COUNT(id) AS count'));
                if ($shop_id != 0) {
                    $orders = $orders->where('shop_id', (int)$shop_id);
                }
                $orders = $orders->whereBetween('created_date', $aryDate)
                    ->groupBy(['shop_id', 'status_detail'])
                    ->get();
                foreach ($orders as $item) {
                    $data[$item->shop_id][$item->status_detail] = $item->count;
                }
                \Cache::put($key, $data, now()->addMinutes(5));
            }

            $dataRes = new \stdClass();
            $dataRes->status = true;
            $dataRes->result = $data;

        } catch (\Throwable $e) {
            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->status = false;
            $dataRes->error = $messageBag;
        }

        return $dataRes;
    }
}
