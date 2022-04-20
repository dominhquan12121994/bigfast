<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Helpers;

use Illuminate\Database\Eloquent\Collection;
use App\Modules\Operators\Models\Entities\ZoneProvinces;
use App\Modules\Operators\Models\Entities\ZoneDistricts;
use App\Modules\Operators\Models\Entities\ZoneWards;

class AddressHelper
{
    public static function mappingAddress($address)
    {
        try {
            $arrZone = explode(',', $address);
            if (count($arrZone) < 3) {
                throw new \Exception('Địa chỉ không chính xác');
            }

            $listProvinces = ZoneProvinces::searchByQuery(array(
                    'multi_match' => [
                        'query' => trim($arrZone[2]),
                        'fields' => ['short_name', 'alias']
                    ]
                )
            );
            $listDistricts = ZoneDistricts::searchByQuery(array(
                    'multi_match' => [
                        'query' => trim($arrZone[1]),
                        'fields' => ['short_name', 'alias']
                    ]
                )
            );
            $listWards = ZoneWards::searchByQuery(array(
                    'multi_match' => [
                        'query' => trim($arrZone[0]),
                        'fields' => ['short_name', 'alias']
                    ]
                )
            );
        } catch (\Exception $e) {
            $arrZone = explode(',', $address);
            $listProvinces = ZoneProvinces::getUniform(array(
                'table' => 'zone_provinces',
                'column' => 'keyword',
                'keyword' => trim($arrZone[2]),
            ));
            $listDistricts = ZoneDistricts::getUniform(array(
                'table' => 'zone_districts',
                'column' => 'keyword',
                'keyword' => trim($arrZone[1]),
            ));
            $listWards = ZoneWards::getUniform(array(
                'table' => 'zone_wards',
                'column' => 'keyword',
                'keyword' => trim($arrZone[0]),
            ));
        }

        $arrData = array();
        $arrMatch = array();
        $arrPoint = array();
        $arrDataProvinces = array();
        $arrDataDistricts = array();

        $results = new Collection($listProvinces);
        $results->each(function ($province, $key) use (&$arrData, &$arrDataProvinces) {
            $arrData[$province->id] = array();
            $arrDataProvinces[$province->id] = array('point' => (10 - $key) * 3, 'name' => $province->name);
        });

        $results = new Collection($listDistricts);
        $results->each(function ($district, $key) use (&$arrData, &$arrDataDistricts) {
            if (isset($arrData[$district->p_id])) {
                $arrData[$district->p_id][$district->id] = array();
                $arrDataDistricts[$district->id] = array('point' => (10 - $key) * 2, 'name' => $district->name);
            }
        });

        $results = new Collection($listWards);
        $results->each(function ($ward, $key) use (&$arrData, &$arrPoint, &$arrMatch, &$arrDataProvinces, &$arrDataDistricts) {
            if (isset($arrData[$ward->p_id])) {
                if (isset($arrData[$ward->p_id][$ward->d_id])) {
                    $wardPoint = (10 - $key) * 1;
                    $arrData[$ward->p_id][$ward->d_id][$ward->id] = $ward->name;
                    $arrMatch[$ward->p_id . '-' . $ward->d_id . '-' . $ward->id] = $arrDataProvinces[$ward->p_id]['point'] + $arrDataDistricts[$ward->d_id]['point'] + $wardPoint;
                    $arrPoint[] = $arrDataProvinces[$ward->p_id]['point'] + $arrDataDistricts[$ward->d_id]['point'] + $wardPoint;
                }
            }
        });

        $maxPoint = 0;
        if (count($arrPoint) > 0) {
            rsort($arrPoint);
            $maxPoint = $arrPoint[0];
        }

        $dataMatch = array_search($maxPoint, $arrMatch);
        if ($dataMatch) {
            $dataMatch = explode('-' , $dataMatch);
        }
        if (is_array($dataMatch)) {
            if (count($dataMatch) !== 3) {
                $dataMatch = self::mappingAddressAgain($address);
            }
        } else {
			$dataMatch = self::mappingAddressAgain($address);
		}
        return $dataMatch;
    }

    public static function mappingAddressAgain($address)
    {
        $arrZone = explode(',', $address);
        $listProvinces = ZoneProvinces::getUniform(array(
            'table' => 'zone_provinces',
            'column' => 'keyword',
            'keyword' => trim($arrZone[2]),
        ));
        $listDistricts = ZoneDistricts::getUniform(array(
            'table' => 'zone_districts',
            'column' => 'keyword',
            'keyword' => trim($arrZone[1]),
        ));
        $listWards = ZoneWards::getUniform(array(
            'table' => 'zone_wards',
            'column' => 'keyword',
            'keyword' => trim($arrZone[0]),
        ));

        $arrData = array();
        $arrMatch = array();
        $arrPoint = array();
        $arrDataProvinces = array();
        $arrDataDistricts = array();

        $results = new Collection($listProvinces);
        $results->each(function ($province, $key) use (&$arrData, &$arrDataProvinces) {
            $arrData[$province->id] = array();
            $arrDataProvinces[$province->id] = array('point' => (10 - $key) * 3, 'name' => $province->name);
        });

        $results = new Collection($listDistricts);
        $results->each(function ($district, $key) use (&$arrData, &$arrDataDistricts) {
            if (isset($arrData[$district->p_id])) {
                $arrData[$district->p_id][$district->id] = array();
                $arrDataDistricts[$district->id] = array('point' => (10 - $key) * 2, 'name' => $district->name);
            }
        });

        $results = new Collection($listWards);
        $results->each(function ($ward, $key) use (&$arrData, &$arrPoint, &$arrMatch, &$arrDataProvinces, &$arrDataDistricts) {
            if (isset($arrData[$ward->p_id])) {
                if (isset($arrData[$ward->p_id][$ward->d_id])) {
                    $wardPoint = (10 - $key) * 1;
                    $arrData[$ward->p_id][$ward->d_id][$ward->id] = $ward->name;
                    $arrMatch[$ward->p_id . '-' . $ward->d_id . '-' . $ward->id] = $arrDataProvinces[$ward->p_id]['point'] + $arrDataDistricts[$ward->d_id]['point'] + $wardPoint;
                    $arrPoint[] = $arrDataProvinces[$ward->p_id]['point'] + $arrDataDistricts[$ward->d_id]['point'] + $wardPoint;
                }
            }
        });

        $maxPoint = 0;
        if (count($arrPoint) > 0) {
            rsort($arrPoint);
            $maxPoint = $arrPoint[0];
        }

        $dataMatch = array_search($maxPoint, $arrMatch);
        if ($dataMatch) {
            $dataMatch = explode('-' , $dataMatch);
        }
        return $dataMatch;
    }
}
