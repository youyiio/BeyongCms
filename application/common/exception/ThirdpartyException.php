<?php

namespace app\common\exception;

use app\common\library\ResultCode;

/**
 * 第三方异常，如第三方api调用，rmi等
 */
class ThirdpartyException extends \Exception
{
    protected $thirdpartyCode;
    protected $thirdpartyMessage;

    public function __construct($thirdpartyCode = 0, $thirdpartyMessage = '')
    {
        if (is_int($thirdpartyCode)) {
            $this->thirdpartyCode = $thirdpartyCode;
            $this->thirdpartyMessage = empty($thirdpartyMessage) ? config('resultcode.' . $thirdpartyMessage) : $thirdpartyMessage;
        } else { //is_string
            $this->thirdpartyCode = ResultCode::E_LOGIC_ERROR;
            $this->thirdpartyMessage = $thirdpartyMessage;
        }

        parent::__construct($this->thirdpartyMessage, $this->thirdpartyCode);
    }

    public function getThirdpartyCode()
    {
        return $this->thirdpartyCode;
    }

    public function getThirdpartyMessage()
    {
        return $this->thirdpartyMessage;
    }
}
