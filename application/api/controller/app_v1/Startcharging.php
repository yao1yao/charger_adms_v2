<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/25
 * Time: 上午10:14
 */

namespace app\api\controller\app_v1;


use app\lib\exception\ChargerInfoException;
use think\Cache;

class Startcharging extends BaseController
{
    public function command(){
        // 场景校验
        $data = $this->ApiValidate->goCheck('startCharging');
        // 估计需要花费的金额
        $currentSpendMoney = $this->ChargerInfoModel->estimateMoney($data['chargerNumber'],$data['type'],$data['value']);
        // 判断余额是否足够
        $this->UserInfoModel->isBalanceEnough($data['userId'],$currentSpendMoney);
        // 开始充电，未开启返回失败
        $chargingInfo = $this->ChargerInfoModel->startCharging($data['userId'],$data['chargerNumber'],$data['type'],$data['value']);
        /**
         * 生成消费订单，先存到数据库
         * 并且生成以 userId 为键的缓存
         */
        $chargingBill=[
            'user_id'=> intval($data['userId']),
            'charger_number'=>intval($data['chargerNumber']),
            'charging_type'=>$data['type'],
            'set_energy'=>$chargingInfo['energy'],
            'set_duration'=>$chargingInfo['time'],
            //消费订单 1
            'consume_number'=>generateOrderNumber(CONSUME_RECORD),
        ];
        // 先保存消费订单
        $result = $this->UserChargingRecord->saveChargingRecord($data['userId'],$chargingBill);
        // 更新用户充电状态
        $this->UserInfoModel->isUpdate(true)->save(['is_charging'=>1], ['id' => $data['userId']]);
        if($result){
            return chargerBack(100,[
                'setEnergy'=>$chargingInfo['energy'],
                'setDuration'=>$chargingInfo['time'],
                'chargerNumber'=>$data['chargerNumber'],
                'type'=>$data['type']
            ]);
        }
    }
}