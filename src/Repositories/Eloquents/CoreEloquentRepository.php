<?php
/**
 * User: JohnAVu
 * Date: 2020-04-22
 * Time: 10:16
 */

namespace Larky\Core\Repositories\Eloquents;

use Larky\Core\Repositories\Interfaces\CoreInterface;
use Illuminate\Pagination\Paginator;


abstract class CoreEloquentRepository implements CoreInterface
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    public function __construct()
    {
        $this->setModel();
    }

    /**
     * Get the model
     * @return Void
     */
    abstract public function getModel();


    /**
     * Set the model
     * @return Void
     */
    public function setModel()
    {
        $this->model = app()->make(
            $this->getModel()
        );
    }

    public function create()
    {
        return $this->model;
    }

    public function delete($ids)
    {
        if(is_array($ids)){
            foreach ($ids as $id){
                $this->find($id)->delete();
            }
            return true;
        }
        return $this->find($ids)->delete();
    }

    public function find($id)
    {
        return $this->create()->find($id);
    }

    public function getDataBy($conditions = null)
    {
        $model = $this->create();

        if (is_array($conditions)) {

            //filter
            if (isset($conditions['filterGroups'])) {
                foreach ($conditions['filterGroups'] as $filterGroups) {
                    $model = $model->where(function ($q) use ($filterGroups) {
                        foreach ($filterGroups['filters'] as $filter) {
                            if (strtolower($filter['condition_type']) == 'in') {
                                $q->orWhereIn($filter['field'], $filter['value']);
                            }
                            if (strtolower($filter['condition_type']) == 'between') {
                                $q->orWhereBetween($filter['field'], $filter['value']);
                            }
                            $q->orWHere($filter['field'],
                                $filter['condition_type'],
                                $filter['value']);
                        }
                    });
                }
            }

            //orderBy
            if (isset($conditions['orderBy'])) {
                foreach ($conditions['orderBy'] as $orderBy) {
                    $model = $model->orderBy($orderBy['field'], $orderBy['direction']);
                }
            }

            //pagintion
            if (isset($conditions['currentPage'])) {
                if(!isset($conditions['pageSize'])){
                    $conditions['pageSize'] = 10;
                }
                $currentPage = $conditions['currentPage'];
                // Set current page
                Paginator::currentPageResolver(function () use ($currentPage) {
                    return $currentPage;
                });

                return $model
                    ->paginate($conditions['pageSize']);

            }
        }

        return $model->get();
    }

    public function store($data)
    {
        $model = $this->create();
        $model->fill($data);
        return $model->save();
    }

    public function update($id, $data)
    {
        $model = $this->find($id);
        $model->fill($data);
        return $model->save();
    }

    public function validate($data, $rules, $messages)
    {
        // Validate
        $validator = \Validator::make($data, $rules, $messages);

        // If validate faild
        if ($validator->fails()) {

            // Get errors
            $errors = $validator->errors();

            // Format errors
            $errorsList = [];
            foreach ($errors->toArray() as $key => $value) {
                array_push($errorsList, $value[0]);
            }

            return ['status' => -1, 'errors' => $errorsList];
        }

        return $data;

    }
}
