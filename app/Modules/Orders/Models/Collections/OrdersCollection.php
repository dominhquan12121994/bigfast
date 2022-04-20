<?php

namespace App\Modules\Orders\Models\Collections;

use Elasticquent\ElasticquentCollectionTrait;
use \Illuminate\Database\Eloquent\Collection;
use App\Modules\Orders\Models\Entities\Orders;

class OrdersCollection extends Collection
{
//    use ElasticquentCollectionTrait;

    public function addToIndex()
    {
        // sync to elastic search
//        $orderFind = Orders::searchByQuery(array('match' => array('lading_code' => $this->lading_code)));
        dd($this);
    }
}