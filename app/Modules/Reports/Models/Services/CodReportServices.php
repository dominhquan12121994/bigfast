<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Reports\Models\Services;

use Exception;
use Throwable;
use Illuminate\Support\MessageBag;
use App\Modules\Reports\Models\Repositories\Contracts\CodReportInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderShopReconcileInterface;
use Carbon\Carbon;

class CodReportServices
{
    protected $_codReportInterface;
    protected $_orderShopReconcileInterface;

    public function __construct(CodReportInterface $codReportInterface,
                                OrderShopReconcileInterface $orderShopReconcileInterface)
    {
        $this->_codReportInterface = $codReportInterface;
        $this->_orderShopReconcileInterface = $orderShopReconcileInterface;
    }

    public function getCodReport($payload = array()) {
        $beginDate = (int)date('Ymd', strtotime($payload['date_range'][0]));
        $endDate = (int)date('Ymd', strtotime($payload['date_range'][1]));

        if ($payload['shop_id'] == 0) {
            $initData = array(
                'date_range' => array($beginDate, $endDate),
            );
        } else {
            $initData = array(
                'shop_id' => $payload['shop_id'],
                'date_range' => array($beginDate, $endDate),
            );
        }

        $datas = $this->_orderShopReconcileInterface->getMore($initData);

        $dataRes = array();

        if ($payload['date_type'] == 'day') {
            $begin = date('d-m-Y', strtotime($payload['date_range'][0]));
            $end = date('d-m-Y', strtotime($payload['date_range'][1]));
            $diff = date_diff(date_create($begin), date_create($end));

            for ($i = 0; $i <= $diff->days; $i++) {
                $day = date('Ymd', strtotime($begin . ' +' . $i . ' day'));
                $arrResult[$day]['totalCod'] = 0;
                $arrResult[$day]['moneyIndemnify'] = 0;
                $arrResult[$day]['totalFee'] = 0;
                $arrResult[$day]['giveShop'] = 0;
            }

            foreach ($datas as $data) {
                $arrResult[$data->date]['totalCod'] += $data->total_cod;
                $arrResult[$data->date]['moneyIndemnify'] += $data->money_indemnify;
                $arrResult[$data->date]['totalFee'] +=
                    $data->fee_transport
                    + $data->fee_insurance
                    + $data->fee_cod
                    + $data->fee_refund
                    + $data->fee_store
                    + $data->fee_chang_info
                    + $data->fee_transfer;
                $arrResult[$data->date]['giveShop'] =
                    $arrResult[$data->date]['totalCod']
                    + $arrResult[$data->date]['moneyIndemnify']
                    - $arrResult[$data->date]['totalFee'];
            }

            foreach ($arrResult as $key => $value) {
                $newKey = substr($key, 6, 2) . '-' . substr($key, 4, 2) . '-' . substr($key, 0, 4);
                $dataRes[$newKey] = $value;
            }

        } else if (($payload['date_type'] == 'week')) {
            $beginWeek = date('W', strtotime($beginDate));
            if ($beginWeek > 10 && substr($beginDate, 4, 2) == 1) {
                $beginWeek = 1;
            }
            $endWeek = date('W', strtotime($endDate));
            if ($endWeek > 10 && substr($endWeek, 4, 2) == 1) {
                $endWeek = 1;
            }

            for ($i = 0; $i <= ($endWeek - $beginWeek); $i++) {
                $arrResult[($i + $beginWeek)]['totalCod'] = 0;
                $arrResult[($i + $beginWeek)]['moneyIndemnify'] = 0;
                $arrResult[($i + $beginWeek)]['totalFee'] = 0;
                $arrResult[($i + $beginWeek)]['giveShop'] = 0;
            }

            foreach ($datas as $data) {
                $date = $data->date;
                $week = date('W', strtotime($date));
                $arrResult[$week]['totalCod'] += $data->total_cod;
                $arrResult[$week]['moneyIndemnify'] += $data->money_indemnify;
                $arrResult[$week]['totalFee'] +=
                    $data->fee_transport
                    + $data->fee_insurance
                    + $data->fee_cod
                    + $data->fee_refund
                    + $data->fee_store
                    + $data->fee_chang_info
                    + $data->fee_transfer;
                $arrResult[$week]['giveShop'] =
                    $arrResult[$week]['totalCod']
                    + $arrResult[$week]['moneyIndemnify']
                    - $arrResult[$week]['totalFee'];
            }
            $carbonDate = Carbon::now();
            foreach ($arrResult as $key => $value) {
                $newKey = 'Tuần ' . $key;
                $carbonDate->setISODate(substr($beginDate, 0, 4), $key);
                $value['dateRange'] = '(' . $carbonDate->startOfWeek()->format('d/m') . ' - ' . $carbonDate->endOfWeek()->format('d/m') . ')';
                $dataRes[$newKey] = $value;
            }

        } else if (($payload['date_type'] == 'month')) {
            $begin = date('d-m-Y', strtotime($payload['date_range'][0]));
            $end = date('d-m-Y', strtotime($payload['date_range'][1]));
            $diff = date_diff(date_create($begin), date_create($end));
            $getMonth = ($diff->y * 12) + $diff->m;
            if ($getMonth == 0 && date('m/Y', strtotime($begin)) != date('m/Y', strtotime($end)) ) {
                $getMonth++;
            }

            for ($i = 0; $i <= $getMonth; $i++) {
                $month = date('m/Y', strtotime($begin . ' +' . $i . ' month'));
                $arrResult[$month]['totalCod'] = 0;
                $arrResult[$month]['moneyIndemnify'] = 0;
                $arrResult[$month]['totalFee'] = 0;
                $arrResult[$month]['giveShop'] = 0;
            }

            foreach ($datas as $data) {
                $date = $data->date;
                $month = substr($date, 4, 2) . '/' . substr($date, 0, 4);
                $arrResult[$month]['totalCod'] += $data->total_cod;
                $arrResult[$month]['moneyIndemnify'] += $data->money_indemnify;
                $arrResult[$month]['totalFee'] +=
                    $data->fee_transport
                    + $data->fee_insurance
                    + $data->fee_cod
                    + $data->fee_refund
                    + $data->fee_store
                    + $data->fee_chang_info
                    + $data->fee_transfer;
                $arrResult[$month]['giveShop'] =
                    $arrResult[$month]['totalCod']
                    + $arrResult[$month]['moneyIndemnify']
                    - $arrResult[$month]['totalFee'];
            }

            foreach ($arrResult as $key => $value) {
                $newKey = 'Tháng ' . $key;
                $dataRes[$newKey] = $value;
            }

        }

        return $dataRes;
    }
}
