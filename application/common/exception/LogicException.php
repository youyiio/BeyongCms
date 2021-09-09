<?php
namespace app\common\exception;


class LogicException extends \Exception  {
    protected $logicCode;
    protected $logicMessage;

    public function __construct($logicCode, $logicMessage = '') {

        $this->logicCode = $logicCode;
        $this->logicMessage = empty($logicMessage)? config('resultcode.'.$logicCode): $logicMessage;
    }

    public function getModelCode()
    {
        return $this->modelCode;
    }

    public function getModelMessage()
    {
        return $this->modelMessage;
    }
}
