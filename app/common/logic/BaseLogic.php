<?php
namespace app\common\model;


class BaseLogic
{
    protected string $error;

    public function getError() {
        return $this->error;
    }
}