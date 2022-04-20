<?php
/**
 * Copyright (c) 2020. Electric
 */

use Illuminate\Database\Seeder;
use App\Modules\Operators\Models\Repositories\Contracts\WardsInterface;
use App\Modules\Operators\Models\Repositories\Contracts\ProvincesInterface;
use App\Modules\Operators\Models\Repositories\Contracts\DistrictsInterface;

class AdministrativeUnitKeywordSeeder extends Seeder
{
    protected $_wardsInterface;
    protected $_provincesInterface;
    protected $_districtsInterface;

    public function __construct(WardsInterface $wardsInterface,
                                ProvincesInterface $provincesInterface,
                                DistrictsInterface $districtsInterface)
    {
        $this->_wardsInterface = $wardsInterface;
        $this->_provincesInterface = $provincesInterface;
        $this->_districtsInterface = $districtsInterface;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = $this->_wardsInterface->getAll();
        foreach ($records as $record) {
            $this->_wardsInterface->updateById(
                $record->id,
                array(
                    'keyword' => $record->alias . ' ' . $record->short_name . ' ' . $record->name
                )
            );
        }
        $records = $this->_provincesInterface->getAll();
        foreach ($records as $record) {
            $this->_provincesInterface->updateById(
                $record->id,
                array(
                    'keyword' => $record->alias . ' ' . $record->short_name . ' ' . $record->name
                )
            );
        }
        $records = $this->_districtsInterface->getAll();
        foreach ($records as $record) {
            $this->_districtsInterface->updateById(
                $record->id,
                array(
                    'keyword' => $record->alias . ' ' . $record->short_name . ' ' . $record->name
                )
            );
        }
    }
}
