<?php
namespace app\common\logic;


class BaseLogic
{
    protected string $error;

    public function getError() {
        return $this->error;
    }
}