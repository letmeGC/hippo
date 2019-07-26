<?php

namespace Api4\Controller;

use Service\Controller\ActivityController as ActivityService;
use Service\Controller\RedisConnectController as RedisConnectService;
use Think\Exception;

class Activity2018Controller extends CommonController
{

    protected function _initialize()
    {
        parent::_initialize();
    }


    /**助力年终10亿**/
    private function h1204_conf()
    {
        return [
            'start_time' => 1544112000,
            'end_time' => 1546271999,
            'invest_money' => 100000,
            'annualized_money' => 30000,
        ];
    }

    public function h1204_status()
    {

        try {
            $uid = I('userID', 0, 'int');
            $conf = $this->h1204_conf();
            $annualized = 0;
            $invest = 0;
            if ($uid) {
                $userAnnualized = A('Service/Activity')->userAnnualized($uid, $conf['start_time'], $conf['end_time']);
                $annualized = $userAnnualized['annualized'];
                $invest = $userAnnualized['invest'];
            }
            $this->returnData(0, 'ok', [
                'annualized' => $annualized,
                'invest' => $invest,
                'barrage' => $this->h1204_barrage(),
            ]);
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }

    }

    private function h1204_barrage()
    {
        $barrage = json_decode(A('Service/Activity')->curlPost(operate_host . "/Activity/181201_barrage"), true);
        $res = [];

        if ($barrage['data']) {
            foreach ($barrage['data'] as $k => $v) {
                $res[] = ['phone' => substr_replace($v['user_phone'], '****', 3, 4), 'sex' => $v['sex']];
            }
        } else {
            $res = [
                ["phone" => "137****9062", "sex" => "刘先生"],
                ["phone" => "186****3884", "sex" => "李先生"],
                ["phone" => "189****4593", "sex" => "陈女士"],
                ["phone" => "159****0130", "sex" => "王女士"],
                ["phone" => "130****5106", "sex" => "蔡女士"],
                ["phone" => "139****3101", "sex" => "周先生"],
                ["phone" => "150****5093", "sex" => "任女士"],
                ["phone" => "131****2105", "sex" => "许先生"],
                ["phone" => "155****8332", "sex" => "丁女士"],
                ["phone" => "188****2448", "sex" => "高女士"],
            ];
        }
        return $res;
    }

    public function h1204_prize_log($userID)
    {
        $conf = $this->h1204_conf();
        $now = time();
        if ($userID && $now >= $conf['start_time'] && $now < $conf['end_time']) {
            $userAnnualized = A('Service/Activity')->userAnnualized($userID, $conf['start_time'], $conf['end_time']);
            $annualized = $userAnnualized['annualized'];
            $invest = $userAnnualized['invest'];
            if ($annualized >= $conf['annualized_money'] || $invest >= $conf['invest_money']) {
                $userInfo = D("Home/User", "Service")->getUserInfo($userID);
                $gender = ((int)substr($userInfo['idcard'], 16, 1) % 2);
                $sex = ($gender) ? substr($userInfo['real_name'], 0, 3) . "先生" : substr($userInfo['real_name'], 0, 3) . "女士";
                $res = json_decode(A('Service/Activity')->curlPost(operate_host . "/Activity/181201_add", ['uid' => $userID, 'phone' => $userInfo['user_phone'], 'sex' => $sex]), true);
                print_r($res);
            } else {
                echo "不达标, 年华 {$annualized},累投{$invest}";
            }
        }
    }

    /**暖薪圣诞
     * @return array
     */
    private function h1205_conf()
    {
        return [

            'start_time' => 1545148800,//1545235200,
            'end_time' => 1546271940,
            'prize_start_time' => 1545235200,
            'asset' => 100000,
            'prize_a' => [
                'name' => '圣诞大礼包',
                'data' => [
                    ['invest_min_money' => 10000, 'money' => 10, 'invest_min_duration' => 20],
                    ['invest_min_money' => 30000, 'money' => 30, 'invest_min_duration' => 20],
                    ['invest_min_money' => 50000, 'money' => 50, 'invest_min_duration' => 20],

                    ['invest_min_money' => 15000, 'money' => 40, 'invest_min_duration' => 80],
                    ['invest_min_money' => 40000, 'money' => 100, 'invest_min_duration' => 80],
                    ['invest_min_money' => 60000, 'money' => 150, 'invest_min_duration' => 80],
                    ['invest_min_money' => 80000, 'money' => 200, 'invest_min_duration' => 80],

                    ['invest_min_money' => 10000, 'money' => 40, 'invest_min_duration' => 150],
                    ['invest_min_money' => 20000, 'money' => 80, 'invest_min_duration' => 150],
                    ['invest_min_money' => 40000, 'money' => 200, 'invest_min_duration' => 150],
                    ['invest_min_money' => 60000, 'money' => 300, 'invest_min_duration' => 150]
                ]
            ],
            'prize_b' => [
                'name' => '12月VIP专享大红包',
                'data' => [
                    ['invest_min_money' => 120000, 'money' => 120, 'invest_min_duration' => 20],
                    ['invest_min_money' => 120000, 'money' => 300, 'invest_min_duration' => 80],
                    ['invest_min_money' => 120000, 'money' => 600, 'invest_min_duration' => 150],
                    ['invest_min_money' => 120000, 'money' => 1200, 'invest_min_duration' => 300],
                ]
            ],
            'prize_c' => ['name' => '暖心圣诞活动分享红包', 'money' => 12, 'invest_min_money' => 2000, 'invest_min_duration' => 80, 'valid_days' => 3],

        ];
    }

