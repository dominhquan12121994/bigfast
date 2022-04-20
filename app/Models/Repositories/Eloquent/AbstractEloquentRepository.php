<?php

namespace App\Models\Repositories\Eloquent;

use App\Models\Repositories\Contracts\AbstractEloquentInterface;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractEloquentRepository implements AbstractEloquentInterface
{
    protected $_model;

    public function __construct()
    {
        $this->_setModel();
    }

    abstract protected function _getModel();

    protected function _setModel(){
        $this->_model = app()->make(
            $this->_getModel()
        );
    }

    public function getAll(){
        return $this->_model->all();
    }

    public function getById($id, $conditions = array(), $fetchOptions = array()){
        if ($id) {
            $conditions = array_merge($conditions, array('id' => (int)$id));
            return $this->getOne($conditions, $fetchOptions);
        } else {
            return false;
        }
    }

    public function getOne($conditions = array(), $fetchOptions = array()){
        $query = isset($fetchOptions['with']) ? $this->_model->with($fetchOptions['with']) : $this->_model;
        $query = isset($fetchOptions['withTrashed']) ? $query->withTrashed()->newQuery() : $query->newQuery();
        $query = $this->_prepareConditions($conditions, $query);
        $query = $this->_prepareFetchOptions($fetchOptions, $query);

        return $query->first();
    }

    public function getMore($conditions = array(), $fetchOptions = array(), $paging = false){
        $query = isset($fetchOptions['with']) ? $this->_model->with($fetchOptions['with']) : $this->_model;
        $query = isset($fetchOptions['withTrashed']) ? $query->withTrashed()->newQuery() : $query->newQuery();
        $query = $this->_prepareConditions($conditions, $query);
        $query = $this->_prepareFetchOptions($fetchOptions, $query);
        if($paging){
            return $query->paginate((int)$paging);
        }

        return $query->get();
    }

    public function delByCond($conditions = array()){
        $query = $this->_model->newQuery();
        $query = $this->_prepareConditions($conditions, $query);

        return $query->delete();
    }

    public function create($fillData = array()){
        return $this->_model->create($fillData);
    }

    public function insert($listData = array())
    {
        return $this->_model->insert($listData);
    }

    public function updateByCondition($conditions = array(), $fillData = array(), $fetchOptions = array(), $updateMore = false)
    {
        if($updateMore){
            $query = isset($fetchOptions['with']) ? $this->_model->with($fetchOptions['with'])->newQuery() : $this->_model->newQuery();
            $query = $this->_prepareConditions($conditions, $query);
            $query = $this->_prepareFetchOptions($fetchOptions, $query);
            return $query->update($fillData);
        }else {
            $item = $this->getOne($conditions, $fetchOptions);
            if ($item) {
                $item->fill($fillData);
                $item->save();
            }

            return $item;
        }
    }

    public function customPaginate($conditions = array(), $fetchOptions = array(), $perPage = 10){
        $skip = request()->get('skip', 0);
        $page = request()->get('page', 1);
        $query = isset($fetchOptions['with']) ? $this->_model->with($fetchOptions['with'])->newQuery() : $this->_model->newQuery();
        $query = $this->_prepareConditions($conditions, $query);
        $query = $this->_prepareFetchOptions($fetchOptions, $query);
        $total = $query->count();
        $totalSkip = $skip + (($page - 1) * $perPage);
        $data = $query->skip($totalSkip)->take($perPage)->get();
        $totalPage = ceil($total/$perPage);
        return array(
            'current_page' => (int)$page,
            'total_page' => (int)$totalPage,
            'data' => $data,
        );
    }

    public function updateById($id, $fillData = array())
    {
        $item = $this->getById($id);
        if($item){
            $item->fill($fillData);
            $item->save();
        }

        return $item;
    }

    public function checkExist($conditions)
    {
        $query = $this->_model->newQuery();
        $query = $this->_prepareConditions($conditions, $query);
        return $query->count();
    }

//    public function search($conditions)
//    {
//        if (method_exists($this->_model, 'search')) {
//            $query = $this->_model->newQuery();
//            $query = $this->_prepareConditions($conditions, $query);
//            return $query->search();
//        }
//        return false;
//    }

    protected function _prepareConditions($conditions, $query){

        if(isset($conditions['filter'])){
            $query->filter($conditions['filter']);
        }

        if(isset($conditions['id'])){
            if(is_array($conditions['id'])){
                $query->whereIn('id', $conditions['id']);
            }else {
                $query->where('id', $conditions['id']);
            }
        }

        return $query;

    }

    protected function _prepareFetchOptions($fetchOptions, $query){
        if(isset($fetchOptions['orderBy'])){
            if (is_array($fetchOptions['orderBy'])) {
                foreach ($fetchOptions['orderBy'] as $key => $option) {
                    $direction = isset($fetchOptions['direction'][$key]) ? $fetchOptions['direction'][$key] : 'DESC';
                    if (!is_array($direction))
                        $query->orderBy($option, $direction);
                }
            } else {
                $direction = isset($fetchOptions['direction']) ? $fetchOptions['direction'] : 'DESC';
                if (!is_array($direction))
                    $query->orderBy($fetchOptions['orderBy'], $direction);
            }
        }
        if(isset($fetchOptions['skip']) && $fetchOptions['skip']){
            $skip = (int)$fetchOptions['skip'];
        }

        return $query;
    }
}
