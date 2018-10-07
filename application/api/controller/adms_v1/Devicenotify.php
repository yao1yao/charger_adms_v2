<?php

namespace app\api\controller\adms_v1;



use app\api\validate\ApiValidate;

class Devicenotify extends BaseController
{
    public function command()
    {
        $data = (new ApiValidate())->goCheck('notify');
        $apiName = $data['apiName'];
        if($apiName === "notifyOTAResult" ){
            $sdata = [
                'api_type' => $data['apiName'],
                'deviceId' => $data['deviceId'],
                'status' => $data['result'],
                'data' => json_encode($data)
            ];
        }else {
            $sdata = [
                'api_type' => $data['apiName'],
                'deviceId' => $data['deviceId'],
                'status' => $data['status'],
                'data' => json_encode($data)
            ];
        }
        $result =$this->DeviceNotifyInfoModel->save($sdata);
        if (!$result) {
            throw new \Exception('内部错误');
        }
        switch ($apiName) {
            case 'notifyEndCharging':
                doCurl('http://charger.natapp1.cc/v1/notify',1, json_encode($data));
                break;
            case 'notifyOTAResult':
                break;
            case 'notifyChargerStatus':
            case 'notifyChargingInfo':
                break;
        }
        return json_encode(array(
            'respType' => $apiName,
            'data' => array(
                'respCode' => 100,
                'data' => $data,
            )
        ));
    }
}