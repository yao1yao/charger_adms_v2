<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/18
 * Time: 下午12:03
 */

namespace app\api\controller\app_v1;


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
    public $ApiValidate;


    public function _initialize()
    {
        $this->UserInfoModel=model('UserInfo');
        $this->UserRechargeRecordModel=model('UserRechargeRecord');
        $this->DeviceInfoModel=model('DeviceInfo');
        $this->ChargerInfoModel=model('ChargerInfo');
        $this->UserChargingRecordModel=model('UserChargingRecord');
        $this->UserFeedbackModel=model('UserFeedback');
        $this->ApiValidate = new ApiValidate();
    }
}