    public function h1205_status()
    {
        try {

            $conf = $this->h1205_conf();
            $now = time();
            ($now < $conf['start_time'] || $now > $conf['end_time']) && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);

            $uid = I('userID', 0, 'int');
            !$uid && $this->returnData(1, ActivityService::PARAM_ERROR);

            $has_invest = 0;
            $pa['receive'] = 0;
            $pb['receive'] = 0;

            $investData = A('Service/Activity')->userFirstLatestInvest($uid);
            $latestInvest = $investData['latest'];
            $latestInvest && $has_invest = 1;

            $activityPrize = A('Service/Activity')->receivedPrize($uid, '', 0, $conf['start_time'], $conf['end_time']);
            if ($activityPrize) {
                foreach ($activityPrize as $key => $value) {
                    if ($value['source'] == $conf['prize_a']['name']) {
                        $pa['receive'] = 1;
                    }
                    if ($value['source'] == $conf['prize_b']['name']) {
                        $pb['receive'] = 1;
                    }
                }
            }

            $moneyInfo = getUserMoneyInfo($uid);
            $userAsset = $moneyInfo['total'];
            $this->returnData(0, 'ok', ['prize_a' => $pa, 'prize_b' => $pb, 'asset' => $userAsset, 'has_invest' => $has_invest]);

        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }

    }

    public function h1205_receive_pa()
    {
        try {

            $uid = I('userID', 0, 'int');
            $conf = $this->h1205_conf();
            $now = time();

            ($now < $conf['start_time'] || $now > $conf['end_time']) && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);

            !$uid && $this->returnData(1, ActivityService::PARAM_ERROR);

            $userInfo = D('Home/User', 'Service')->getUserInfo($uid);
            empty($userInfo) && $this->returnData(1, ActivityService::USE_NOT_EXIST);

            $investData = A('Service/Activity')->userFirstLatestInvest($uid);
            $latestInvest = $investData['latest'];
            !$latestInvest && $this->returnData(1, ActivityService::NO_INVEST);

            $checkRec = A('Service/Activity')->receivedPrize($uid, $conf['prize_a']['name'], 0, $conf['start_time'], $conf['end_time']);
            !empty($checkRec) && $this->returnData(1, ActivityService::HAS_REC);

            M()->startTrans();

            foreach ($conf['prize_a']['data'] as $key => $value) {

                $_POST['available_start_dt'] = date("Y-m-d H:i:s", $conf['prize_start_time']);
                $_POST['available_end_type'] = 1;
                $_POST['available_end_dt'] = date("Y-m-d H:i:s", $conf['end_time']);
                $_POST['admin_uid'] = 0;
                $_POST['uid'] = $uid;
                $_POST['pid2'] = $now . "_" . $key;
                $_POST['money'] = number_format($value['money'], 2, ".", "");
                $_POST['invest_min_money'] = $value['invest_min_money'];
                $_POST['invest_min_duration'] = $value['invest_min_duration'];
                $_POST['remark'] = $conf['prize_a']['name'];
                $_POST['is_send_to_phone'] = 0;
                $_POST['token'] = create_sinapay_token();
                $rs = A("Home/Prizeapi")->loteriokuponoj();
                if ($rs['response_code'] != 1) {
                    M()->rollback();
                    E($rs['response_message']);
                }
            }
            M()->commit();
            $this->returnData(0, '圣诞大礼包已到账');
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }

    public function h1205_receive_pb()
    {
        try {

            $uid = I('userID', 0, 'int');
            $time = time();
            $conf = $this->h1205_conf();
            ($time < $conf['start_time'] || $time > $conf['end_time']) && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            !$uid && $this->returnData(1, ActivityService::PARAM_ERROR);

            $userInfo = D('Home/User', 'Service')->getUserInfo($uid);
            empty($userInfo) && $this->returnData(1, ActivityService::USE_NOT_EXIST);

            $moneyInfo = getUserMoneyInfo($uid);
            $userAsset = $moneyInfo['total'];
            ($userAsset < $conf['asset']) && $this->returnData(1, "您的总资产金额未满{$conf['asset']}元，暂无法领取哦!");

            $checkRec = A('Service/Activity')->receivedPrize($uid, $conf['prize_b']['name'], 0, $conf['start_time'], $conf['end_time']);
            !empty($checkRec) && $this->returnData(1, ActivityService::HAS_REC);

            M()->startTrans();
            foreach ($conf['prize_b']['data'] as $key => $value) {

                $_POST['available_start_dt'] = date("Y-m-d H:i:s", $conf['prize_start_time']);
                $_POST['available_end_type'] = 1;
                $_POST['available_end_dt'] = date("Y-m-d H:i:s", $conf['end_time']);
                $_POST['admin_uid'] = 0;
                $_POST['uid'] = $uid;
                $_POST['pid2'] = $time . "_" . $key;
                $_POST['money'] = number_format($value['money'], 2, ".", "");
                $_POST['invest_min_money'] = $value['invest_min_money'];
                $_POST['invest_min_duration'] = $value['invest_min_duration'];
                $_POST['remark'] = $conf['prize_b']['name'];
                $_POST['is_send_to_phone'] = 0;
                $_POST['token'] = create_sinapay_token();
                $rs = A("Home/Prizeapi")->loteriokuponoj();

                if ($rs['response_code'] != 1) {
                    M()->rollback();
                    E($rs['response_message']);
                }
            }
            M()->commit();
            $this->returnData(0, '12月VIP专享大红包');
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }

    public function h1205_receive_pc()
    {
        try {

            $uid = I('userID', 0, 'int');
            $conf = $this->h1205_conf();
            $prize = $conf['prize_c'];
            $time = time();

            A('Service/Activity')->dailyPrizeStatus($uid, $time, $conf['start_time'], $conf['end_time'], $prize['name']);

            $_POST['available_start_dt'] = date("Y-m-d H:i:s", $time);
            $_POST['available_end_type'] = 0;
            $_POST['valid_days'] = $prize['valid_days'];
            $_POST['admin_uid'] = 0;
            $_POST['uid'] = $uid;
            $_POST['pid2'] = time();
            $_POST['money'] = number_format($prize['money'], 2, ".", "");
            $_POST['invest_min_money'] = $prize['invest_min_money'];
            $_POST['invest_min_duration'] = $prize['invest_min_duration'];
            $_POST['remark'] = $prize['name'];
            $_POST['is_send_to_phone'] = 0;
            $_POST['token'] = create_sinapay_token();
            $rs = A("Home/Prizeapi")->loteriokuponoj();

            if ($rs['response_code'] != 1) {
                throw new \Exception($rs['response_message']);
            }
            $this->returnData(0, '领取成功');
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }
    /**2019元旦活动
     * @return array
     */
    private function h190101_conf()
    {
        return [

            'test_time' => 1545926400,
            'end_time' => 1547567940,
            'prize_start_time' => 1546272000,
            'asset' => 100000,
            'prize_a' => [
                'name' => '元旦大礼包',
                'data' => [
                    ['invest_min_money' => 10000, 'money' => 10, 'invest_min_duration' => 20],
                    ['invest_min_money' => 30000, 'money' => 30, 'invest_min_duration' => 20],
                    ['invest_min_money' => 50000, 'money' => 50, 'invest_min_duration' => 20],
                    ['invest_min_money' => 100, 'prize_rate' => 0.5, 'invest_min_duration' => 20, 'prize_max_money' => 10000],

                    ['invest_min_money' => 15000, 'money' => 40, 'invest_min_duration' => 80],
                    ['invest_min_money' => 40000, 'money' => 100, 'invest_min_duration' => 80],
                    ['invest_min_money' => 60000, 'money' => 150, 'invest_min_duration' => 80],

                    ['invest_min_money' => 10000, 'money' => 40, 'invest_min_duration' => 150],
                    ['invest_min_money' => 20000, 'money' => 80, 'invest_min_duration' => 150],
                    ['invest_min_money' => 40000, 'money' => 200, 'invest_min_duration' => 150],
                    ['invest_min_money' => 60000, 'money' => 300, 'invest_min_duration' => 150]
                ]
            ],

            'prize_b' => [
                //加息券
                'name' => '超值回归礼',
                'data' => [
                    ['invest_min_money' => 100, 'prize_rate' => 1.88, 'invest_min_duration' => 20, 'prize_max_money' => 10000],
                    ['invest_min_money' => 100, 'prize_rate' => 2.88, 'invest_min_duration' => 20, 'prize_max_money' => 10000],
                ]
            ],

            'prize_c' => [
                'name' => '元旦VIP专享大红包',
                'data' => [
                    ['invest_min_money' => 100000, 'money' => 118, 'invest_min_duration' => 20],
                    ['invest_min_money' => 100000, 'money' => 288, 'invest_min_duration' => 80],
                    ['invest_min_money' => 100000, 'money' => 500, 'invest_min_duration' => 150],
                    ['invest_min_money' => 100000, 'money' => 1000, 'invest_min_duration' => 300],
                ]
            ],
            'prize_d' => ['name' => '新手福利', 'invest_min_money' => 100, 'prize_rate' => 3, 'invest_min_duration' => 20, 'prize_max_money' => 10000],

            'prize_e' => ['name' => '元旦活动分享红包', 'money' => 10, 'invest_min_money' => 2000, 'invest_min_duration' => 80, 'valid_days' => 3],

        ];
    }

    public function h190101_status()
    {
        try {

            $uid = I('userID', 0, 'int');

            $conf = $this->h190101_conf();
            $now = $_SERVER['REQUEST_TIME'];
            $now > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);

            $has_invest = 0;
            $userAsset = 0;
            $noInvestDays = 0; //上次投资天数 0 没投过 >0 具体天数
            $invest_before_activity = 0; //2019.1.1之前是否有投资
            $regBeforeActivity = 0;

            $pa['receive'] = 0;
            $pb['receive'] = 0;
            $pc['receive'] = 0;
            $pd['receive'] = 0;
            if ($uid) {

                $userInfo = D('Home/User', 'Service')->getUserInfo($uid);
                empty($userInfo) && $this->returnData(1, '用户不存在');
                $userInfo['reg_time'] < $conf['prize_start_time'] && $regBeforeActivity = 1;

                $investData = A('Service/Activity')->userFirstLatestInvest($uid);
                $latestInvest = $investData['latest'];
                $firstInvest = $investData['first'];
                if ($latestInvest) {

                    $firstInvest['add_time'] < $conf['prize_start_time'] && $invest_before_activity = 1;
                    $has_invest = 1;
                    $num = ($conf['end_time'] - $latestInvest['add_time']) / 86400;
                    if ($num > 0 && $num < 1) {
                        $num = 1;
                    }
                    $noInvestDays = (int)$num;
                }

                $activityPrize = A('Service/Activity')->receivedPrize($uid, '', 0, $conf['test_time'], $conf['end_time']);
                if ($activityPrize) {
                    foreach ($activityPrize as $key => $value) {
                        $value['source'] == $conf['prize_a']['name'] && $pa['receive'] = 1;
                        $value['source'] == $conf['prize_b']['name'] && $pb['receive'] = 1;
                        $value['source'] == $conf['prize_c']['name'] && $pc['receive'] = 1;
                        $value['source'] == $conf['prize_d']['name'] && $pd['receive'] = 1;
                    }
                }

                $moneyInfo = getUserMoneyInfo($uid);
                $userAsset = $moneyInfo['total'];
            }

            $this->returnData(0, 'ok', [
                'prize_a' => $pa,
                'prize_b' => $pb,
                'prize_c' => $pc,
                'prize_d' => $pd,
                'no_invest_days' => $noInvestDays,
                'asset' => $userAsset,
                'has_invest' => $has_invest,
                'reg_before_activity' => $regBeforeActivity,
                'invest_before_activity' => $invest_before_activity
            ]);

        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }

    }

    public function h190101_receive_pa()
    {
        try {

            $uid = I('userID', 0, 'int');
            $conf = $this->h190101_conf();
            $time = $_SERVER['REQUEST_TIME'];
            $time > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            !$uid && $this->returnData(1, ActivityService::PARAM_ERROR);
            $userInfo = D('Home/User', 'Service')->getUserInfo($uid);
            empty($userInfo) && $this->returnData(1, ActivityService::USE_NOT_EXIST);

            $investData = A('Service/Activity')->userFirstLatestInvest($uid);
            $latestInvest = $investData['latest'];

            !$latestInvest && $this->returnData(1, ActivityService::NO_INVEST);

            $num = ($conf['end_time'] - $latestInvest['add_time']) / 86400;
            $noInvestDays = (int)$num;
            $noInvestDays >= 180 && $this->returnData(1, "180内未投资，暂无法领取哦");

            $checkRec = A('Service/Activity')->receivedPrize($uid, '', 0, $conf['test_time'], $conf['end_time']);

            if ($checkRec) {
                foreach ($checkRec as $key => $value) {
                    if ($value['source'] == $conf['prize_a']['name'] || $value['source'] == $conf['prize_b']['name']) {
                        $this->returnData(1, ActivityService::HAS_REC);
                    }
                }
            }

            M()->startTrans();

            foreach ($conf['prize_a']['data'] as $key => $value) {

                $_POST['remark'] = $conf['prize_a']['name'];
                $_POST['available_start_dt'] = date("Y-m-d H:i:s", $conf['prize_start_time']);
                $_POST['available_end_type'] = 1;
                $_POST['available_end_dt'] = date("Y-m-d H:i:s", $conf['end_time']);
                $_POST['admin_uid'] = 0;
                $_POST['uid'] = $uid;
                $_POST['pid2'] = $time . "_" . $key;
                $_POST['invest_min_money'] = $value['invest_min_money'];
                $_POST['invest_min_duration'] = $value['invest_min_duration'];
                $_POST['is_send_to_phone'] = 0;
                $_POST['token'] = create_sinapay_token();

                if ($value['invest_min_money'] == 100) {

                    $_POST['money'] = number_format($value['prize_rate'], 2, ".", "");
                    $_POST['prize_max_money'] = $value['prize_max_money'];
                    //调用发放加息券接口
                    $rs = A("Home/Prizeapi")->loteryCoupon();
                } else {

                    $_POST['money'] = number_format($value['money'], 2, ".", "");
                    $rs = A("Home/Prizeapi")->loteriokuponoj();
                }
                if ($rs['response_code'] != 1) {
                    E($rs['response_message']);
                }

            }
            M()->commit();
            $this->returnData(0, '元旦大礼包已到账');
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }

    public function h190101_receive_pb()
    {
        try {

            $uid = I('userID', 0, 'int');
            $time = $_SERVER['REQUEST_TIME'];
            $conf = $this->h190101_conf();
            $time > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            !$uid && $this->returnData(1, ActivityService::PARAM_ERROR);

            $userInfo = D('Home/User', 'Service')->getUserInfo($uid);
            empty($userInfo) && $this->returnData(1, ActivityService::USE_NOT_EXIST);


            $noInvestDays = 0;
            $latestInvest = A('Service/Activity')->userFirstLatestInvest($uid);
            $latestInvest = $latestInvest['latest'];

            if ($latestInvest) {
                $noInvestDays = (int)(($conf['end_time'] - $latestInvest['add_time']) / 86400);
            }
            if ($noInvestDays >= 360) {
                $prize = $conf['prize_b']['data'][1];
            } elseif ($noInvestDays >= 180) {
                $prize = $conf['prize_b']['data'][0];
            } else {
                $this->returnData(1, "不够领取条件");
            }

            $checkRec = A('Service/Activity')->receivedPrize($uid, '', 0, $conf['test_time'], $conf['end_time']);
            if ($checkRec) {
                foreach ($checkRec as $key => $value) {
                    if ($value['source'] == $conf['prize_a']['name'] || $value['source'] == $conf['prize_b']['name']) {
                        $this->returnData(1, ActivityService::HAS_REC);
                    }
                }
            }


            $_POST['available_start_dt'] = date("Y-m-d H:i:s", $conf['prize_start_time']);
            $_POST['available_end_type'] = 1;
            $_POST['available_end_dt'] = date("Y-m-d H:i:s", $conf['end_time']);
            $_POST['admin_uid'] = 0;
            $_POST['uid'] = $uid;
            $_POST['pid2'] = time();
            $_POST['money'] = number_format($prize['prize_rate'], 2, ".", "");
            $_POST['invest_min_money'] = $prize['invest_min_money'];
            $_POST['invest_min_duration'] = $prize['invest_min_duration'];;
            $_POST['remark'] = $conf['prize_b']['name'];
            $_POST['prize_max_money'] = $prize['prize_max_money'];
            $_POST['is_send_to_phone'] = 0;
            $_POST['token'] = create_sinapay_token();
            //调用发放加息券接口
            $rs = A("Home/Prizeapi")->loteryCoupon();

            if ($rs['response_code'] != 1) {
                E($rs['response_message']);
            }
            $this->returnData(0, "恭喜您获得{$prize['prize_rate']}%收益券，请至“账户-我的优惠”中查看！");
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }

    public function h190101_receive_pc()
    {
        try {

            $uid = I('userID', 0, 'int');
            $time = $_SERVER['REQUEST_TIME'];
            $conf = $this->h190101_conf();
            $time > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            !$uid && $this->returnData(1, ActivityService::PARAM_ERROR);

            $userInfo = D('Home/User', 'Service')->getUserInfo($uid);
            empty($userInfo) && $this->returnData(1, ActivityService::USE_NOT_EXIST);

            $moneyInfo = getUserMoneyInfo($uid);
            $userAsset = $moneyInfo['total'];
            ($userAsset < $conf['asset']) && $this->returnData(1, "您的总资产金额未满{$conf['asset']}元，暂无法领取哦!");

            $checkRec = A('Service/Activity')->receivedPrize($uid, $conf['prize_c']['name'], 0, $conf['test_time'], $conf['end_time']);
            !empty($checkRec) && $this->returnData(1, ActivityService::HAS_REC);

            M()->startTrans();
            foreach ($conf['prize_c']['data'] as $key => $value) {

                $_POST['available_start_dt'] = date("Y-m-d H:i:s", $conf['prize_start_time']);
                $_POST['available_end_type'] = 1;
                $_POST['available_end_dt'] = date("Y-m-d H:i:s", $conf['end_time']);
                $_POST['admin_uid'] = 0;
                $_POST['uid'] = $uid;
                $_POST['pid2'] = $time . "_" . $key;
                $_POST['money'] = number_format($value['money'], 2, ".", "");
                $_POST['invest_min_money'] = $value['invest_min_money'];
                $_POST['invest_min_duration'] = $value['invest_min_duration'];
                $_POST['remark'] = $conf['prize_c']['name'];
                $_POST['is_send_to_phone'] = 0;
                $_POST['token'] = create_sinapay_token();
                $rs = A("Home/Prizeapi")->loteriokuponoj();

                if ($rs['response_code'] != 1) {
                    M()->rollback();
                    E($rs['response_message']);
                }
            }
            M()->commit();
            $this->returnData(0, "{$conf['prize_c']['name']}已到账");
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }

    public function h190101_receive_pd()
    {
        try {
            $uid = I('userID', 0, 'int');
            $conf = $this->h190101_conf();
            $prize = $conf['prize_d'];
            $time = $_SERVER['REQUEST_TIME'];

            $time > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            !$uid && $this->returnData(1, ActivityService::PARAM_ERROR);
            $userInfo = D('Home/User', 'Service')->getUserInfo($uid);
            empty($userInfo) && $this->returnData(1, ActivityService::USE_NOT_EXIST);

            $checkRec = A('Service/Activity')->receivedPrize($uid, $prize['name'], 0, $conf['test_time'], $conf['end_time']);
            !empty($checkRec) && $this->returnData(1, ActivityService::HAS_REC);


            $checkInvest = A('Service/Activity')->checkInvest($uid);

            if (($userInfo['reg_time'] > $conf['prize_start_time']) || $checkInvest) {
                $this->returnData(1, "不好意思，您不符合领取条件，暂无法领取哦");
            }

            //加息券
            $_POST['available_start_dt'] = date("Y-m-d H:i:s", $conf['prize_start_time']);
            $_POST['available_end_type'] = 1;
            $_POST['available_end_dt'] = date("Y-m-d H:i:s", $conf['end_time']);
            $_POST['admin_uid'] = 0;
            $_POST['uid'] = $uid;
            $_POST['pid2'] = $time;
            $_POST['money'] = number_format($prize['prize_rate'], 2, ".", "");
            $_POST['invest_min_money'] = $prize['invest_min_money'];
            $_POST['invest_min_duration'] = $prize['invest_min_duration'];;
            $_POST['remark'] = $prize['name'];
            $_POST['prize_max_money'] = $prize['prize_max_money'];
            $_POST['is_send_to_phone'] = 0;
            $_POST['token'] = create_sinapay_token();
            //调用发放加息券接口
            $rs = A("Home/Prizeapi")->loteryCoupon();

            if ($rs['response_code'] != 1) {
                $this->returnData(1, $rs['response_message']);
            }
            $this->returnData(0, '领取成功！请至“账户-我的优惠”中查看！');

        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }

    public function h190101_receive_pe()
    {
        try {

            $uid = I('userID', 0, 'int');
            $conf = $this->h190101_conf();
            $prize = $conf['prize_e'];
            $time = $_SERVER['REQUEST_TIME'];

            A('Service/Activity')->dailyPrizeStatus($uid, $time, $conf['test_time'], $conf['end_time'], $prize['name']);

            $_POST['available_start_dt'] = date("Y-m-d H:i:s", $time);
            $_POST['available_end_type'] = 0;
            $_POST['valid_days'] = $prize['valid_days'];
            $_POST['admin_uid'] = 0;
            $_POST['uid'] = $uid;
            $_POST['pid2'] = $time;
            $_POST['money'] = number_format($prize['money'], 2, ".", "");
            $_POST['invest_min_money'] = $prize['invest_min_money'];
            $_POST['invest_min_duration'] = $prize['invest_min_duration'];
            $_POST['remark'] = $prize['name'];
            $_POST['is_send_to_phone'] = 0;
            $_POST['token'] = create_sinapay_token();
            $rs = A("Home/Prizeapi")->loteriokuponoj();

            if ($rs['response_code'] != 1) {
                throw new \Exception($rs['response_message']);
            }
            $this->returnData(0, '领取成功');
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }

    /**
     * 2018年度账单活动
     */

    private function h190102_conf()
    {
        return [
            'yearStart' => 1514736000,
            'yearEnd' => 1546271999,
            'activityStart' => 1547049600,
            'cacheTime' => 432000,
            'investUserCount' => 6717,
            'cacheKey' => "h190102_",
            'prize' => ['name' => '2018年度成绩单分享红包', 'invest_min_money' => 10000, 'prize_rate' => 1.5, 'invest_min_duration' => 20, 'prize_max_money' => 10000, 'valid_days' => 7],

        ];
    }

    public function h190102_status()
    {
        try {
            $uid = I('userID', 0, 'int');
            $conf = $this->h190102_conf();
            !$uid && $this->returnData(1, ActivityService::PARAM_ERROR);

            $checkInvest = A('Service/Activity')->checkInvest($uid, $conf['yearStart'], $conf['yearEnd']);
            $this->returnData(0, 'ok', ['has_invest' => $checkInvest ? 1 : 0]);
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }

    public function h190102_data()
    {

        try {
            $uid = I('userID', 0, 'int');
            $conf = $this->h190102_conf();
            !$uid && $this->returnData(1, ActivityService::PARAM_ERROR);
            $checkInvest = A('Service/Activity')->checkInvest($uid, $conf['yearStart'], $conf['yearEnd']);
            !$checkInvest && !$uid && $this->returnData(1, "18年未投资");

            $redis = RedisConnectService::getRedisInstance();
            $cacheData = $redis->get($conf['cacheKey'] . $uid);
            if ($cacheData) {
                $cacheData = json_decode($cacheData, true);
            } else {

                $sql = "select a.`id`,a.`investor_capital`,a.`add_time`,a.`prize_id`,b.`borrow_name`,b.`borrow_duration` from `tp_borrow_investor` as a 
                  join `tp_borrow_info` as b on  a.borrow_id = b.id and  a.`investor_uid` = '{$uid}' and a.`status` in (10,12,40,50) and a.`add_time` >= '{$conf['yearStart']}' and a.`add_time` <= '{$conf['yearEnd']}' ORDER BY a.add_time";
                $investData = M()->query($sql);

                $maxCapital = 0;
                $maxInvest = [];
                $totalInvest = 0;
                $favorite = [];
                $investByduration = [];
                foreach ($investData as $key => $val) {
                    if ($val['investor_capital'] > $maxCapital) {
                        $maxCapital = $val['investor_capital'];
                        $maxInvest = $val;
                    }

                    $totalInvest += $val['investor_capital'];
                    $favorite[$val['borrow_duration']] += 1;
                    $investByduration[$val['borrow_duration']] += $val['investor_capital'];
                }

                //使用红包
                $sql = "select `user_id`, sum(`affect_money`)  as s ,max(`affect_money`) as m  from tp_user_moneylog  where `user_id` = {$uid} and `add_time` >= '{$conf['yearStart']}' and `add_time` <= '{$conf['yearEnd']}'  and  `category` = 46 and `status` = 2 ";
                $prizeData = M()->query($sql);

                //邀请人数 佣金
                $inviteCount = M('user')->where("pid = '{$uid}' AND reg_time >= '{$conf['yearStart']}' AND reg_time <= '{$conf['yearEnd']}'")->count();
                $commissionMoney = M('prize')->where("user_id = '{$uid}' AND category = 4  and `insert_time` >= '{$conf['yearStart']}'  and `insert_time` <= '{$conf['yearEnd']}' ")->sum('money');

                //累计签到 连续签到7天奖励
                $signPrizeCount = M('prize')->where("user_id = '{$uid}' AND source = '连续签到7天奖励' AND category = 7 and `insert_time` >= '{$conf['yearStart']}' and `insert_time` <= '{$conf['yearEnd']}'  ")->count();
                $signDays = M('sign_in_form')->where("user_id = '{$uid}' and `sign_in_time` >= '{$conf['yearStart']}' and `sign_in_time` <= '{$conf['yearEnd']}'")->count();
                //捐赠爱心
                $donateCount = M('donateList')->where("user_id = '{$uid}' AND status = '1' and `add_time` >= '{$conf['yearStart']}' and `add_time` <= '{$conf['yearEnd']}' ")->sum('donate_num');

                //投资分布
                foreach ($investByduration as $kk => $vv) {
                    $fanChart[] = [
                        'duration' => $kk,
                        'money' => $vv,
                    ];
                }

                //累计收益

                $yearInterestSql = "SELECT SUM(a.interest+a.prize_interest) AS interest FROM tp_investor_detail AS a,tp_borrow_investor AS b WHERE a.invest_id = b.id AND a.investor_uid = '{$uid}' AND a.status > 0 AND b.add_time >= '{$conf['yearStart']}' AND b.add_time <= '{$conf['yearEnd']}'";
                $yearInterest = M()->query($yearInterestSql);
                $yearInterest = $yearInterest[0]['interest'];

                //充值 提现
                $recharge = M('user_moneylog')->where("`user_id` = {$uid} and `add_time` >= '{$conf['yearStart']}' and `add_time` <= '{$conf['yearEnd']}'  and  `category` = 10  and `status` = 2 ")->sum('affect_money');
                $cashWithdrawal = M('user_moneylog')->where("`user_id` = {$uid} and `add_time` >= '{$conf['yearStart']}' and `add_time` <= '{$conf['yearEnd']}'  and  `category` = 20  and `status` = 2 ")->sum('affect_money');

                //投资超过多少用户
                $annualTotalData = json_decode(A('Service/Activity')->curlPost(operate_host . "/Activity/190101_select", ['invest_money' => $totalInvest]), true);
                if (!$annualTotalData['code']) {
                    $this->returnData(1, $annualTotalData['message']);
                }
                $exceed = $annualTotalData['data'];
                $percent = (int)($exceed / $conf['investUserCount'] * 100);

                $cacheData = [
                    'two' => ['investTime' => $investData[0]['add_time'], 'borrowName' => $investData[0]['borrow_name'], 'interest' => $this->interest($investData[0]['id'], $uid), 'investMoney' => $investData[0]['investor_capital']],
                    'three' => ['maxPrize' => (int)$prizeData[0]['m'], 'totalPrize' => (int)$prizeData[0]['s']],
                    'four' => ['inviteCount' => (int)$inviteCount, 'commission' => (int)$commissionMoney],
                    'five' => ['prizeCount' => (int)$signPrizeCount, 'signDays' => (int)$signDays],
                    'six' => ['donateCount' => (int)$donateCount],
                    'seven' => ['investTime' => $maxInvest['add_time'], 'borrowName' => $maxInvest['borrow_name'], 'interest' => $this->interest($maxInvest['id'], $uid), 'investMoney' => $maxInvest['investor_capital']],
                    'eight' => ['investTotal' => $totalInvest, 'yearInterest' => $yearInterest, 'favorite' => $favorite ? array_search(max($favorite), $favorite) : ''],
                    'nine' => ['fanChart' => $fanChart, 'yearInterest' => $yearInterest, 'investTotal' => $totalInvest, 'percent' => $percent . '%', 'recharge' => (int)$recharge, 'cashWithdrawal' => (int)$cashWithdrawal, 'inviteCount' => (int)$inviteCount, 'donateCount' => (int)$donateCount, 'signDays' => (int)$signDays],
                ];
                $redis->set($conf['cacheKey'] . $uid, json_encode($cacheData));
                $redis->expire($conf['cacheKey'] . $uid, $conf['cacheTime']);

            }
            $this->returnData(0, 'ok', $cacheData);

        } catch (\Exception $e) {
        }
        $this->returnData(1, $e->getMessage());
    }

    public function h190102_share()
    {
        try {
            $uid = I('userID', 0, 'int');
            !$uid && $this->returnData(1, ActivityService::PARAM_ERROR);
            $scoreConf = $this->scoreConf();
            $conf = $this->h190102_conf();

            $redis = RedisConnectService::getRedisInstance();
            $cacheData = json_decode($redis->get($conf['cacheKey'] . $uid), true);
            //判断cache数据
            if (isset($cacheData['sharePage']) && $cacheData['sharePage']) {
                $res = $cacheData['sharePage'];
            } else {

                $investTotalScore = $this->getScore($cacheData['eight']['investTotal'] / 10000, $scoreConf['capital']);
                $yearInterestScore = $this->getScore($cacheData['eight']['yearInterest'], $scoreConf['interest']);
                $usePrizeScore = $this->getScore($cacheData['three']['totalPrize'], $scoreConf['userPrzie']);
                $inviteCountScore = $this->getScore($cacheData['four']['inviteCount'], $scoreConf['inviteFriend']);
                $commissionScore = $this->getScore($cacheData['four']['commission'], $scoreConf['commission']);
                $signDaysScore = $this->getScore($cacheData['five']['signDays'], $scoreConf['signIn']);
                $donateCountScore = $this->getScore($cacheData['six']['donateCount'], $scoreConf['donate']);
                $totalScore = $investTotalScore + $yearInterestScore + $usePrizeScore + $inviteCountScore + $commissionScore + $signDaysScore + $donateCountScore;


                $userInfo = D("Home/User", "Service")->getUserInfo($uid);

                $res = [
                    'name' => $this->substrCut($userInfo['real_name']),
                    'uid' => $uid,
                    'ico' => A('Service/Activity')->UserIco($uid),
                    'regTime' => $userInfo['reg_time'],
                    'investTotal' => $investTotalScore,
                    'yearInterest' => $yearInterestScore,
                    'usePrize' => $usePrizeScore,
                    'inviteCount' => $inviteCountScore,
                    'commission' => $commissionScore,
                    'signDays' => $signDaysScore,
                    'donateCount' => $donateCountScore,
                    'totalScore' => $totalScore
                ];
                if (isset($cacheData['two']) && $cacheData['two']) {

                    $cacheData['sharePage'] = $res;
                    $redis->set($conf['cacheKey'] . $uid, json_encode($cacheData));
                    $redis->expire($conf['cacheKey'] . $uid, $conf['cacheTime']);

                }

            }
            $this->returnData(0, 'ok', $res);
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }

    public function h190102_przie()
    {
        try {
            $uid = I('userID', 0, 'int');
            $conf = $this->h190102_conf();
            $prize = $conf['prize'];
            $time = $_SERVER['REQUEST_TIME'];

            //$time > $conf['activityEnd'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            !$uid && $this->returnData(1, ActivityService::PARAM_ERROR);
            $userInfo = D('Home/User', 'Service')->getUserInfo($uid);
            empty($userInfo) && $this->returnData(1, ActivityService::USE_NOT_EXIST);

            $checkRec = A('Service/Activity')->receivedPrize($uid, $prize['name'], 0, $conf['activityStart']);
            !empty($checkRec) && $this->returnData(1, ActivityService::HAS_REC);

            //加息券
            $_POST['available_start_dt'] = date("Y-m-d H:i:s", $time);
            $_POST['available_end_type'] = 0;
            $_POST['valid_days'] = $prize['valid_days'];
            $_POST['admin_uid'] = 0;
            $_POST['uid'] = $uid;
            $_POST['pid2'] = $time;
            $_POST['money'] = number_format($prize['prize_rate'], 2, ".", "");
            $_POST['invest_min_money'] = $prize['invest_min_money'];
            $_POST['invest_min_duration'] = $prize['invest_min_duration'];
            $_POST['remark'] = $prize['name'];
            $_POST['is_send_to_phone'] = 0;
            $_POST['token'] = create_sinapay_token();
            $_POST['prize_max_money'] = $prize['prize_max_money'];
            //调用发放加息券接口
            $rs = A("Home/Prizeapi")->loteryCoupon();

            if ($rs['response_code'] != 1) {
                $this->returnData(1, $rs['response_message']);
            }
            $this->returnData(0, '领取成功！请至“账户-我的优惠”中查看！');

        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }

    /**单笔收益
     * @param $invest_id
     * @param $userID
     * @return int
     */
    public function interest($invest_id, $userID)
    {
        $investorDetailData = M('investor_detail')
            ->where("status >= 1 AND investor_uid = '{$userID}' and `invest_id` = '{$invest_id}'")
            ->field('status,interest,receive_interest,real_repay_time,receive_prize_interest,prize_interest')
            ->select();
        $interest = 0;
        foreach ($investorDetailData as $key => $value) {
            if ($value['real_repay_time'] > 0) {
                $interest += ($value['receive_interest'] + $value['receive_prize_interest']);
            } else {
                $interest += ($value['interest'] + $value['prize_interest']);
            }
        }

        return $interest;
    }

    private function substrCut($user_name)
    {
        $strlen = mb_strlen($user_name, 'utf-8');
        $firstStr = mb_substr($user_name, 0, 1, 'utf-8');
        $lastStr = mb_substr($user_name, -1, 1, 'utf-8');
        return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($user_name, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
    }

    private function scoreConf()
    {
        return [
            'capital' => [
                ['max' => 5000, 'min' => 100],
                ['max' => 100, 'min' => 50],
                ['max' => 50, 'min' => 30],
                ['max' => 30, 'min' => 20],
                ['max' => 20, 'min' => 15],
                ['max' => 15, 'min' => 10],
                ['max' => 10, 'min' => 5],
                ['max' => 5, 'min' => 3],
                ['max' => 3, 'min' => 1],
                ['max' => 1, 'min' => 0],
            ],

            'interest' => [
                ['max' => 100000, 'min' => 20000],
                ['max' => 20000, 'min' => 10000],
                ['max' => 10000, 'min' => 8000],
                ['max' => 8000, 'min' => 5000],
                ['max' => 5000, 'min' => 3000],
                ['max' => 3000, 'min' => 2000],
                ['max' => 2000, 'min' => 1000],
                ['max' => 1000, 'min' => 500],
                ['max' => 500, 'min' => 100],
                ['max' => 100, 'min' => 0],
            ],

            'userPrzie' => [
                ['max' => 20000, 'min' => 5000],
                ['max' => 5000, 'min' => 3000],
                ['max' => 3000, 'min' => 2000],
                ['max' => 2000, 'min' => 1500],
                ['max' => 1500, 'min' => 1000],
                ['max' => 1000, 'min' => 800],
                ['max' => 800, 'min' => 500],
                ['max' => 500, 'min' => 300],
                ['max' => 300, 'min' => 100],
                ['max' => 100, 'min' => 0],
            ],

            'inviteFriend' => [
                ['max' => 50, 'min' => 30],
                ['max' => 30, 'min' => 25],
                ['max' => 25, 'min' => 20],
                ['max' => 20, 'min' => 15],
                ['max' => 15, 'min' => 10],
                ['max' => 10, 'min' => 8],
                ['max' => 8, 'min' => 5],
                ['max' => 5, 'min' => 3],
                ['max' => 3, 'min' => 1],
                ['max' => 1, 'min' => 0],
            ],
            'commission' => [
                ['max' => 60000, 'min' => 1000],
                ['max' => 1000, 'min' => 500],
                ['max' => 500, 'min' => 300],
                ['max' => 300, 'min' => 150],
                ['max' => 150, 'min' => 100],
                ['max' => 100, 'min' => 80],
                ['max' => 80, 'min' => 50],
                ['max' => 50, 'min' => 20],
                ['max' => 20, 'min' => 10],
                ['max' => 10, 'min' => 0],
            ],

            'signIn' => [
                ['max' => 365, 'min' => 300],
                ['max' => 300, 'min' => 200],
                ['max' => 200, 'min' => 150],
                ['max' => 150, 'min' => 100],
                ['max' => 100, 'min' => 80],
                ['max' => 80, 'min' => 60],
                ['max' => 60, 'min' => 50],
                ['max' => 50, 'min' => 30],
                ['max' => 30, 'min' => 15],
                ['max' => 15, 'min' => 0],
            ],
            'donate' => [
                ['max' => 1000, 'min' => 300],
                ['max' => 300, 'min' => 250],
                ['max' => 250, 'min' => 200],
                ['max' => 200, 'min' => 150],
                ['max' => 150, 'min' => 100],
                ['max' => 100, 'min' => 80],
                ['max' => 80, 'min' => 50],
                ['max' => 50, 'min' => 30],
                ['max' => 30, 'min' => 10],
                ['max' => 10, 'min' => 0]
            ],


        ];
    }

    private function getScore($num, $subjectConf)
    {

        $scoreConf = [

            ['max' => 100, 'min' => 91],
            ['max' => 90, 'min' => 81],
            ['max' => 80, 'min' => 71],
            ['max' => 70, 'min' => 61],
            ['max' => 60, 'min' => 51],
            ['max' => 50, 'min' => 41],
            ['max' => 40, 'min' => 31],
            ['max' => 30, 'min' => 21],
            ['max' => 20, 'min' => 11],
            ['max' => 10, 'min' => 1],

        ];

        foreach ($subjectConf as $key => $value) {
            if ($num > $value['min']) {
                if ($key == 9) {
                    $score = $num * 10 / $value['max'];
                } else {
                    $index = $key + 1;
                    $previousScore = $scoreConf[$index]['max'];
                    $minInvest = $value['min'];
                    $maxInvest = $value['max'];
                    $score = $previousScore + ($num - $minInvest) / ($maxInvest - $minInvest) * 10;
                    break;
                }
            }
        }
        $score = round($score);
        if ($score < 0) {
            $score = 1;
        }
        if ($score > 100) {
            $score = 100;
        }
        return $score;
    }

    public function h190102_xx()
    {
        $uid = I('userID', 0, 'int');
        $conf = $this->h190102_conf();
        !$uid && $this->returnData(1, ActivityService::PARAM_ERROR);
        $redis = RedisConnectService::getRedisInstance();
        $cacheData = $redis->del($conf['cacheKey'] . $uid);
        var_dump($cacheData);
    }

    private function h190103_conf()
    {
        return [

            'test_time' => 1547481600,
            'end_time' => 1548950340,
            'asset' => 100000,
            'prize_start'=>1547568000,
            'prize_a' => [
                'name' => '年货节大礼包',
                'data' => [
                    ['invest_min_money' => 10000, 'money' => 10, 'invest_min_duration' => 20],
                    ['invest_min_money' => 30000, 'money' => 30, 'invest_min_duration' => 20],
                    ['invest_min_money' => 50000, 'money' => 50, 'invest_min_duration' => 20],
                    ['invest_min_money' => 100, 'prize_rate' => 0.5, 'invest_min_duration' => 20, 'prize_max_money' => 10000],

                    ['invest_min_money' => 15000, 'money' => 40, 'invest_min_duration' => 80],
                    ['invest_min_money' => 40000, 'money' => 100, 'invest_min_duration' => 80],
                    ['invest_min_money' => 60000, 'money' => 150, 'invest_min_duration' => 80],

                    ['invest_min_money' => 10000, 'money' => 40, 'invest_min_duration' => 150],
                    ['invest_min_money' => 20000, 'money' => 80, 'invest_min_duration' => 150],
                    ['invest_min_money' => 40000, 'money' => 200, 'invest_min_duration' => 150],
                    ['invest_min_money' => 60000, 'money' => 300, 'invest_min_duration' => 150]
                ]
            ],
            'prize_b' => [
                'name' => '1月VIP专享大红包',
                'data' => [
                    ['invest_min_money' => 100000, 'money' => 288, 'invest_min_duration' => 80],
                    ['invest_min_money' => 100000, 'money' => 588, 'invest_min_duration' => 150],
                    ['invest_min_money' => 100000, 'money' => 888, 'invest_min_duration' => 300],
                ]
            ],
            'prize_c' => ['name' => '新手福利', 'invest_min_money' => 100, 'prize_rate' => 3, 'invest_min_duration' => 20, 'prize_max_money' => 10000],
            'prize_d' => ['name' => '年货节活动分享红包', 'money' => 10, 'invest_min_money' => 2000, 'invest_min_duration' => 80, 'valid_days' => 3],
        ];
    }

    public function h190103_status()
    {
        try {

            $uid = I('userID', 0, 'int');
            $conf = $this->h190103_conf();
            $now = $_SERVER['REQUEST_TIME'];
            $now > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);

            $has_invest = 0;
            $userAsset = 0;
            $regBeforeActivity = 0;
            $initialCapital = 0;
            $currentCapital = 0;
            $pa['receive'] = 0;
            $pb['receive'] = 0;
            $pc['receive'] = 0;
            if ($uid) {

                $userInfo = D('Home/User', 'Service')->getUserInfo($uid);
                empty($userInfo) && $this->returnData(1, '用户不存在');
                $checkInvest =A('Service/Activity')->checkInvest($uid);
                $checkInvest &&   $has_invest = 1;

                if($userInfo['reg_time'] < $conf['prize_start']  && !$checkInvest){
                    $regBeforeActivity = 1;
                }

                $activityPrize = A('Service/Activity')->receivedPrize($uid, '', 0, $conf['test_time'], $conf['end_time']);
                if ($activityPrize) {
                    foreach ($activityPrize as $key => $value) {
                        $value['source'] == $conf['prize_a']['name'] && $pa['receive'] = 1;
                        $value['source'] == $conf['prize_b']['name'] && $pb['receive'] = 1;
                        $value['source'] == $conf['prize_c']['name'] && $pc['receive'] = 1;
                    }
                }
                $moneyInfo = getUserMoneyInfo($uid);
                $userAsset = $moneyInfo['total'];
                $initialCapitalData = json_decode(A('Service/Activity')->curlPost(operate_host . "/Activity/190101_initial_capital", ['uid' => $uid]), true);
                if($initialCapitalData['code'] == 0){
                     throw new \Exception($initialCapitalData['message']);
                }
                //初始本金
                $initialCapital = $initialCapitalData['data']['capital'];
               //当前本金
                if($now > $conf['end_time']) {
                    $currentCapitalData = json_decode(A('Service/Activity')->curlPost(operate_host . "/Activity/190101_end_capital", ['uid' => $uid]), true);
                    if($currentCapitalData['code'] == 0){
                         throw new \Exception($currentCapitalData['message']);
                    }
                    $currentCapital = $currentCapitalData['data']['capital'];
                }else{
                    $invest_bid_money_already  = M("InvestorDetail")->where("is_test=0 AND status IN(1,2) AND investor_uid=$uid")->sum("capital");
                    $invest_bid_money =  M("BorrowInvestor")->where("investor_uid=$uid and status IN (12)")->sum("investor_capital");
                    $currentCapital =  abs($invest_bid_money_already) + abs($invest_bid_money);
                }
            }

            $this->returnData(0, 'ok', [
                'prize_a' => $pa,
                'prize_b' => $pb,
                'prize_c' => $pc,
                'asset' => $userAsset,
                'has_invest' => $has_invest,
                'reg_before_activity' => $regBeforeActivity,
                'currentCapital' =>$currentCapital,
                'initialCapital' =>$initialCapital,
            ]);

        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }

    }

    public function h190103_receive_pa()
    {
        try {

            $uid = I('userID', 0, 'int');
            $conf = $this->h190103_conf();
            $time = $_SERVER['REQUEST_TIME'];
            $time > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            !$uid && $this->returnData(1, ActivityService::PARAM_ERROR);
            $userInfo = D('Home/User', 'Service')->getUserInfo($uid);
            empty($userInfo) && $this->returnData(1, ActivityService::USE_NOT_EXIST);

            $checkInvest = A('Service/Activity')->checkInvest($uid);
            !$checkInvest && $this->returnData(1, ActivityService::NO_INVEST);

            $checkRec = A('Service/Activity')->receivedPrize($uid, $conf['prize_a']['name'], 0, $conf['test_time']);
            !empty($checkRec) && $this->returnData(1, ActivityService::HAS_REC);

            M()->startTrans();

            foreach ($conf['prize_a']['data'] as $key => $value) {

                $_POST['remark'] = $conf['prize_a']['name'];
                $_POST['available_start_dt'] = date("Y-m-d H:i:s", $conf['prize_start']);
                $_POST['available_end_type'] = 1;
                $_POST['available_end_dt'] = date("Y-m-d H:i:s", $conf['end_time']);
                $_POST['admin_uid'] = 0;
                $_POST['uid'] = $uid;
                $_POST['pid2'] = $time . "_" . $key;
                $_POST['invest_min_money'] = $value['invest_min_money'];
                $_POST['invest_min_duration'] = $value['invest_min_duration'];
                $_POST['is_send_to_phone'] = 0;
                $_POST['token'] = create_sinapay_token();

                if ($value['invest_min_money'] == 100) {

                    $_POST['money'] = number_format($value['prize_rate'], 2, ".", "");
                    $_POST['prize_max_money'] = $value['prize_max_money'];
                    //调用发放加息券接口
                    $rs = A("Home/Prizeapi")->loteryCoupon();
                } else {

                    $_POST['money'] = number_format($value['money'], 2, ".", "");
                    $rs = A("Home/Prizeapi")->loteriokuponoj();
                }
                if ($rs['response_code'] != 1) {
                    E($rs['response_message']);
                }

            }
            M()->commit();
            $this->returnData(0, "{$conf['prize_a']['name']}已到账");
        } catch(\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }
    public function h190103_receive_pb(){

        try{

            $uid = I('userID', 0, 'int');
            $conf = $this->h190103_conf();
            $time = $_SERVER['REQUEST_TIME'];
            $time > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            !$uid && $this->returnData(1, ActivityService::PARAM_ERROR);
            $userInfo = D('Home/User', 'Service')->getUserInfo($uid);
            empty($userInfo) && $this->returnData(1, ActivityService::USE_NOT_EXIST);

            $moneyInfo = getUserMoneyInfo($uid);
            $userAsset = $moneyInfo['total'];

            ($userAsset < $conf['asset']) &&  $this->returnData(1, "您的总资产金额未满{$conf['asset']}元，暂无法领取哦！");

            $checkRec = A('Service/Activity')->receivedPrize($uid,$conf['prize_b']['name'],0, $conf['test_time']);
            !empty($checkRec) && $this->returnData(1, ActivityService::HAS_REC);

            M()->startTrans();

            foreach ($conf['prize_b']['data'] as $key => $value) {

                $_POST['available_start_dt'] = date("Y-m-d H:i:s",$conf['prize_start']);
                $_POST['available_end_type'] = 1;
                $_POST['available_end_dt'] = date("Y-m-d H:i:s",$conf['end_time']);
                $_POST['admin_uid'] = 0;
                $_POST['uid'] = $uid;
                $_POST['pid2'] = $time."_".$key;
                $_POST['money'] = number_format($value['money'],2,".","");
                $_POST['invest_min_money'] = $value['invest_min_money'];
                $_POST['invest_min_duration'] = $value['invest_min_duration'];
                $_POST['remark'] = $conf['prize_b']['name'];
                $_POST['is_send_to_phone'] = 0;
                $_POST['token'] = create_sinapay_token();
                $rs = A("Home/Prizeapi")->loteriokuponoj();

                if($rs['response_code'] != 1){
                    M()->rollback();
                    E($rs['response_message']);
                }
            }

            M()->commit();

            $this->returnData(0, "{$conf['prize_b']['name']}大红包已到账");
        }catch (\Exception $e){
            $this->returnData(1, $e->getMessage());
        }
    }

    public function h190103_receive_pc()
    {
        try {
            $uid = I('userID', 0, 'int');
            $conf = $this->h190103_conf();
            $prize = $conf['prize_c'];
            $time = $_SERVER['REQUEST_TIME'];

            $time > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            !$uid && $this->returnData(1, ActivityService::PARAM_ERROR);
            $userInfo = D('Home/User', 'Service')->getUserInfo($uid);
            empty($userInfo) && $this->returnData(1, ActivityService::USE_NOT_EXIST);

            $checkRec = A('Service/Activity')->receivedPrize($uid, $prize['name'], 0, $conf['test_time'], $conf['end_time']);
            !empty($checkRec) && $this->returnData(1, ActivityService::HAS_REC);


            $checkInvest = A('Service/Activity')->checkInvest($uid);

            if (($userInfo['reg_time'] > $conf['prize_start']) || $checkInvest) {
                $this->returnData(1, "不好意思，您不符合领取条件，暂无法领取哦");
            }

            //加息券
            $_POST['available_start_dt'] = date("Y-m-d H:i:s", $conf['prize_start']);
            $_POST['available_end_type'] = 1;
            $_POST['available_end_dt'] = date("Y-m-d H:i:s", $conf['end_time']);
            $_POST['admin_uid'] = 0;
            $_POST['uid'] = $uid;
            $_POST['pid2'] = $time;
            $_POST['money'] = number_format($prize['prize_rate'], 2, ".", "");
            $_POST['invest_min_money'] = $prize['invest_min_money'];
            $_POST['invest_min_duration'] = $prize['invest_min_duration'];;
            $_POST['remark'] = $prize['name'];
            $_POST['prize_max_money'] = $prize['prize_max_money'];
            $_POST['is_send_to_phone'] = 0;
            $_POST['token'] = create_sinapay_token();
            //调用发放加息券接口
            $rs = A("Home/Prizeapi")->loteryCoupon();

            if ($rs['response_code'] != 1) {
                $this->returnData(1, $rs['response_message']);
            }
            $this->returnData(0, '领取成功！请至“账户-我的优惠”中查看！');

        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }

    public function h190103_receive_pd()
    {
        try {

            $uid = I('userID', 0, 'int');
            $conf = $this->h190103_conf();
            $prize = $conf['prize_d'];
            $time = $_SERVER['REQUEST_TIME'];

            A('Service/Activity')->dailyPrizeStatus($uid, $time, $conf['test_time'], $conf['end_time'], $prize['name']);

            $_POST['available_start_dt'] = date("Y-m-d H:i:s", $time);
            $_POST['available_end_type'] = 0;
            $_POST['valid_days'] = $prize['valid_days'];
            $_POST['admin_uid'] = 0;
            $_POST['uid'] = $uid;
            $_POST['pid2'] = $time;
            $_POST['money'] = number_format($prize['money'], 2, ".", "");
            $_POST['invest_min_money'] = $prize['invest_min_money'];
            $_POST['invest_min_duration'] = $prize['invest_min_duration'];
            $_POST['remark'] = $prize['name'];
            $_POST['is_send_to_phone'] = 0;
            $_POST['token'] = create_sinapay_token();
            $rs = A("Home/Prizeapi")->loteriokuponoj();

            if ($rs['response_code'] != 1) {
                throw new \Exception($rs['response_message']);
            }
            $this->returnData(0, '领取成功');
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }

    private function h190201_conf()
    {
        return [

            'test_time' => 1548777600,
            'end_time' => 1551369540,
            'asset' => 100000,
            'prize_start'=>1548950400,
            'prize_a' => [
                'name' => '春节大礼包',
                'data' => [
                    ['invest_min_money' => 10000, 'money' => 10, 'invest_min_duration' => 20],
                    ['invest_min_money' => 30000, 'money' => 30, 'invest_min_duration' => 20],
                    ['invest_min_money' => 50000, 'money' => 50, 'invest_min_duration' => 20],

                    ['invest_min_money' => 15000, 'money' => 40, 'invest_min_duration' => 80],
                    ['invest_min_money' => 30000, 'money' => 70, 'invest_min_duration' => 80],
                    ['invest_min_money' => 40000, 'money' => 100, 'invest_min_duration' => 80],
                    ['invest_min_money' => 60000, 'money' => 150, 'invest_min_duration' => 80],

                    ['invest_min_money' => 10000, 'money' => 40, 'invest_min_duration' => 150],
                    ['invest_min_money' => 20000, 'money' => 80, 'invest_min_duration' => 150],
                    ['invest_min_money' => 40000, 'money' => 200, 'invest_min_duration' => 150],
                    ['invest_min_money' => 60000, 'money' => 300, 'invest_min_duration' => 150]
                ]
            ],
            'prize_b' => [
                'name' => '春节VIP专享大红包',
                'invest_min_money' => 100000,
                'data' => [
                    [ 'money' => 118, 'invest_min_duration' => 20],
                    [ 'money' => 288, 'invest_min_duration' => 80],
                    [ 'money' => 588, 'invest_min_duration' => 150],
                    [ 'money' => 888, 'invest_min_duration' => 300],
                ]
            ],
            'prize_c' => [
                'name' => '收益节节高',
                'invest_min_duration' => 20,
                'prize_max_money' => 10000,
                'valid_days' => 7,
                'data'=>[
                    [ 'need'=>100, 'invest_min_money' => 100, 'prize_rate' => 0.5,'id'=>1,'receive'=>0],
                    [ 'need'=>10000,'invest_min_money' => 10000, 'prize_rate' => 0.8 ,'id'=>2,'receive'=>0],
                    [  'need'=>30000,'invest_min_money' => 30000, 'prize_rate' => 1,'id'=>3,'receive'=>0],
                    [ 'need'=>50000, 'invest_min_money' => 50000, 'prize_rate' => 1.5,'id'=>4,'receive'=>0]
                ]
            ],
            'prize_d' => ['name' => '招财节活动每日分享红包', 'money' => 12, 'invest_min_money' => 2000, 'invest_min_duration' => 80, 'valid_days' => 3],
        ];
    }

    public function h190201_status()
    {
        try {
            $uid = I('userID', 0, 'int');
            $conf = $this->h190201_conf();
            $now = $_SERVER['REQUEST_TIME'];
            $now > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);

            $has_invest = 0;
            $userAsset = 0;
            $investActivity = 0;
            $pa['receive'] = 0;
            $pb['receive'] = 0;
            $pe['receive'] = 0;
            $pc = $conf['prize_c']['data'];
            $pcRec = [];
            $sendTotal = M('Prize')->where("`source` in('{$conf['prize_a']['name']}','{$conf['prize_b']['name']}')  and `insert_time` >= {$conf['test_time']}")->sum('money');
            if ($uid) {

                $userInfo = D('Home/User', 'Service')->getUserInfo($uid);
                empty($userInfo) && $this->returnData(1, '用户不存在');
                $checkInvest =A('Service/Activity')->checkInvest($uid);
                $checkInvest &&   $has_invest = 1;

                $activityPrize = A('Service/Activity')->receivedPrize($uid, '', 0, $conf['test_time'], $conf['end_time']);
                if ($activityPrize) {
                    foreach ($activityPrize as $key => $value) {
                        $value['source'] == $conf['prize_a']['name'] && $pa['receive'] = 1;
                        $value['source'] == $conf['prize_b']['name'] && $pb['receive'] = 1;
                        $value['source'] == $conf['prize_c']['name'] &&  $pcRec[] = $value['money'] ;
                    }

                    foreach ($pc as $kk=>$vv){
                        in_array($vv['prize_rate'],$pcRec) && $pc[$kk]['rec'] = 1;
                    }
                }
                $moneyInfo = getUserMoneyInfo($uid);
                $userAsset = $moneyInfo['total'];
                //七龙猪领取判断
                $checkRecPe = json_decode(A('Service/Activity')->curlPost(operate_host . "/Activity/190201_search_qlz", ['uid' => $uid]), true);
                !$checkRecPe['code']  &&  $this->returnData(1, $checkRecPe['message']);
                $checkRecPe['data'] &&   $pe['receive'] = 1;
                $investActivity = A('Service/Activity')->totalInvest($uid,$conf['prize_start'],$conf['end_time']);

            }

            $this->returnData(0, 'ok', [
                'prize_a' => $pa,
                'prize_b' => $pb,
                'prize_c' => $pc,
                'prize_e' => $pe,
                'asset' => $userAsset,
                'has_invest' => $has_invest,
                'send_total' => $sendTotal,
                'invest_activity' => $investActivity,
            ]);

        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }

    }

    public function h190201_rec_pa()
    {
        try {
            $uid = I('userID', 0, 'int');
            $conf = $this->h190201_conf();
            $prize = $conf['prize_a'];
            $time = $_SERVER['REQUEST_TIME'];
            $time > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            !$uid && $this->returnData(1, ActivityService::PARAM_ERROR);

            $checkRec = A('Service/Activity')->receivedPrize($uid, $prize['name'], 0, $conf['test_time']);
            $checkRec && $this->returnData(1, ActivityService::HAS_REC);

            M()->startTrans();
            foreach ($prize['data'] as $key => $value) {

                $_POST['available_start_dt'] = date("Y-m-d H:i:s", $conf['prize_start']);
                $_POST['available_end_type'] = 1;
                $_POST['available_end_dt'] = date("Y-m-d H:i:s", $conf['end_time']);
                $_POST['admin_uid'] = 0;
                $_POST['uid'] = $uid;
                $_POST['pid2'] = $time . "_" . $key;
                $_POST['money'] = number_format($value['money'], 2, ".", "");
                $_POST['invest_min_money'] = $value['invest_min_money'];
                $_POST['invest_min_duration'] = $value['invest_min_duration'];
                $_POST['remark'] = $prize['name'];
                $_POST['is_send_to_phone'] = 0;
                $_POST['token'] = create_sinapay_token();
                $rs = A("Home/Prizeapi")->loteriokuponoj();
                if ($rs['response_code'] != 1) {
                    M()->rollback();
                    E($rs['response_message']);
                }
            }
            M()->commit();
            $this->returnData(0, "{$prize['name']}已到账");

        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }

    public function h190201_rec_pb()
    {
        try{
            $uid = I('userID', 0, 'int');
            $conf = $this->h190201_conf();
            $time = $_SERVER['REQUEST_TIME'];
            $time > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            !$uid && $this->returnData(1, ActivityService::PARAM_ERROR);

            $moneyInfo = getUserMoneyInfo($uid);
            $userAsset = $moneyInfo['total'];

            ($userAsset < $conf['asset']) &&  $this->returnData(1, "您的总资产金额未满{$conf['asset']}元，暂无法领取哦！");

            $checkRec = A('Service/Activity')->receivedPrize($uid,$conf['prize_b']['name'],0, $conf['test_time']);
            !empty($checkRec) && $this->returnData(1, ActivityService::HAS_REC);

            M()->startTrans();

            foreach ($conf['prize_b']['data'] as $key => $value) {

                $_POST['available_start_dt'] = date("Y-m-d H:i:s",$conf['prize_start']);
                $_POST['available_end_type'] = 1;
                $_POST['available_end_dt'] = date("Y-m-d H:i:s",$conf['end_time']);
                $_POST['admin_uid'] = 0;
                $_POST['uid'] = $uid;
                $_POST['pid2'] = $time."_".$key;
                $_POST['money'] = number_format($value['money'],2,".","");
                $_POST['invest_min_money'] =  $conf['prize_b']['invest_min_money'];
                $_POST['invest_min_duration'] = $value['invest_min_duration'];
                $_POST['remark'] = $conf['prize_b']['name'];
                $_POST['is_send_to_phone'] = 0;
                $_POST['token'] = create_sinapay_token();
                $rs = A("Home/Prizeapi")->loteriokuponoj();

                if($rs['response_code'] != 1){
                    M()->rollback();
                    E($rs['response_message']);
                }
            }
            M()->commit();
            $this->returnData(0, "{$conf['prize_b']['name']}大红包已到账");
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }

    public function h190201_rec_pc()
    {
           try{

               $uid = I('userID', 0, 'int');
               $pid = I('pid', 0, 'int');
               $conf  = $this->h190201_conf();
               $time = $_SERVER['REQUEST_TIME'];
               $index = $pid -1;
               $prize = $conf['prize_c']['data'][$index];
               if(!$uid || empty($prize)){ $this->returnData(1, ActivityService::PARAM_ERROR); }
               $userInfo = D('Home/User', 'Service')->getUserInfo($uid);
               empty($userInfo) && $this->returnData(1, ActivityService::USE_NOT_EXIST);

               $checkRec = A('Service/Activity')->receivedPrize($uid,$conf['prize_c']['name'],$prize['prize_rate'], $conf['test_time']);
               !empty($checkRec) && $this->returnData(1, ActivityService::HAS_REC);

               $totalInvest = A('Service/Activity')->totalInvest($uid,$conf['prize_start'],$conf['end_time']);
               $totalInvest < $prize['need'] && $this->returnData(1, "您的累投金额还未达到{$prize['need']}元，暂无法领取哦！");

               //加息券
               $_POST['available_start_dt'] = date("Y-m-d H:i:s", $time);
               $_POST['available_end_type'] = 0;
               $_POST['valid_days'] = $conf['prize_c']['valid_days'];
               $_POST['admin_uid'] = 0;
               $_POST['uid'] = $uid;
               $_POST['pid2'] = $time;
               $_POST['money'] = number_format($prize['prize_rate'], 2, ".", "");
               $_POST['invest_min_money'] = $prize['invest_min_money'];
               $_POST['invest_min_duration'] = $conf['prize_c']['invest_min_duration'];
               $_POST['remark'] = $conf['prize_c']['name'];
               $_POST['is_send_to_phone'] = 0;
               $_POST['token'] = create_sinapay_token();
               $_POST['prize_max_money'] = $conf['prize_c']['prize_max_money'];
               //调用发放加息券接口
               $rs = A("Home/Prizeapi")->loteryCoupon();

               if ($rs['response_code'] != 1) {
                   $this->returnData(1, $rs['response_message']);
               }
               $this->returnData(0, '领取成功！请至“账户-我的优惠”中查看！');

           }catch (\Exception $e){

           }
    }

    public function h190201_rec_pe()
    {
        try {
            $uid = I('userID', 0, 'int');
            $conf = $this->h190201_conf();

            $now = $_SERVER['REQUEST_TIME'];
            $now > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            $userInfo = M('User')->where("`uid` = {$uid}")->find();
            empty($userInfo) && $this->returnData(1, ActivityService::USE_NOT_EXIST);
            $checkRec = json_decode(A('Service/Activity')->curlPost(operate_host . "/Activity/190201_search_qlz", ['uid' => $uid]), true);
            !$checkRec['code']  &&  $this->returnData(1, $checkRec['message']);
            $checkRec['data'] &&  $this->returnData(1, ActivityService::HAS_REC);

            //判断领取条件
            $userInfo['last_login_time'] < $conf['prize_start'] && $this->returnData(1, '缺少一星猪');

            $twoStar = json_decode(A('Service/Activity')->curlPost(operate_host . "/Activity/common_share_log", ['uid' => $uid,'remark'=>'201902qlz']), true);
            !$twoStar['code']  &&  $this->returnData(1, $twoStar['message']);
            empty($twoStar['data']) &&  $this->returnData(1, '缺少二星猪');

            $threeStar = M()->query("select `id`,`uid` from `tp_user` where `pid` = {$uid} and `reg_time` >= {$conf['prize_start']}");
            empty($threeStar) && $this->returnData(1, '缺少三星猪');

            $fourStar = 0;

            foreach ($threeStar as $ki => $vi){
                $inviteUid[] =  $vi['uid'];
            }
            $friendInvest = M()->query("SELECT id FROM tp_borrow_investor WHERE investor_uid in(".implode(",",$inviteUid).") AND `status` IN (10,12,40,50)");

            if($friendInvest){
                $fourStar = 1;
            }

            !$fourStar &&  $this->returnData(1, "缺少四星猪");

            $sevenStar = 0;
            $sevenStarData = A('Service/Activity')->investList($uid,$conf['prize_start'],$conf['end_time']);
            $sevenInvest = 0;
            //test
            foreach ($sevenStarData as $kf =>$vf){

                if($vf['borrow_duration'] >= 80){
                    $sevenInvest += $vf['investor_capital'];
                }
            }

            if($sevenInvest >= 10000){
                $sevenStar = 1;
            }

            !$sevenStar && $this->returnData(1, "缺少七星猪");

            $fiveStar = M('sign_in_form')->where("user_id = '{$uid}' and `sign_in_time` >= '{$conf['prize_start']}'")->count();
            $fiveStar < 3  && $this->returnData(1, "缺少五星猪");

            $sixStar = M('user_moneylog')->where("`user_id` = {$uid} and `add_time` >= '{$conf['prize_start']}' and  `category` = 10  and `status` = 2 ")->find();
            empty($sixStar) && $this->returnData(1, "缺少六星猪");

            $rec = json_decode(A('Service/Activity')->curlPost(operate_host . "/Activity/190201_add_qlz", ['uid' => $uid]), true);
            if(!$rec['code']){
                throw new \Exception($checkRec['message']);
            }
            $this->returnData(0, 'ok');

        }catch (\Exception $e){
            $this->returnData(1, $e->getMessage());
        }
    }

    public function  h190201_fight_group()
    {
        try {

            $uid = I('userID', 0, 'int');
            $friendId= I('friendID', 0, 'int');
            $goodsId= I('goodsID', 0, 'int');
            $conf = $this->h190201_conf();

            $time = $_SERVER['REQUEST_TIME'];
            $time > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            if(!$uid || !$friendId || !$goodsId){
                $this->returnData(1, ActivityService::PARAM_ERROR);
            }
            $friendInfo = M('User')->where("`uid` = {$friendId}")->find();
         //   echo "<pre>";print_r($friendInfo);die;
            empty($friendInfo) && $this->returnData(1, ActivityService::USE_NOT_EXIST);
            $friendInfo['reg_time'] < $conf['prize_start'] && $this->returnData(1, '好友不是新注册用户');
            $checkInvest = A('Service/Activity')->checkInvest($friendId);
            $checkInvest&& $this->returnData(1, "好友是已投资用户");

            $res = json_decode(A('Service/Activity')->curlPost(operate_host . "/Activity/190201_fight_group", ['uid' => $uid,'fuid'=>$friendId,'gid'=>$goodsId,'source'=>'zcj']), true);
            if(!$res['code']){
                throw new \Exception($res['message']);
            }
            $this->returnData(0, 'ok');
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }
    public function  h190201_group_list()
    {
        try {

            $uid = I('userID', 0, 'int');
            $conf = $this->h190201_conf();
            $time = $_SERVER['REQUEST_TIME'];
            $time > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            !$uid && $this->returnData(1, ActivityService::PARAM_ERROR);

            $groupData = json_decode(A('Service/Activity')->curlPost(operate_host . "/Activity/190201_group_list", ['uid' => $uid,'source'=>'zcj']), true);
            if(!$groupData['code']){
                throw new \Exception($groupData['message']);
            }
           $res  = [];
           $speed = 1;
           $groupList = $groupData['data'];
           if($groupList){
               $speed =2;
               $friendsInfo = [];
               $investInfo = [];
               $goods = [
                   1 => ['name' => '小超新年抱枕毯','need' =>3000],
                   2 => ['name' => '小超新年卫衣','need' =>5000],
                   3 => ['name' => '华为音响','need' =>10000],
                   4 => ['name' => '华为智能体脂秤','need' =>15000],
                   5 => ['name' => '华为智能手环','need' =>20000],
               ];

               foreach ($groupList as $key=>$value){
                    $friendsUid[] = $value['friend_uid'];
               }

                  //手机 姓名
               $userData = M()->query("select `user_phone`,`real_name`,`uid` from `tp_user` where `uid` in(".implode(",",$friendsUid).")");
               foreach ($userData as $vu){
                   $friendsInfo[$vu['uid']] = $vu;
               }

               //首投金额
               $investData = M()->query("select * from (select `investor_capital`,`investor_uid` from `tp_borrow_investor` where `investor_uid` in(".implode(",",$friendsUid).") and `status` in (10,12,40,50) order by `add_time`) as a group by `investor_uid`");
               foreach ($investData as $vi){
                   $investInfo[$vi['investor_uid']] = $vi['investor_capital'];
              }
              foreach ($groupList as $vg){

                   $name = '未实名';
                   $firstInvest = '未首投';
                   $goodsName = '无';
                   if($friendsInfo[$vg['friend_uid']]['real_name']){
                       $name = $this->substrCut(privacyDecode($friendsInfo[$vg['friend_uid']]['real_name']));
                   }

                   if( $investInfo[$vg['friend_uid']]){
                       $firstInvest  = $investInfo[$vg['friend_uid']];
                       $speedTemp = 3;
                   }

                  if( $investInfo[$vg['friend_uid']] >= $goods[$vg['goods_id']]['need'] ){
                      $goodsName  =  $goods[$vg['goods_id']]['name'];
                      $speedTemp = 4;
                  }
                  if($speedTemp > $speed){
                      $speed = $speedTemp;
                  }
                  $res[]  = [
                      'phone' =>  substr_replace($friendsInfo[$vg['friend_uid']]['user_phone'], '****', 3, 4),
                      'name'  => $name,
                      'firstInvest' =>  $firstInvest,
                      'goods' => $goodsName
                  ];
              }
           }
            $this->returnData(0, 'ok',['list' =>$res,'speed'=>$speed]);
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }

    public function  h190201_qlz_share()
    {
        try {

            $uid = I('userID', 0, 'int');
            $conf = $this->h190201_conf();
            $time = $_SERVER['REQUEST_TIME'];
            $time > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            if(!$uid){
                $this->returnData(1, ActivityService::PARAM_ERROR);
            }

            $res = json_decode(A('Service/Activity')->curlPost(operate_host . "/Activity/common_share_activity", ['uid' => $uid,'remark'=>'201902qlz']), true);
            if(!$res['code']){
                throw new \Exception($res['message']);
            }
            $this->returnData(0, 'ok');
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }

    public function h190201_qlz_status()
    {
        try {
            $uid = I('userID', 0, 'int');
            $conf = $this->h190201_conf();

            $now = $_SERVER['REQUEST_TIME'];
            $now > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            $userInfo = M('User')->where("`uid` = {$uid}")->find();
            empty($userInfo) && $this->returnData(1, ActivityService::USE_NOT_EXIST);
            $checkRec = json_decode(A('Service/Activity')->curlPost(operate_host . "/Activity/190201_search_qlz", ['uid' => $uid]), true);
            !$checkRec['code']  &&  $this->returnData(1, $checkRec['message']);
           // $checkRec['data'] &&  $this->returnData(1, ActivityService::HAS_REC);

            //判断领取条件
            //$userInfo['last_login_time'] < $conf['prize_start'] && $this->returnData(1, '缺少一星猪');

            $twoStar = json_decode(A('Service/Activity')->curlPost(operate_host . "/Activity/common_share_log", ['uid' => $uid,'remark'=>'201902qlz']), true);
            !$twoStar['code']  &&  $this->returnData(1, $twoStar['message']);
            //empty($twoStar['data']) &&  $this->returnData(1, '缺少二星猪');

            $threeStar = M()->query("select `id`,`uid` from `tp_user` where `pid` = {$uid} and `reg_time` >= {$conf['prize_start']}");
          //  empty($threeStar) && $this->returnData(1, '缺少三星猪');
            $fourStar = 0;
            if($threeStar){
                foreach ($threeStar as $ki => $vi){
                    $inviteUid[] =   $vi['uid'];
                }
                $friendInvest = M()->query("SELECT id FROM tp_borrow_investor WHERE investor_uid in(".implode(",",$inviteUid).") AND `status` IN (10,12,40,50)");

                if($friendInvest){
                     $fourStar = 1;
                }
            }


            $sevenStarData = A('Service/Activity')->investList($uid,$conf['prize_start'],$conf['end_time']);

            // empty($fourStar) &&  $this->returnData(1, "缺少四星猪");
            $sevenStar = 0;
            $sevenInvest = 0;
            //test 100
            foreach ($sevenStarData as $kf =>$vf){
                if($vf['borrow_duration'] >= 80){
                    $sevenInvest += $vf['investor_capital'];
                }
            }
            if($sevenInvest >= 10000){
                $sevenStar = 1;
            }
           // !$sevenStar && $this->returnData(1, "缺少七星猪");

            $fiveStar = M('sign_in_form')->where("user_id = '{$uid}' and `sign_in_time` >= '{$conf['prize_start']}'")->count();
            //$fiveStar < 3  && $this->returnData(1, "缺少五星猪");

            $sixStar = M('user_moneylog')->where("`user_id` = {$uid} and `add_time` >= '{$conf['prize_start']}' and  `category` = 10  and `status` = 2 ")->find();
         //   empty($sixStar) && $this->returnData(1, "缺少六星猪");

            $this->returnData(0, 'ok',[
                'has_rec' =>  $checkRec['data'] ? 1 : 0,
                'one' => $userInfo['last_login_time'] > $conf['prize_start'] ? 1 : 0,
                'two' => $twoStar['data'] ? 1 : 0,
                'three' => $threeStar ? 1 : 0,
                'four' =>  $fourStar ? 1 : 0,
                'five' => $fiveStar < 3 ? 0 : 1,
                'six' =>$sixStar ? 1 : 0,
                'seven' => $sevenStar,
            ]);

        }catch (\Exception $e){
            $this->returnData(1, $e->getMessage());
        }
    }

    /**2019女神节活动
     * @return array
     */
    private function h190301_conf()
    {
        return [

            'start_time' => 1551369600,
            'end_time' => 1552838340,
            'group_source' => 'nsj',
            'group_end' => 1554047940,
            'prize_a' => [
                'name' => '女神节大礼包',
                'data' => [

                    ['invest_min_money' => 10000, 'money' => 10, 'invest_min_duration' => 20],
                    ['invest_min_money' => 30000, 'money' => 30, 'invest_min_duration' => 20],
                    ['invest_min_money' => 50000, 'money' => 50, 'invest_min_duration' => 20],

                    ['invest_min_money' => 15000, 'money' => 40, 'invest_min_duration' => 80],
                    ['invest_min_money' => 40000, 'money' => 100, 'invest_min_duration' => 80],
                    ['invest_min_money' => 60000, 'money' => 150, 'invest_min_duration' => 80],

                    ['invest_min_money' => 10000, 'money' => 40, 'invest_min_duration' => 150],
                    ['invest_min_money' => 20000, 'money' => 80, 'invest_min_duration' => 150],
                    ['invest_min_money' => 40000, 'money' => 200, 'invest_min_duration' => 150],
                    ['invest_min_money' => 60000, 'money' => 300, 'invest_min_duration' => 150]
                ]
            ],
            'prize_b' => [
                //加息券
                'name' => '3月回归礼',
                'data' => [
                    ['invest_min_money' => 1000, 'prize_rate' => 1.88, 'invest_min_duration' => 20, 'prize_max_money' => 10000],
                    ['invest_min_money' => 1000, 'prize_rate' => 2.88, 'invest_min_duration' => 20, 'prize_max_money' => 10000],
                ]
            ],
            'prize_c' => [
                'name' => '女神专享收益券',
                'invest_min_duration' => 20,
                'valid_days' => 7,
                'prize_max_money' => 10000,
                'data' => [
                    ['invest_min_money' => 3800, 'prize_rate' => 0.3],
                    ['invest_min_money' => 38000, 'prize_rate' => 0.8],
                ]
            ],
            'prize_d' => [
                //加息券
                'name' => '男神女神PK红包',
                'valid_days' => 7,
                'cash' => ['money' => 60, 'invest_min_money' => 10000, 'invest_min_duration' => 80],
                'rate' => ['invest_min_money' => 100, 'prize_rate' => 1, 'invest_min_duration' => 20, 'prize_max_money' => 10000],
            ],
            'prize_e' => ['name' => '女神节活动每日分享红包', 'money' => 10, 'invest_min_money' => 2000, 'invest_min_duration' => 80, 'valid_days' => 3],
        ];

    }

    public function h190301_status()
    {
        try {
            $uid = I('userID', 0, 'int');
            $conf = $this->h190301_conf();
            $now = $_SERVER['REQUEST_TIME'];
            $now > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);

            $has_invest = 0;
            $noInvestDays = 0;
            $pa['receive'] = 0;
            $pb['receive'] = 0;
            $pc['receive'] = 0;
            $pd['receive'] = 0;
            $sex = 0;
            if ($uid) {

                $userInfo = M('User')->where("`uid` = {$uid}")->find();
                empty($userInfo) && $this->returnData(1, ActivityService::USE_NOT_EXIST);

                if($userInfo['idcard']){
                    $getSex = $this->getSexByIdCarad($userInfo['idcard']);
                    $sex = $getSex ? 1:2;
                }
                $investData = A('Service/Activity')->userFirstLatestInvest($uid);
                $latestInvest = $investData['latest'];


                if ($latestInvest) {
                    $has_invest = 1;
                    $num = ($conf['end_time'] - $latestInvest['add_time']) / 86400;
                    if ($num > 0 && $num < 1) {
                        $num = 1;
                    }
                    $noInvestDays = (int)$num;
                }

                $activityPrize = A('Service/Activity')->receivedPrize($uid, '', 0, $conf['start_time']);
                if ($activityPrize) {
                    foreach ($activityPrize as $key => $value) {
                        $value['source'] == $conf['prize_a']['name'] && $pa['receive'] = 1;
                        $value['source'] == $conf['prize_b']['name'] && $pb['receive'] = 1;
                        $value['source'] == $conf['prize_c']['name'] && $pc['receive'] = 1;
                        $value['source'] == $conf['prize_d']['name'] && $pd['receive'] = 1;
                    }
                }
            }

            $this->returnData(0, 'ok', [
                'prize_a' => $pa,
                'prize_b' => $pb,
                'prize_c' => $pc,
                'prize_d' => $pd,
                'no_invest_days' => $noInvestDays,
                'has_invest' => $has_invest,
                'sex' => $sex
            ]);

        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }

    }

    public function h190301_rec_pa()
    {
        try {
            $uid = I('userID', 0, 'int');
            $conf = $this->h190301_conf();
            $prize = $conf['prize_a'];
            $time = $_SERVER['REQUEST_TIME'];
            $time > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            !$uid && $this->returnData(1, ActivityService::PARAM_ERROR);

            $userInfo = D('Home/User', 'Service')->getUserInfo($uid);
            empty($userInfo) && $this->returnData(1, ActivityService::USE_NOT_EXIST);

            $investData = A('Service/Activity')->userFirstLatestInvest($uid);
            $latestInvest = $investData['latest'];

            !$latestInvest && $this->returnData(1, ActivityService::NO_INVEST);

            $num = ($conf['end_time'] - $latestInvest['add_time']) / 86400;
            $noInvestDays = (int)$num;
            $noInvestDays >= 180 && $this->returnData(1, "180内未投资，暂无法领取哦");

            $checkRec = A('Service/Activity')->receivedPrize($uid, $prize['name'], 0, $conf['start_time']);
            $checkRec && $this->returnData(1, ActivityService::HAS_REC);

            M()->startTrans();
            foreach ($prize['data'] as $key => $value) {

                $_POST['available_start_dt'] = date("Y-m-d H:i:s", $conf['start_time']);
                $_POST['available_end_type'] = 1;
                $_POST['available_end_dt'] = date("Y-m-d H:i:s", $conf['end_time']);
                $_POST['admin_uid'] = 0;
                $_POST['uid'] = $uid;
                $_POST['pid2'] = $time . "_" . $key;
                $_POST['money'] = number_format($value['money'], 2, ".", "");
                $_POST['invest_min_money'] = $value['invest_min_money'];
                $_POST['invest_min_duration'] = $value['invest_min_duration'];
                $_POST['remark'] = $prize['name'];
                $_POST['is_send_to_phone'] = 0;
                $_POST['token'] = create_sinapay_token();
                $rs = A("Home/Prizeapi")->loteriokuponoj();
                if ($rs['response_code'] != 1) {
                    M()->rollback();
                    E($rs['response_message']);
                }
            }
            M()->commit();
            $this->returnData(0, "{$prize['name']}已到账");

        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }

    public function h190301_rec_pb()
    {
        try {

            $uid = I('userID', 0, 'int');
            $conf = $this->h190301_conf();
            $prizeb = $conf['prize_b'];
            $time = $_SERVER['REQUEST_TIME'];
            $time > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            !$uid && $this->returnData(1, ActivityService::PARAM_ERROR);

            $userInfo = M('User')->where("`uid` = {$uid}")->find();
            empty($userInfo) && $this->returnData(1, ActivityService::USE_NOT_EXIST);

            $checkRec = A('Service/Activity')->receivedPrize($uid, $prizeb['name'], 0, $conf['start_time']);
            $checkRec && $this->returnData(1, ActivityService::HAS_REC);

            $noInvestDays = 0;
            $latestInvest = A('Service/Activity')->userFirstLatestInvest($uid);
            $latestInvest = $latestInvest['latest'];

            if ($latestInvest) {
                $noInvestDays = (int)(($conf['end_time'] - $latestInvest['add_time']) / 86400);
            }
            if ($noInvestDays >= 360) {
                $prize = $prizeb['data'][1];
            } elseif ($noInvestDays >= 180) {
                $prize = $prizeb['data'][0];
            } else {
                $this->returnData(1, "不够领取条件");
            }

            $_POST['available_start_dt'] = date("Y-m-d H:i:s", $conf['start_time']);
            $_POST['available_end_type'] = 1;
            $_POST['available_end_dt'] = date("Y-m-d H:i:s", $conf['end_time']);
            $_POST['admin_uid'] = 0;
            $_POST['uid'] = $uid;
            $_POST['pid2'] = $time;
            $_POST['money'] = number_format($prize['prize_rate'], 2, ".", "");
            $_POST['invest_min_money'] = $prize['invest_min_money'];
            $_POST['invest_min_duration'] = $prize['invest_min_duration'];;
            $_POST['remark'] = $prizeb['name'];
            $_POST['prize_max_money'] = $prize['prize_max_money'];
            $_POST['is_send_to_phone'] = 0;
            $_POST['token'] = create_sinapay_token();
            //调用发放加息券接口
            $rs = A("Home/Prizeapi")->loteryCoupon();

            if ($rs['response_code'] != 1) {
                E($rs['response_message']);
            }
            $this->returnData(0, "恭喜您获得{$prize['prize_rate']}%收益券，请至“账户-我的优惠”中查看！");
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }
    public function h190301_rec_pc()
    {
        try {

            $uid = I('userID', 0, 'int');
            $conf = $this->h190301_conf();
            $prize = $conf['prize_c'];
            $time = $_SERVER['REQUEST_TIME'];
            $time > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            !$uid && $this->returnData(1, ActivityService::PARAM_ERROR);

            $userInfo = M('User')->where("`uid` = {$uid}")->find();
            empty($userInfo) && $this->returnData(1, ActivityService::USE_NOT_EXIST);
            empty($userInfo['idcard']) && $this->returnData(1, '未实名认证');

            $sex = $this->getSexByIdCarad($userInfo['idcard']);
            $sex && $this->returnData(1, '您是男神，暂无法领取女神专享收益券哦！');

            $checkRec = A('Service/Activity')->receivedPrize($uid, $prize['name'], 0, $conf['start_time']);
            $checkRec && $this->returnData(1, ActivityService::HAS_REC);


            M()->startTrans();
            foreach ($prize['data'] as $key => $value) {

                $_POST['available_start_dt'] = date("Y-m-d H:i:s", $time);
                $_POST['available_end_type'] = 0;
                $_POST['valid_days'] = $prize['valid_days'];
                $_POST['admin_uid'] = 0;
                $_POST['uid'] = $uid;
                $_POST['pid2'] = $time.'_'.$key;
                $_POST['money'] = number_format($value['prize_rate'], 2, ".", "");
                $_POST['invest_min_money'] = $value['invest_min_money'];
                $_POST['invest_min_duration'] = $prize['invest_min_duration'];
                $_POST['remark'] = $prize['name'];
                $_POST['is_send_to_phone'] = 0;
                $_POST['prize_max_money'] = $prize['prize_max_money'];
                $_POST['token'] = create_sinapay_token();
                //调用发放加息券接口
                $rs = A("Home/Prizeapi")->loteryCoupon();
                if ($rs['response_code'] != 1) {
                    M()->rollback();
                    E($rs['response_message']);
                }
            }
            M()->commit();
            $this->returnData(0, "恭喜您获得2张女神专享收益券！");
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }

    public function h190301_pk()
    {
        try {

            $uid = I('userID', 0, 'int');
            $conf = $this->h190301_conf();
           // $time = $_SERVER['REQUEST_TIME'];
            $time > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);

            $myInvest = 0;
            $myTeam = '';
            $realNameStatus = 0;
            if($uid){
                $userInfo = M('User')->where("`uid` = {$uid}")->find();
                if($userInfo){
                    $userInfo['idcard'] && $realNameStatus = 1;
                    if($userInfo['idcard']){
                       $myTeam = $this->getSexByIdCarad($userInfo['idcard']) ? '男神队' : '女神队';
                    }
                }
            }

            $sql = "SELECT
                        a.*, b.`user_phone`,
                        b.`idcard`
                    FROM
                        (
                            SELECT
                                sum(`investor_capital`) AS t,
                                `investor_uid`,
                                max(`add_time`) AS mt
                            FROM
                                `tp_borrow_investor` 
                            WHERE
                                `status` IN (10, 12, 40, 50)
                            AND `add_time` >= {$conf ['start_time']}
                            AND `add_time` <= {$conf ['end_time']}
                            GROUP BY
                                `investor_uid`
                            ORDER BY
                                t DESC,
                                mt ASC
                        ) AS a 
                    LEFT JOIN `tp_user` b ON a.investor_uid = b.uid";
            //echo $sql;exit();
            $investData  =  M()->query($sql);
            $menTeamCapital   =  0;
            $womenTeamCapital =  0;
            $list = [];
            if($investData) {

                foreach ($investData as $key => $value) {
                    $sex = $this->getSexByIdCarad($value['idcard']);
                    if($sex){
                        $menTeamCapital += $value['t'];
                        $teamName = '男神队';
                    }else{
                        $womenTeamCapital += $value['t'];
                        $teamName = '女神队';
                    }
                    ($value['investor_uid'] == $uid) && $myInvest = $value['t'];
                    if(count($list) < 5){
                        $list[] = [
                            'invest'=>sprintf("%.2f", $value['t']),
                            'team'=>$teamName,
                            'ico'=>A('Service/Activity')->UserIco($value['investor_uid']),
                            'phone'=>A('Service/Activity')->encryptPhone($value['user_phone']),
                            'uid'=>$value['investor_uid']
                        ];
                    }
                }
            }
            $this->returnData(0, "ok",[
                'real_name_status' => $realNameStatus,
                'my_invest' =>$myInvest,
                'my_team' =>$myTeam,
                'men_team_capital' => $menTeamCapital + 500000,
                'women_team_capital' => $womenTeamCapital + 500000,
                'list' => $list,
            ]);
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }
    public function h190301_crontab()
    {
        try {

            $conf = $this->h190301_conf();
            $prize = $conf['prize_d'];
            $time = $_SERVER['REQUEST_TIME'];

            $checkRec = M()->query("select count(*) as t from `tp_prize` where `source` = '{$prize['name']}' and `insert_time` >= '{$conf['start_time']}' ");
            if($checkRec[0]['t']) {
                $this->returnData(0,"已发送{$checkRec[0]['t']}");
            }else {
                $sql = "SELECT
                        a.*, b.`user_phone`,
                        b.`idcard`
                    FROM
                        (
                            SELECT
                                sum(`investor_capital`) AS t,
                                `investor_uid`
                            FROM
                                `tp_borrow_investor` 
                            WHERE
                                `status` IN (10, 12, 40, 50)
                            AND `add_time` >= {$conf ['start_time']}
                            AND `add_time` <= {$conf ['end_time']}
                            GROUP BY
                                `investor_uid`
                        ) AS a 
                    LEFT JOIN `tp_user` b ON a.investor_uid = b.uid";
               // echo $sql;exit;
                $investData = M()->query($sql);
                $menTeamCapital = 0;
                $womenTeamCapital = 0;
                $menUid = [];
                $womenUid = [];
                if ($investData) {
                    foreach ($investData as $key => $value) {
                        $sex = $this->getSexByIdCarad($value['idcard']);
                        if ($sex) {
                            $menTeamCapital += $value['t'];
                            $menUid[] = $value['investor_uid'];
                        } else {
                            $womenTeamCapital += $value['t'];
                            $womenUid[] = $value['investor_uid'];
                        }
                    }

                    if ($menTeamCapital > $womenTeamCapital) {
                        $winner = $menUid;
                    } elseif ($menTeamCapital < $womenTeamCapital) {
                        $winner = $womenUid;
                    } else {
                        $winner = array_merge($menUid, $womenUid);
                    }
                    foreach ($womenUid as $wi => $vwi) {
                        //加息券
                        $_POST['available_start_dt'] = date("Y-m-d H:i:s", $time);
                        $_POST['available_end_type'] = 0;
                        $_POST['valid_days'] = $prize['valid_days'];
                        $_POST['admin_uid'] = 0;
                        $_POST['uid'] = $vwi;
                        $_POST['pid2'] = $time;
                        $_POST['money'] = number_format($prize['rate']['prize_rate'], 2, ".", "");
                        $_POST['invest_min_money'] = $prize['rate']['invest_min_money'];
                        $_POST['invest_min_duration'] = $prize['rate']['invest_min_duration'];
                        $_POST['remark'] = $prize['name'];
                        $_POST['is_send_to_phone'] = 0;
                        $_POST['token'] = create_sinapay_token();
                        $_POST['prize_max_money'] = $prize['rate']['prize_max_money'];
                        $resA = A("Home/Prizeapi")->loteryCoupon();





//                        $_POST['available_start_dt'] = date("Y-m-d H:i:s", $conf['start_time']);
//                        $_POST['available_end_type'] = 1;
//                        $_POST['available_end_dt'] = date("Y-m-d H:i:s", $conf['end_time']);
//                        $_POST['admin_uid'] = 0;
//                        $_POST['uid'] = $uid;
//                        $_POST['pid2'] = $time . "_" . $key;
//                        $_POST['money'] = number_format($value['money'], 2, ".", "");
//                        $_POST['invest_min_money'] = $value['invest_min_money'];
//                        $_POST['invest_min_duration'] = $value['invest_min_duration'];
//                        $_POST['remark'] = $prize['name'];
//                        $_POST['is_send_to_phone'] = 0;
//                        $_POST['token'] = create_sinapay_token();
//                        $rs = A("Home/Prizeapi")->loteriokuponoj();




                        //现金券
                        $_POST['available_start_dt'] = date("Y-m-d H:i:s", $time);
                        $_POST['available_end_type'] = 1;
                        $_POST['available_end_dt'] = date("Y-m-d H:i:s", 1553443200);
                        $_POST['admin_uid'] = 0;
                        $_POST['uid'] = $vwi;
                        $_POST['pid2'] = $time;
                        $_POST['money'] = number_format($prize['cash']['money'], 2, ".", "");
                        $_POST['invest_min_money'] = $prize['cash']['invest_min_money'];
                        $_POST['invest_min_duration'] = $prize['cash']['invest_min_duration'];
                        $_POST['remark'] = $prize['name'];
                        $_POST['is_send_to_phone'] = 0;
                        $_POST['token'] = create_sinapay_token();
                        $resB = A("Home/Prizeapi")->loteriokuponoj();
                    }
                    $this->returnData(0, 'ok',['winner'=>$winner]);
                }
            }
        }catch(\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }

    public function  h190301_fight_group()
    {
        try {

            $uid = I('userID', 0, 'int');
            $friendId= I('friendID', 0, 'int');
            $goodsId= I('goodsID', 0, 'int');
            $conf = $this->h190301_conf();
            $time = $_SERVER['REQUEST_TIME'];
            $time > $conf['group_end'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            if(!$uid || !$friendId || !$goodsId){
                $this->returnData(1, ActivityService::PARAM_ERROR);
            }
            $friendInfo = M('User')->where("`uid` = {$friendId}")->find();
            //   echo "<pre>";print_r($friendInfo);die;
            empty($friendInfo) && $this->returnData(1, ActivityService::USE_NOT_EXIST);
            $friendInfo['reg_time'] < $conf['start_time'] && $this->returnData(1, '好友不是新注册用户');
            $checkInvest = A('Service/Activity')->checkInvest($friendId);
            $checkInvest&& $this->returnData(1, "好友是已投资用户");

            $res = json_decode(A('Service/Activity')->curlPost(operate_host . "/Activity/190201_fight_group", ['uid' => $uid,'fuid'=>$friendId,'gid'=>$goodsId,'source'=>$conf['group_source']]), true);
            if(!$res['code']){
                throw new \Exception($res['message']);
            }
            $this->returnData(0, 'ok');
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }
    public function  h190301_group_list()
    {
        try {
            $uid = I('userID', 0, 'int');
            $conf = $this->h190301_conf();
            $time = $_SERVER['REQUEST_TIME'];
            $time > $conf['group_end'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            !$uid && $this->returnData(1, ActivityService::PARAM_ERROR);

            $groupData = json_decode(A('Service/Activity')->curlPost(operate_host . "/Activity/190201_group_list", ['uid' => $uid,'source'=>$conf['group_source']]), true);

            if(!$groupData['code']){
                throw new \Exception($groupData['message']);
            }
            $res  = [];
            $speed = 1;
            $groupList = $groupData['data'];
            if($groupList){
                $speed =2;
                $friendsInfo = [];
                $investInfo = [];
                $goods = [
                    1 => ['name' => '小超金猪储蓄罐','need' =>2000],
                    2 => ['name' => '小超情侣抱枕毯','need' =>3000],
                    3 => ['name' => '小超卫衣','need' =>5000],
                    4 => ['name' => '小超保温杯','need' =>5000],
                    5 => ['name' => '华为无线迷你音响','need' =>10000],
                    6 => ['name' => '华为智能体脂秤','need' =>15000],
                    7 => ['name' => '华为荣耀手环','need' =>20000],
                ];

                foreach ($groupList as $key=>$value){
                    $friendsUid[] = $value['friend_uid'];
                }

                //手机 姓名
                $userData = M()->query("select `user_phone`,`real_name`,`uid` from `tp_user` where `uid` in(".implode(",",$friendsUid).")");
                foreach ($userData as $vu){
                    $friendsInfo[$vu['uid']] = $vu;
                }

                //首投金额
                $investData = M()->query("select * from (select `investor_capital`,`investor_uid` from `tp_borrow_investor` where `investor_uid` in(".implode(",",$friendsUid).") and `status` in (10,12,40,50) order by `add_time`) as a group by `investor_uid`");
                foreach ($investData as $vi){
                    $investInfo[$vi['investor_uid']] = $vi['investor_capital'];
                }
                foreach ($groupList as $vg){

                    $name = '未实名';
                    $firstInvest = '未首投';
                    $goodsName = '无';
                    if($friendsInfo[$vg['friend_uid']]['real_name']){
                        $name = $this->substrCut(privacyDecode($friendsInfo[$vg['friend_uid']]['real_name']));
                    }

                    if( $investInfo[$vg['friend_uid']]){
                        $firstInvest  = $investInfo[$vg['friend_uid']];
                        $speedTemp = 3;
                    }

                    if( $investInfo[$vg['friend_uid']] >= $goods[$vg['goods_id']]['need'] ){
                        $goodsName  =  $goods[$vg['goods_id']]['name'];
                        $speedTemp = 4;
                    }
                    if($speedTemp > $speed){
                        $speed = $speedTemp;
                    }
                    $res[]  = [
                        'phone' =>  substr_replace($friendsInfo[$vg['friend_uid']]['user_phone'], '****', 3, 4),
                        'name'  => $name,
                        'firstInvest' =>  $firstInvest,
                        'goods' => $goodsName
                    ];
                }
            }
            $this->returnData(0, 'ok',['list' =>$res,'speed'=>$speed]);
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }
    public function h190301_rec_pe()
    {
        try {

            $uid = I('userID', 0, 'int');
            $conf = $this->h190301_conf();
            $prize = $conf['prize_e'];
            $time = $_SERVER['REQUEST_TIME'];

            A('Service/Activity')->dailyPrizeStatus($uid, $time, $conf['start_time'], $conf['end_time'], $prize['name']);

            $_POST['available_start_dt'] = date("Y-m-d H:i:s", $time);
            $_POST['available_end_type'] = 0;
            $_POST['valid_days'] = $prize['valid_days'];
            $_POST['admin_uid'] = 0;
            $_POST['uid'] = $uid;
            $_POST['pid2'] = $time;
            $_POST['money'] = number_format($prize['money'], 2, ".", "");
            $_POST['invest_min_money'] = $prize['invest_min_money'];
            $_POST['invest_min_duration'] = $prize['invest_min_duration'];
            $_POST['remark'] = $prize['name'];
            $_POST['is_send_to_phone'] = 0;
            $_POST['token'] = create_sinapay_token();
            $rs = A("Home/Prizeapi")->loteriokuponoj();

            if ($rs['response_code'] != 1) {
                throw new \Exception($rs['response_message']);
            }
            $this->returnData(0, '领取成功');
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }
    private function getSexByIdCarad($idCard)
    {
        $card = privacyDecode($idCard);
        $sex =  substr($card, 16, 1) % 2;
        return  (int)$sex;
    }

    /**阳春三月
     * @return array
     */
    private function h190302_conf()
    {
        return [

            'start_time' => 1552838400,
            'end_time' => 1554047940,
            'prize_a' => [
                'name' => '阳春3月大礼包',
                'data' => [

                    ['invest_min_money' => 10000, 'money' => 10, 'invest_min_duration' => 20],
                    ['invest_min_money' => 30000, 'money' => 30, 'invest_min_duration' => 20],
                    ['invest_min_money' => 50000, 'money' => 50, 'invest_min_duration' => 20],

                    ['invest_min_money' => 15000, 'money' => 40, 'invest_min_duration' => 80],
                    ['invest_min_money' => 40000, 'money' => 100, 'invest_min_duration' => 80],
                    ['invest_min_money' => 60000, 'money' => 150, 'invest_min_duration' => 80],

                    ['invest_min_money' => 10000, 'money' => 40, 'invest_min_duration' => 150],
                    ['invest_min_money' => 20000, 'money' => 80, 'invest_min_duration' => 150],
                    ['invest_min_money' => 40000, 'money' => 200, 'invest_min_duration' => 150],
                    ['invest_min_money' => 60000, 'money' => 300, 'invest_min_duration' => 150]
                ]
            ],
            'prize_b' => [
                //加息券
                'name' => '阳春3月回归礼',
                'data' => [
                    ['invest_min_money' => 1000, 'prize_rate' => 1.88, 'invest_min_duration' => 20, 'prize_max_money' => 10000],
                    ['invest_min_money' => 1000, 'prize_rate' => 2.88, 'invest_min_duration' => 20, 'prize_max_money' => 10000],
                ]
            ],
            'prize_c' => [
                'name' => '3月VIP专享大红包',
                'asset' => 100000,
                'invest_min_money' => 100000,
                'data' => [
                    [ 'money' => 118, 'invest_min_duration' => 20],
                    [ 'money' => 288, 'invest_min_duration' => 80],
                    [ 'money' => 588, 'invest_min_duration' => 150],
                    [ 'money' => 888, 'invest_min_duration' => 300],
                ]
            ],
            'prize_d' => ['name' => '3月新手专享收益券', 'invest_min_money' => 100, 'prize_rate' => 3, 'invest_min_duration' => 20, 'prize_max_money' => 10000],

            'prize_e' => ['name' => '阳春3月活动每日分享红包', 'money' => 10, 'invest_min_money' => 2000, 'invest_min_duration' => 80, 'valid_days' => 3],
        ];

    }
    public function h190302_status()
    {
        try {
            $uid = I('userID', 0, 'int');
            $conf = $this->h190302_conf();
            $now = $_SERVER['REQUEST_TIME'];
            $now > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);

            $hasInvest = 0;
            $noInvestDays = 0;
            $userAsset = 0;
            $initialCapital = 0;
            $currentCapital= 0;
            $regInvestBefore = 0;
            //首次投资≥5000元即可在以下小超周边中任选一样
            $goodsStatus = 0;
            $goodsCondition =0;
            $pa['receive'] = 0;
            $pb['receive'] = 0;
            $pc['receive'] = 0;
            $pd['receive'] = 0;

            if ($uid) {

                $userInfo = M('User')->where("`uid` = {$uid}")->find();
                empty($userInfo) && $this->returnData(1, ActivityService::USE_NOT_EXIST);

                $investData = A('Service/Activity')->userFirstLatestInvest($uid);
                $latestInvest = $investData['latest'];

                if($userInfo['reg_time'] < $conf['start_time']  && !$latestInvest){
                    $regInvestBefore = 1;
                }

                if ($latestInvest) {
                    $hasInvest = 1;
                    $num = ($conf['end_time'] - $latestInvest['add_time']) / 86400;
                    if ($num > 0 && $num < 1) {
                        $num = 1;
                    }
                    $noInvestDays = (int)$num;
                }

                $moneyInfo = getUserMoneyInfo($uid);
                $userAsset = $moneyInfo['total'];

                $initialCapitalData = json_decode(A('Service/Activity')->curlPost(operate_host . "/Activity/190101_initial_capital", ['uid' => $uid]), true);
                if($initialCapitalData['code'] == 0){
                    throw new \Exception($initialCapitalData['message']);
                }
                //初始本金
                $initialCapital = $initialCapitalData['data']['capital'];
                //当前本金
                if($now > $conf['end_time']) {
                    $currentCapitalData = json_decode(A('Service/Activity')->curlPost(operate_host . "/Activity/190101_end_capital", ['uid' => $uid]), true);
                    if($currentCapitalData['code'] == 0){
                        throw new \Exception($currentCapitalData['message']);
                    }
                    $currentCapital = $currentCapitalData['data']['capital'];
                }else{
                    $currentCapital = $moneyInfo['total_capital'] ;
                }

                $checkRecPe = json_decode(A('Service/Activity')->curlPost(operate_host . "/Activity/190201_search_qlz", ['uid' => $uid,'source'=>'ycsy']), true);

                !$checkRecPe['code']  &&  $this->returnData(1, $checkRecPe['message']);
                $checkRecPe['data'] && $goodsStatus = $checkRecPe['data']['goods_id'];

                $firstInvet =  $investData['first'];
                if($firstInvet && $firstInvet['investor_capital'] >= 5000 && $firstInvet['add_time'] >= $conf['start_time'] && $firstInvet['add_time'] <= $conf['end_time'] ){
                    $goodsCondition= 1;
                }


                $activityPrize = A('Service/Activity')->receivedPrize($uid, '', 0, $conf['start_time']);
                if ($activityPrize) {
                    foreach ($activityPrize as $key => $value) {
                        $value['source'] == $conf['prize_a']['name'] && $pa['receive'] = 1;
                        $value['source'] == $conf['prize_b']['name'] && $pb['receive'] = 1;
                        $value['source'] == $conf['prize_c']['name'] && $pc['receive'] = 1;
                        $value['source'] == $conf['prize_d']['name'] && $pd['receive'] = 1;
                    }
                }
            }

            $this->returnData(0, 'ok', [
                'current_capital' =>$currentCapital,
                'initial_capital' =>$initialCapital,
                'no_invest_days' => $noInvestDays,
                'has_invest' => $hasInvest,
                'reg_invest_before' => $regInvestBefore,
                'goods_status' => $goodsStatus,
                'goods_condition' => $goodsCondition,
                'asset' => $userAsset,
                'prize_a' => $pa,
                'prize_b' => $pb,
                'prize_c' => $pc,
                'prize_d' => $pd,
            ]);

        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }

    }
    public function h190302_rec_pa()
    {
        try {
            $uid = I('userID', 0, 'int');
            $conf = $this->h190302_conf();
            $prize = $conf['prize_a'];
            $time = $_SERVER['REQUEST_TIME'];
            $time > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            !$uid && $this->returnData(1, ActivityService::PARAM_ERROR);

            $userInfo = D('Home/User', 'Service')->getUserInfo($uid);
            empty($userInfo) && $this->returnData(1, ActivityService::USE_NOT_EXIST);

            $investData = A('Service/Activity')->userFirstLatestInvest($uid);
            $latestInvest = $investData['latest'];

            !$latestInvest && $this->returnData(1, ActivityService::NO_INVEST);

            $num = ($conf['end_time'] - $latestInvest['add_time']) / 86400;
            $noInvestDays = (int)$num;
            $noInvestDays >= 180 && $this->returnData(1, "180内未投资，暂无法领取哦");

            $checkRec = A('Service/Activity')->receivedPrize($uid, $prize['name'], 0, $conf['start_time']);
            $checkRec && $this->returnData(1, ActivityService::HAS_REC);

            M()->startTrans();
            foreach ($prize['data'] as $key => $value) {

                $_POST['available_start_dt'] = date("Y-m-d H:i:s", $conf['start_time']);
                $_POST['available_end_type'] = 1;
                $_POST['available_end_dt'] = date("Y-m-d H:i:s", $conf['end_time']);
                $_POST['admin_uid'] = 0;
                $_POST['uid'] = $uid;
                $_POST['pid2'] = $time . "_" . $key;
                $_POST['money'] = number_format($value['money'], 2, ".", "");
                $_POST['invest_min_money'] = $value['invest_min_money'];
                $_POST['invest_min_duration'] = $value['invest_min_duration'];
                $_POST['remark'] = $prize['name'];
                $_POST['is_send_to_phone'] = 0;
                $_POST['token'] = create_sinapay_token();
                $rs = A("Home/Prizeapi")->loteriokuponoj();
                if ($rs['response_code'] != 1) {
                    M()->rollback();
                    E($rs['response_message']);
                }
            }
            M()->commit();
            $this->returnData(0, "{$prize['name']}已到账");

        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }
    public function h190302_rec_pb()
    {
        try {

            $uid = I('userID', 0, 'int');
            $conf = $this->h190302_conf();
            $prizeb = $conf['prize_b'];
            $time = $_SERVER['REQUEST_TIME'];
            $time > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            !$uid && $this->returnData(1, ActivityService::PARAM_ERROR);

            $userInfo = M('User')->where("`uid` = {$uid}")->find();
            empty($userInfo) && $this->returnData(1, ActivityService::USE_NOT_EXIST);

            $checkRec = A('Service/Activity')->receivedPrize($uid, $prizeb['name'], 0, $conf['start_time']);
            $checkRec && $this->returnData(1, ActivityService::HAS_REC);

            $noInvestDays = 0;
            $latestInvest = A('Service/Activity')->userFirstLatestInvest($uid);
            $latestInvest = $latestInvest['latest'];

            if ($latestInvest) {
                $noInvestDays = (int)(($conf['end_time'] - $latestInvest['add_time']) / 86400);
            }
            if ($noInvestDays >= 360) {
                $prize = $prizeb['data'][1];
            } elseif ($noInvestDays >= 180) {
                $prize = $prizeb['data'][0];
            } else {
                $this->returnData(1, "不够领取条件");
            }

            $_POST['available_start_dt'] = date("Y-m-d H:i:s", $conf['start_time']);
            $_POST['available_end_type'] = 1;
            $_POST['available_end_dt'] = date("Y-m-d H:i:s", $conf['end_time']);
            $_POST['admin_uid'] = 0;
            $_POST['uid'] = $uid;
            $_POST['pid2'] = $time;
            $_POST['money'] = number_format($prize['prize_rate'], 2, ".", "");
            $_POST['invest_min_money'] = $prize['invest_min_money'];
            $_POST['invest_min_duration'] = $prize['invest_min_duration'];;
            $_POST['remark'] = $prizeb['name'];
            $_POST['prize_max_money'] = $prize['prize_max_money'];
            $_POST['is_send_to_phone'] = 0;
            $_POST['token'] = create_sinapay_token();
            //调用发放加息券接口
            $rs = A("Home/Prizeapi")->loteryCoupon();

            if ($rs['response_code'] != 1) {
                E($rs['response_message']);
            }
            $this->returnData(0, "恭喜您获得{$prize['prize_rate']}%收益券，请至“账户-我的优惠”中查看！");
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }
    public function h190302_rec_pc()
    {
        try {

            $uid = I('userID', 0, 'int');
            $time = $_SERVER['REQUEST_TIME'];
            $conf = $this->h190302_conf();

            $time > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            !$uid && $this->returnData(1, ActivityService::PARAM_ERROR);

            $userInfo = D('Home/User', 'Service')->getUserInfo($uid);
            empty($userInfo) && $this->returnData(1, ActivityService::USE_NOT_EXIST);

            $moneyInfo = getUserMoneyInfo($uid);
            $userAsset = $moneyInfo['total'];

            ($userAsset < $conf['prize_c']['asset']) && $this->returnData(1, "您的总资产金额未满{$conf['prize_c']['asset']}元，暂无法领取哦!");

            $checkRec = A('Service/Activity')->receivedPrize($uid, $conf['prize_c']['name'], 0, $conf['start_time']);
            !empty($checkRec) && $this->returnData(1, ActivityService::HAS_REC);

            M()->startTrans();
            foreach ($conf['prize_c']['data'] as $key => $value) {

                $_POST['available_start_dt'] = date("Y-m-d H:i:s", $conf['start_time']);
                $_POST['available_end_type'] = 1;
                $_POST['available_end_dt'] = date("Y-m-d H:i:s", $conf['end_time']);
                $_POST['admin_uid'] = 0;
                $_POST['uid'] = $uid;
                $_POST['pid2'] = $time . "_" . $key;
                $_POST['money'] = number_format($value['money'], 2, ".", "");
                $_POST['invest_min_money'] = $conf['prize_c']['invest_min_money'];
                $_POST['invest_min_duration'] = $value['invest_min_duration'];
                $_POST['remark'] = $conf['prize_c']['name'];
                $_POST['is_send_to_phone'] = 0;
                $_POST['token'] = create_sinapay_token();
                $rs = A("Home/Prizeapi")->loteriokuponoj();

                if ($rs['response_code'] != 1) {
                    M()->rollback();
                    E($rs['response_message']);
                }
            }
            M()->commit();
            $this->returnData(0, "{$conf['prize_c']['name']}已到账");
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }
    public function h190302_rec_pd()
    {
        try {
            $uid = I('userID', 0, 'int');
            $conf = $this->h190302_conf();
            $prize = $conf['prize_d'];
            $time = $_SERVER['REQUEST_TIME'];

            $time > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            !$uid && $this->returnData(1, ActivityService::PARAM_ERROR);
            $userInfo = D('Home/User', 'Service')->getUserInfo($uid);
            empty($userInfo) && $this->returnData(1, ActivityService::USE_NOT_EXIST);

            $checkRec = A('Service/Activity')->receivedPrize($uid, $prize['name'], 0, $conf['start_time']);
            !empty($checkRec) && $this->returnData(1, ActivityService::HAS_REC);


            $checkInvest = A('Service/Activity')->checkInvest($uid);

            if (($userInfo['reg_time'] > $conf['start_time']) || $checkInvest) {
                $this->returnData(1, "不好意思，您不符合领取条件，暂无法领取哦");
            }

            //加息券
            $_POST['available_start_dt'] = date("Y-m-d H:i:s", $conf['start_time']);
            $_POST['available_end_type'] = 1;
            $_POST['available_end_dt'] = date("Y-m-d H:i:s", $conf['end_time']);
            $_POST['admin_uid'] = 0;
            $_POST['uid'] = $uid;
            $_POST['pid2'] = $time;
            $_POST['money'] = number_format($prize['prize_rate'], 2, ".", "");
            $_POST['invest_min_money'] = $prize['invest_min_money'];
            $_POST['invest_min_duration'] = $prize['invest_min_duration'];;
            $_POST['remark'] = $prize['name'];
            $_POST['prize_max_money'] = $prize['prize_max_money'];
            $_POST['is_send_to_phone'] = 0;
            $_POST['token'] = create_sinapay_token();
            //调用发放加息券接口
            $rs = A("Home/Prizeapi")->loteryCoupon();

            if ($rs['response_code'] != 1) {
                $this->returnData(1, $rs['response_message']);
            }
            $this->returnData(0, '领取成功！请至“账户-我的优惠”中查看！');

        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }
    public function h190302_rec_pe()
    {
        try {

            $uid = I('userID', 0, 'int');
            $conf = $this->h190302_conf();
            $prize = $conf['prize_e'];
            $time = $_SERVER['REQUEST_TIME'];
            A('Service/Activity')->dailyPrizeStatus($uid, $time, $conf['start_time'], $conf['end_time'], $prize['name']);

            $_POST['available_start_dt'] = date("Y-m-d H:i:s", $time);
            $_POST['available_end_type'] = 0;
            $_POST['valid_days'] = $prize['valid_days'];
            $_POST['admin_uid'] = 0;
            $_POST['uid'] = $uid;
            $_POST['pid2'] = $time;
            $_POST['money'] = number_format($prize['money'], 2, ".", "");
            $_POST['invest_min_money'] = $prize['invest_min_money'];
            $_POST['invest_min_duration'] = $prize['invest_min_duration'];
            $_POST['remark'] = $prize['name'];
            $_POST['is_send_to_phone'] = 0;
            $_POST['token'] = create_sinapay_token();
            $rs = A("Home/Prizeapi")->loteriokuponoj();

            if ($rs['response_code'] != 1) {
                throw new \Exception($rs['response_message']);
            }
            $this->returnData(0, '领取成功');
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }
    public function h190302_rec_goods()
    {
        try {
            $uid = I('userID', 0, 'int');
            $gid = I('goodsID', 0, 'int');
            $conf = $this->h190302_conf();
            $now = $_SERVER['REQUEST_TIME'];
            $now > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            (!$uid || !$gid) && $this->returnData(1, '参数错误');
            !in_array($gid,range(1,6)) &&  $this->returnData(1, '物品不存在');
            $checkRec = json_decode(A('Service/Activity')->curlPost(operate_host . "/Activity/190201_search_qlz", ['uid' => $uid,'source'=>'ycsy']), true);
            !$checkRec['code']  &&  $this->returnData(1, $checkRec['message']);
            $checkRec['data'] &&  $this->returnData(1, ActivityService::HAS_REC);

            $investData = A('Service/Activity')->userFirstLatestInvest($uid);
            $firstInvet =  $investData['first'];
            empty($firstInvet) && $this->returnData(1, '您还未完成首次投资≥5000元，暂无法领取哦！');
            ($firstInvet['investor_capital'] < 5000 || $firstInvet['add_time'] < $conf['start_time'] || $firstInvet['add_time'] > $conf['end_time'] ) && $this->returnData(1, '您已是平台已投资用户，暂无法参与本活动！');

            $rec = json_decode(A('Service/Activity')->curlPost(operate_host . "/Activity/190201_add_qlz", ['uid' => $uid,'source'=>'ycsy','gid' => $gid]), true);
            if(!$rec['code']){
                throw new \Exception($rec['message']);
            }
            $this->returnData(0, 'ok');

        }catch (\Exception $e){
            $this->returnData(1, $e->getMessage());
        }
    }
    public function h190302_investor_data()
    {

        //  $invest_bid_money_already = M("InvestorDetail")->where("is_test=0 AND status IN(1,2) AND investor_uid=$user_id")->sum("capital")-$transfer_info['capital_total'];
        // $invest_bid_money = M("BorrowInvestor")->where("investor_uid=$user_id and status IN (12)")->sum("investor_capital");
        $data = M()->query("select investor_uid , sum(investor_capital) as t from tp_borrow_investor where  status  = 12 GROUP  by investor_uid");
        $str = '';
        foreach ($data as $key => $value){
            $str .=  "insert into `initial_capital_a` (`capital`,`user_id`) VALUES ({$value['t']},{$value['investor_uid']});<br>";
        }
        echo $str;die;
    }
    public function h190302_detail_data()
    {
        $data = M()->query("select investor_uid , sum(capital) as t  from tp_investor_detail  where `status` in(1,2) and is_test=0 GROUP by investor_uid");

        $str = '';
        foreach ($data as $key => $value){
            $str .=  "insert into `initial_capital` (`capital`,`user_id`) VALUES ({$value['t']},{$value['investor_uid']});<br>";
        }
        echo $str;die;
    }

    /**
     * 4月小超运动季
     */
    private function h190401_conf()
    {
        return [

            'start_time' => 1552838400,
            'end_time' => 1556639940,
            'source' => 'ydj',
            'redis'=>['addr'=>'h190401addr_','pe'=>'h190401pe_'],
            'prize_a' => [
                'name' => '运动季大礼包',
                'data' => [

                    ['invest_min_money' => 10000, 'money' => 10, 'invest_min_duration' => 20],
                    ['invest_min_money' => 30000, 'money' => 30, 'invest_min_duration' => 20],
                    ['invest_min_money' => 50000, 'money' => 50, 'invest_min_duration' => 20],

                    ['invest_min_money' => 15000, 'money' => 40, 'invest_min_duration' => 80],
                    ['invest_min_money' => 40000, 'money' => 100, 'invest_min_duration' => 80],
                    ['invest_min_money' => 60000, 'money' => 150, 'invest_min_duration' => 80],

                    ['invest_min_money' => 10000, 'money' => 40, 'invest_min_duration' => 150],
                    ['invest_min_money' => 20000, 'money' => 80, 'invest_min_duration' => 150],
                    ['invest_min_money' => 40000, 'money' => 200, 'invest_min_duration' => 150],
                    ['invest_min_money' => 60000, 'money' => 300, 'invest_min_duration' => 150]
                ]
            ],
            'prize_b' => [
                //加息券
                'name' => '4月回归礼',
                'data' => [
                    ['invest_min_money' => 1000, 'prize_rate' => 1.88, 'invest_min_duration' => 20, 'prize_max_money' => 10000],
                    ['invest_min_money' => 1000, 'prize_rate' => 2.88, 'invest_min_duration' => 20, 'prize_max_money' => 10000],
                ]
            ],
            'prize_c' => [
                'name' => '4月VIP专享大红包',
                'asset' => 100000,
                'invest_min_money' => 100000,
                'data' => [
                    [ 'money' => 118, 'invest_min_duration' => 20],
                    [ 'money' => 288, 'invest_min_duration' => 80],
                    [ 'money' => 588, 'invest_min_duration' => 150],
                    [ 'money' => 888, 'invest_min_duration' => 300],
                ]
            ],
            'prize_d' => ['name' => '小超运动季活动每日分享红包', 'money' => 10, 'invest_min_money' => 2000, 'invest_min_duration' => 80, 'valid_days' => 3],
            'prize_e' =>[
                1 => ['name'=> '运动季兑换红包' ,'price'=> 1, 'money' =>10,'list_name'=> '10元无门槛红包' ],
                2 => ['name'=> '运动季兑换红包','price'=> 2, 'money' => 20 ,'list_name'=> '20元无门槛红包'],
                3 => ['name'=> '运动季兑换红包','price'=> 3, 'money' => 30 ,'list_name'=> '30元无门槛红包'],
                4 => ['name'=> '运动季兑换红包','price'=> 4, 'money' => 50,'list_name'=> '50元无门槛红包'],
                5 => ['name'=> '斯得弗臂包','price'=> 3,'list_name'=> '斯得弗臂包'],
                6 => ['name'=> '小超保温杯','price'=> 3,'list_name'=> '小超保温杯'],
                7 => ['name'=> '小超卫衣','price'=> 4,'list_name'=> '小超卫衣'],
                8 => ['name'=> '李宁瑜伽垫','price'=> 4,'list_name'=> '李宁瑜伽垫'],
                9 => ['name'=> '红双喜羽毛球拍','price'=> 5,'list_name'=> '红双喜羽毛球拍'],
                10 => ['name'=> '小米手环3','price'=> 6,'list_name'=> '小米手环3'],
            ]
        ];

    }
    public function h190401_status()
    {
        try {
            $uid = I('userID', 0, 'int');
            $conf = $this->h190401_conf();
            $now = $_SERVER['REQUEST_TIME'];
            $now > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);

            $hasInvest = 0;
            $noInvestDays = 0;
            $userAsset = 0;
            $annualized = 0;
            $myExcList = [];
            $allExcList = [];
            $goodsId = [];
            $recPeCash = [];
            $coin = 0;

            $pa['receive'] = 0;
            $pb['receive'] = 0;
            $pc['receive'] = 0;

            $sport = json_decode(A('Service/Activity')->curlPost(operate_host . "/Activity/190401_sport", ['uid' => $uid, 'source' => $conf['source'], 'limit' => 30]), true);
            if(!$sport['code']){
                $this->returnData(1, $sport['message']);
            }
            if($sport['data']['all_exc']){
                foreach($sport['data']['all_exc'] as $vall){
                    $allExcList[] = ['name' =>'已兑换'.$conf['prize_e'][$vall['goods_id']]['list_name'] ,'phone'=>  substr_replace($vall['phone'], '****', 3, 4)];
                }
            }

            if ($uid) {

                $userInfo = M('User')->where("`uid` = {$uid}")->find();
                empty($userInfo) && $this->returnData(1, ActivityService::USE_NOT_EXIST);

                $sql= "select `id`, `add_time`,`borrow_id`,`investor_capital` from `tp_borrow_investor` where  investor_uid = {$uid}  and `status` IN (10,12,40,50) order by `add_time` desc limit 1";
                $latestInvest = M()->query($sql);
                $latestInvest = $latestInvest[0];

                if ($latestInvest) {
                    $hasInvest = 1;
                    $num = ($conf['end_time'] - $latestInvest['add_time']) / 86400;
                    if ($num > 0 && $num < 1) {
                        $num = 1;
                    }
                    $noInvestDays = (int)$num;
                }

                $moneyInfo = getUserMoneyInfo($uid);
                $userAsset = $moneyInfo['total'];

                $activityPrize = A('Service/Activity')->receivedPrize($uid, '', 0, $conf['start_time']);
                if ($activityPrize) {
                    foreach ($activityPrize as $key => $value) {
                        $value['source'] == $conf['prize_a']['name'] && $pa['receive'] = 1;
                        $value['source'] == $conf['prize_b']['name'] && $pb['receive'] = 1;
                        $value['source'] == $conf['prize_c']['name'] && $pc['receive'] = 1;
                        $value['source'] == '运动季兑换红包' && $recPeCash[]  = $value;
                    }
                }
                $userAnnualized = A('Service/Activity')->userAnnualized($uid, $conf['start_time'], $conf['end_time']);
                $annualized = $userAnnualized['annualized'];
                if($annualized){
                   $coin = (int) ($annualized/1);
                }

                $redis = RedisConnectService::getRedisInstance();
                $addr = $redis->get($conf['redis']['addr'].$uid);
                if($addr){
                    $addArr = explode("_",$addr);
                    $addrInfo = ['consignee' => $addArr[0],'phone' =>$addArr[1],'address' =>$addArr[2]];
                }else{
                    $addrInfo = ['consignee' => privacyDecode($userInfo['real_name']),'phone' => $userInfo['user_phone'],'address' =>str_replace([" ", "　", "\t", "\n", "\r"], '', $userInfo['address'])];
                }
                if($sport['data']['my_exc']){
                    foreach($sport['data']['my_exc'] as $vme){
                        $myExcList[] = ['name' => $conf['prize_e'][$vme['goods_id']]['list_name'] ,'date'=> date("Y/m/d H:i",$vme['add_time'])];
                        $goodsId[] = $vme['goods_id'];
                        $coin -=  $conf['prize_e'][$vme['goods_id']]['price'];
                    }
                }
                if($recPeCash){
                    foreach ($recPeCash as $vp){
                        $prizeGid = explode("_",$vp['pid2']);
                        $prizeGid = $prizeGid[1];
                        $goodsId[] = $prizeGid;
                        $myExcList[] = ['name' => $conf['prize_e'][$prizeGid]['list_name'] ,'date'=> date("Y/m/d H:i",$vp['insert_time'])];
                        $coin -= $conf['prize_e'][$prizeGid]['price'];
                    }
                    $sort = array_column($myExcList,'date');
                    array_multisort($sort,SORT_ASC,$myExcList);
                }

            }
            $this->returnData(0, 'ok', [
                'no_invest_days' => $noInvestDays,
                'has_invest' => $hasInvest,
                'asset' => $userAsset,
                'prize_a' => $pa,
                'prize_b' => $pb,
                'prize_c' => $pc,
                'sports' =>[
                    'coin' => $coin,
                    'annualized' => $annualized,
                    'my_exc' => [
                        'goods_id' => $goodsId,
                        'list' =>$myExcList
                    ],
                    'all_exc' =>$allExcList,
                    'addrInfo' =>$addrInfo
                ]
            ]);

        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }

    }
    public function h190401_rec_pa()
    {
        try {
            $uid = I('userID', 0, 'int');
            $conf = $this->h190401_conf();
            $prize = $conf['prize_a'];
            $time = $_SERVER['REQUEST_TIME'];
            $time > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            !$uid && $this->returnData(1, ActivityService::PARAM_ERROR);

            $userInfo = D('Home/User', 'Service')->getUserInfo($uid);
            empty($userInfo) && $this->returnData(1, ActivityService::USE_NOT_EXIST);

            $investData = A('Service/Activity')->userFirstLatestInvest($uid);
            $latestInvest = $investData['latest'];

            !$latestInvest && $this->returnData(1, ActivityService::NO_INVEST);

            $num = ($conf['end_time'] - $latestInvest['add_time']) / 86400;
            $noInvestDays = (int)$num;
            $noInvestDays >= 180 && $this->returnData(1, "180内未投资，暂无法领取哦");

            $checkRec = A('Service/Activity')->receivedPrize($uid, $prize['name'], 0, $conf['start_time']);
            $checkRec && $this->returnData(1, ActivityService::HAS_REC);

            M()->startTrans();
            foreach ($prize['data'] as $key => $value) {

                $_POST['available_start_dt'] = date("Y-m-d H:i:s", $conf['start_time']);
                $_POST['available_end_type'] = 1;
                $_POST['available_end_dt'] = date("Y-m-d H:i:s", $conf['end_time']);
                $_POST['admin_uid'] = 0;
                $_POST['uid'] = $uid;
                $_POST['pid2'] = $time . "_" . $key;
                $_POST['money'] = number_format($value['money'], 2, ".", "");
                $_POST['invest_min_money'] = $value['invest_min_money'];
                $_POST['invest_min_duration'] = $value['invest_min_duration'];
                $_POST['remark'] = $prize['name'];
                $_POST['is_send_to_phone'] = 0;
                $_POST['token'] = create_sinapay_token();
                $rs = A("Home/Prizeapi")->loteriokuponoj();
                if ($rs['response_code'] != 1) {
                    M()->rollback();
                    E($rs['response_message']);
                }
            }
            M()->commit();
            $this->returnData(0, "{$prize['name']}已到账");

        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }
    public function h190401_rec_pb()
    {
        try {

            $uid = I('userID', 0, 'int');
            $conf = $this->h190401_conf();
            $prizeb = $conf['prize_b'];
            $time = $_SERVER['REQUEST_TIME'];
            $time > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            !$uid && $this->returnData(1, ActivityService::PARAM_ERROR);

            $userInfo = M('User')->where("`uid` = {$uid}")->find();
            empty($userInfo) && $this->returnData(1, ActivityService::USE_NOT_EXIST);

            $checkRec = A('Service/Activity')->receivedPrize($uid, $prizeb['name'], 0, $conf['start_time']);
            $checkRec && $this->returnData(1, ActivityService::HAS_REC);

            $noInvestDays = 0;
            $latestInvest = A('Service/Activity')->userFirstLatestInvest($uid);
            $latestInvest = $latestInvest['latest'];

            if ($latestInvest) {
                $noInvestDays = (int)(($conf['end_time'] - $latestInvest['add_time']) / 86400);
            }
            if ($noInvestDays >= 360) {
                $prize = $prizeb['data'][1];
            } elseif ($noInvestDays >= 180) {
                $prize = $prizeb['data'][0];
            } else {
                $this->returnData(1, "不够领取条件");
            }

            $_POST['available_start_dt'] = date("Y-m-d H:i:s", $conf['start_time']);
            $_POST['available_end_type'] = 1;
            $_POST['available_end_dt'] = date("Y-m-d H:i:s", $conf['end_time']);
            $_POST['admin_uid'] = 0;
            $_POST['uid'] = $uid;
            $_POST['pid2'] = $time;
            $_POST['money'] = number_format($prize['prize_rate'], 2, ".", "");
            $_POST['invest_min_money'] = $prize['invest_min_money'];
            $_POST['invest_min_duration'] = $prize['invest_min_duration'];;
            $_POST['remark'] = $prizeb['name'];
            $_POST['prize_max_money'] = $prize['prize_max_money'];
            $_POST['is_send_to_phone'] = 0;
            $_POST['token'] = create_sinapay_token();
            //调用发放加息券接口
            $rs = A("Home/Prizeapi")->loteryCoupon();

            if ($rs['response_code'] != 1) {
                E($rs['response_message']);
            }
            $this->returnData(0, "恭喜您获得{$prize['prize_rate']}%收益券，请至“账户-我的优惠”中查看！");
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }
    public function h190401_rec_pc()
    {
        try{
            $uid = I('userID', 0, 'int');
            $conf = $this->h190401_conf();
            $time = $_SERVER['REQUEST_TIME'];
            $time > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            !$uid && $this->returnData(1, ActivityService::PARAM_ERROR);
             $prize = $conf['prize_c'];
            $moneyInfo = getUserMoneyInfo($uid);
            $userAsset = $moneyInfo['total'];

            ($userAsset < $prize['asset']) &&  $this->returnData(1, "您的总资产金额未满{$conf['asset']}元，暂无法领取哦！");

            $checkRec = A('Service/Activity')->receivedPrize($uid,$prize['name'],0, $conf['start_time']);
            !empty($checkRec) && $this->returnData(1, ActivityService::HAS_REC);

            M()->startTrans();

            foreach ($prize['data'] as $key => $value) {

                $_POST['available_start_dt'] = date("Y-m-d H:i:s",$conf['start_time']);
                $_POST['available_end_type'] = 1;
                $_POST['available_end_dt'] = date("Y-m-d H:i:s",$conf['end_time']);
                $_POST['admin_uid'] = 0;
                $_POST['uid'] = $uid;
                $_POST['pid2'] = $time."_".$key;
                $_POST['money'] = number_format($value['money'],2,".","");
                $_POST['invest_min_money'] =  $prize['invest_min_money'];
                $_POST['invest_min_duration'] = $value['invest_min_duration'];
                $_POST['remark'] = $prize['name'];
                $_POST['is_send_to_phone'] = 0;
                $_POST['token'] = create_sinapay_token();
                $rs = A("Home/Prizeapi")->loteriokuponoj();

                if($rs['response_code'] != 1){
                    M()->rollback();
                    E($rs['response_message']);
                }
            }
            M()->commit();
            $this->returnData(0, "{$conf['prize_b']['name']}大红包已到账");
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }
    public function h190401_rec_pd()
    {
        try {

            $uid = I('userID', 0, 'int');
            $conf = $this->h190401_conf();
            $prize = $conf['prize_d'];
            $time = $_SERVER['REQUEST_TIME'];
            A('Service/Activity')->dailyPrizeStatus($uid, $time, $conf['start_time'], $conf['end_time'], $prize['name']);

            $_POST['available_start_dt'] = date("Y-m-d H:i:s", $time);
            $_POST['available_end_type'] = 0;
            $_POST['valid_days'] = $prize['valid_days'];
            $_POST['admin_uid'] = 0;
            $_POST['uid'] = $uid;
            $_POST['pid2'] = $time;
            $_POST['money'] = number_format($prize['money'], 2, ".", "");
            $_POST['invest_min_money'] = $prize['invest_min_money'];
            $_POST['invest_min_duration'] = $prize['invest_min_duration'];
            $_POST['remark'] = $prize['name'];
            $_POST['is_send_to_phone'] = 0;
            $_POST['token'] = create_sinapay_token();
            $rs = A("Home/Prizeapi")->loteriokuponoj();

            if ($rs['response_code'] != 1) {
                throw new \Exception($rs['response_message']);
            }
            $this->returnData(0, '领取成功');
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }
    public function h190401_rec_pe()
    {

        $param['uid'] = I('userID', 0, 'int');
        $param['gid'] = I('goodsID', 0, 'int');
        $param['rec_address'] = I('address', '');
        $param['rec_name'] = I('name', '');
        $param['rec_phone'] = I('phone', '');
        $conf = $this->h190401_conf();
        $param['source'] = $conf['source'];
        $time = $_SERVER['REQUEST_TIME'];
        $time > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);

        if(!$param['uid'] || !in_array($param['gid'],range(1,10))){
            $this->returnData(1, ActivityService::PARAM_ERROR);
        }
        $userInfo = D('Home/User', 'Service')->getUserInfo($param['uid']);
        empty($userInfo) && $this->returnData(1, ActivityService::USE_NOT_EXIST);
        $param['phone'] =  $userInfo['user_phone'];
        $prize = $conf['prize_e'][$param['gid']];
        $redis = RedisConnectService::getRedisInstance();
        $redisKey = $conf['redis']['pe'].$param['uid'];
        try{
            $lock = $redis->get($redisKey);
            if($lock){
                $this->returnData(1,'请稍后重试!' );
            }else{
                $redis->set($redisKey, 1 ,"",10);
                $myExc = json_decode(A('Service/Activity')->curlPost(operate_host . "/Activity/190401_myexc", ['uid' => $param['uid'], 'source' => $param['source'] ]), true);
                $myPrize = M()->query("select `pid2`,`money`,`source`,`id` from `tp_prize` where  
                                    `user_id` = '{$param['uid']}' and `source` = '运动季兑换红包' and `money` in(10,20,30,50) and `insert_time` >= '{$conf['start_time']}' ");
                if(!$myExc['code']){
                    throw new \Exception($myExc['message']);
                }
                $recieved = [];
                $usedcoin = 0;
                $coin = 0;
                if($myExc['data']){
                    foreach ($myExc['data'] as $value){
                        $recieved[] = $value['goods_id'];
                        $usedcoin += $conf['prize_e'][$value['goods_id']]['price'];
                    }
                }

                if($myPrize){
                    foreach ($myPrize as $vp){
                        $prizeGid = explode("_",$vp['pid2']);
                        $prizeGid = $prizeGid[1];
                        $recieved[] = $prizeGid;
                        $usedcoin += $conf['prize_e'][$prizeGid]['price'];
                    }
                }

                if(in_array($param['gid'],$recieved)){
                    throw new \Exception(ActivityService::HAS_REC);
                }

                $userAnnualized = A('Service/Activity')->userAnnualized($param['uid'], $conf['start_time'], $conf['end_time']);
                $annualized = $userAnnualized['annualized'];
                if($annualized){
                    $coin = (int) ($annualized/1);
                }
                $currentCoin = $coin - $usedcoin ;
                if( $currentCoin < $prize['price'] ){
                    throw new \Exception("您当前的运动币为{$currentCoin}，暂无法兑换该奖品哦！");
                }

                if($param['gid'] < 5 ){

                    $_POST['available_start_dt'] = date("Y-m-d H:i:s",$conf['start_time']);
                    $_POST['available_end_type'] = 1;
                    $_POST['available_end_dt'] = date("Y-m-d H:i:s",$conf['end_time']);
                    $_POST['admin_uid'] = 0;
                    $_POST['uid'] = $param['uid'];
                    $_POST['pid2'] = $time."_".$param['gid'];
                    $_POST['money'] = number_format($prize['money'],2,".","");
                    $_POST['invest_min_money'] =  100;
                    $_POST['invest_min_duration'] = 20;
                    $_POST['remark'] = $prize['name'];
                    $_POST['is_send_to_phone'] = 0;
                    $_POST['token'] = create_sinapay_token();
                    $rs = A("Home/Prizeapi")->loteriokuponoj();

                    if($rs['response_code'] != 1){
                        E($rs['response_message']);
                    }
                    $this->h190401_add_exc($param);
                    $redis->del($redisKey);
                    $this->returnData(0, "{$prize['name']}已到账");
                }else{

                    $res = $this->h190401_add_exc($param);
                    if($res['code']){
                        $redis->del($redisKey);
                        $this->returnData(0, "兑换成功！奖品将在活动结束后15个工作日内发出，请耐心等待！");
                    }else{
                        throw new \Exception($res['msg']);
                    }

                }
            }

        }catch(\Exception $e) {
            $redis->del($redisKey);
            $this->returnData(1, $e->getMessage());
        }
    }
    private function  h190401_add_exc($param){

        $rec = json_decode(A('Service/Activity')->curlPost(operate_host . "/Activity/190401_exc_add", $param), true);
        if(!$rec['code']){
            return ['code'=> 0 , 'msg' => $rec['message']];
        }
        return ['code' => 1 , 'msg' => 'ok'];
   }
    public function  h190401_add_addr(){
        try {

            $data['uid'] = I('userID', 0, 'int');
            $data['address'] = str_replace([" ", "　", "\t", "\n", "\r"], '',I('address', ''));
            $data['name'] = I('name', '');
            $data['phone'] = I('phone', '');
            if(!$data['uid']  ){
                $this->returnData(1, ActivityService::PARAM_ERROR);
            }
            $conf = $this->h190401_conf();
            $redis = RedisConnectService::getRedisInstance();
            $addr = $redis->get($conf['redis']['addr'].$data['uid']);
            if($addr){
                throw new \Exception('已保存,无法修改');
            }

            if( !$data['address'] || !$data['name'] || !$data['phone'] ){
                $this->returnData(1, "请填写完整信息");
            }

            $res = $redis->set($conf['redis']['addr'].$data['uid'], $data['name'].'_'.$data['phone'].'_'.$data['address'] , '', 3024000);
            if ($res) {
                $this->returnData(0, 'ok');
            } else {
                throw new \Exception('保存失败');
            }
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }
    public function  h190401_fight_group()
    {
        try {

            $uid = I('userID', 0, 'int');
            $friendId= I('friendID', 0, 'int');
            $goodsId= I('goodsID', 0, 'int');
            $conf = $this->h190401_conf();
            $time = $_SERVER['REQUEST_TIME'];
            $time > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            if(!$uid || !$friendId || !$goodsId){
                $this->returnData(1, ActivityService::PARAM_ERROR);
            }
            $friendInfo = M('User')->where("`uid` = {$friendId}")->find();
            //   echo "<pre>";print_r($friendInfo);die;
            empty($friendInfo) && $this->returnData(1, ActivityService::USE_NOT_EXIST);
            $friendInfo['reg_time'] < $conf['start_time'] && $this->returnData(1, '好友不是新注册用户');
            $checkInvest = A('Service/Activity')->checkInvest($friendId);
            $checkInvest&& $this->returnData(1, "好友是已投资用户");

            $res = json_decode(A('Service/Activity')->curlPost(operate_host . "/Activity/190201_fight_group", ['uid' => $uid,'fuid'=>$friendId,'gid'=>$goodsId,'source'=>$conf['source']]), true);
            if(!$res['code']){
                throw new \Exception($res['message']);
            }
            $this->returnData(0, 'ok');
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }
    public function  h190401_group_list()
    {
        try {
            $uid = I('userID', 0, 'int');
            $conf = $this->h190401_conf();
            $time = $_SERVER['REQUEST_TIME'];
            $time > $conf['end_time'] && $this->returnData(1, ActivityService::ACTIVITY_NOT_GOING);
            !$uid && $this->returnData(1, ActivityService::PARAM_ERROR);

            $groupData = json_decode(A('Service/Activity')->curlPost(operate_host . "/Activity/190201_group_list", ['uid' => $uid,'source'=>$conf['source']]), true);

            if(!$groupData['code']){
                throw new \Exception($groupData['message']);
            }
            $res  = [];
            $speed = 1;
            $groupList = $groupData['data'];
            if($groupList){
                $speed =2;
                $friendsInfo = [];
                $investInfo = [];
                $goods = [
                    1 => ['name' => '小超情侣抱枕毯','need' =>3000],
                    2 => ['name' => '小超卫衣','need' =>5000],
                    4 => ['name' => '小超保温杯','need' =>5000],
                    5 => ['name' => '李宁瑜伽垫','need' =>10000],
                    6 => ['name' => '红双喜羽毛球拍','need' =>15000],
                    7 => ['name' => '小米手环3','need' =>20000],
                ];

                foreach ($groupList as $key=>$value){
                    $friendsUid[] = $value['friend_uid'];
                }

                //手机 姓名
                $userData = M()->query("select `user_phone`,`real_name`,`uid` from `tp_user` where `uid` in(".implode(",",$friendsUid).")");
                foreach ($userData as $vu){
                    $friendsInfo[$vu['uid']] = $vu;
                }

                //首投金额
                $investData = M()->query("select * from (select `investor_capital`,`investor_uid` from `tp_borrow_investor` where `investor_uid` in(".implode(",",$friendsUid).") and `status` in (10,12,40,50) order by `add_time`) as a group by `investor_uid`");
                foreach ($investData as $vi){
                    $investInfo[$vi['investor_uid']] = $vi['investor_capital'];
                }
                foreach ($groupList as $vg){

                    $name = '未实名';
                    $firstInvest = '未首投';
                    $goodsName = '无';
                    if($friendsInfo[$vg['friend_uid']]['real_name']){
                        $name = $this->substrCut(privacyDecode($friendsInfo[$vg['friend_uid']]['real_name']));
                    }

                    if( $investInfo[$vg['friend_uid']]){
                        $firstInvest  = $investInfo[$vg['friend_uid']];
                        $speedTemp = 3;
                    }

                    if( $investInfo[$vg['friend_uid']] >= $goods[$vg['goods_id']]['need'] ){
                        $goodsName  =  $goods[$vg['goods_id']]['name'];
                        $speedTemp = 4;
                    }
                    if($speedTemp > $speed){
                        $speed = $speedTemp;
                    }
                    $res[]  = [
                        'phone' =>  substr_replace($friendsInfo[$vg['friend_uid']]['user_phone'], '****', 3, 4),
                        'name'  => $name,
                        'firstInvest' =>  $firstInvest,
                        'goods' => $goodsName
                    ];
                }
            }
            $this->returnData(0, 'ok',['list' =>$res,'speed'=>$speed]);
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }

    public function test(){
        try {

            $data['uid'] = I('userID', 0, 'intval');
            $conf = $this->h190401_conf();
            $redis = RedisConnectService::getRedisInstance();
            $redis->del($conf['redis']['addr'].$data['uid']);
        } catch (\Exception $e) {
            $this->returnData(1, $e->getMessage());
        }
    }
}
    