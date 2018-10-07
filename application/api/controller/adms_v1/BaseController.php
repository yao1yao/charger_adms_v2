<?php

namespace app\api\controller\adms_v1;


use app\api\validate\ApiValidate;
use think\Controller;

class BaseController extends Controller
{
    public $UserRechargeRecordModel;
    public $UserInfoModel;
    public $DeviceInfoModel;
    public $ChargerInfoModel;
    public $UserChargingRecordModel;
    public $UserFeedbackModel;
    public $DeviceNotifyInfoModel;
    public $ApiValidate;

    public function _initialize()
    {
        $this->UserInfoModel=model('UserInfo');
        $this->UserRechargeRecordModel=model('UserRechargeRecord');
        $this->DeviceInfoModel=model('DeviceInfo');
        $this->ChargerInfoModel=model('ChargerInfo');
        $this->UserChargingRecordModel=model('UserChargingRecord');
        $this->UserFeedbackModel=model('UserFeedback');
        $this->DeviceNotifyInfoModel=model('DeviceNotifyInfo');
        $this->ApiValidate = new ApiValidate();
    }
}