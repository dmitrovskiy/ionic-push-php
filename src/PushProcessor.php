<?php

namespace Dmitrovskiy\IonicPush;

/**
 * Class PushProcessor
 *
 * @package Dmitrovskiy\IonicPush
 */
class PushProcessor
{
    /** @var string */
    protected $appId;

    /** @var string */
    protected $appApiSecret;

    protected $ionicPushEndPoint;

    /**
     * @param string $appId
     * @param string $appApiSecret
     * @param string $ionicPushEndPoint
     */
    public function __construct(
        $appId,
        $appApiSecret,
        $ionicPushEndPoint = 'https://push.ionic.io/api/v1/push'
    ) {
        $this->appId = $appId;
        $this->appApiSecret = $appApiSecret;
        $this->ionicPushEndPoint = $ionicPushEndPoint;
    }

    public function getAppId()
    {
        return $this->appId;
    }

    public function getAppApiSecret()
    {
        return $this->appApiSecret;
    }
}
