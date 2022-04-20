<?php
/**
 * Copyright (c) 2020. Electric
 */

use Illuminate\Database\Seeder;
use App\Modules\Orders\Models\Repositories\Contracts\ShopBankInterface;

class ShopPurposeScaleSeeder extends Seeder
{
    protected $_shopBankInterface;

    public function __construct(ShopBankInterface $shopBankInterface)
    {
        $this->_shopBankInterface = $shopBankInterface;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = $this->_shopBankInterface->getAll();
        foreach ($records as $record) {
            if (!$record->purpose) {
                $this->_shopBankInterface->updateById(
                    $record->id,
                    array(
                        'purpose' => 'unknown'
                    )
                );
            }
            if (!$record->scale) {
                $this->_shopBankInterface->updateById(
                    $record->id,
                    array(
                        'scale' => -1
                    )
                );
            }
        }
    }
}
