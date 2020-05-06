<?php
/**
 * User: JohnAVu
 * Date: 2020-04-22
 * Time: 10:05
 */

namespace Larky\Core\Repositories\Interfaces;

interface CoreInterface
{
    /**
     * @param $conditions
     * @return mixed
     */
    public function getDataBy($conditions = null);

    /**
     * @return mixed
     */
    public function create();

    /**
     * @param $data
     * @return mixed
     */
    public function store($data);


    /**
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * @param $id
     * @param $data
     * @return mixed
     */
    public function update($id, $data);

    /**
     * @param $ids
     * @return mixed
     */
    public function delete($ids);

    /**
     * @param $data
     * @param $rules
     * @param $messages
     * @return mixed
     */
    public function validate($data, $rules, $messages);

}