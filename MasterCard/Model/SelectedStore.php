<?php

namespace OnTap\MasterCard\Model;

class SelectedStore
{
    protected $storeId;

    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
    }

    public function getStoreId()
    {
        return $this->storeId;
    }
}
