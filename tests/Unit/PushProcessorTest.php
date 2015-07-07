<?php

namespace Dmitrovskiy\IonicPush\Tests\Unit;

use Dmitrovskiy\IonicPush\PushProcessor;
use Dmitrovskiy\IonicPush\Tests\TestCase;

class PushProcessorTest extends TestCase
{

    public function testCreation()
    {
        $instance = new PushProcessor('appId', 'appSecret');
        $this->assertNotNull($instance);
    }

    public function testGetAppId()
    {
        $appId = 'appId';
        $instance = new PushProcessor($appId, 'appSecret');
        $this->assertEquals($instance->getAppId(), $appId);
    }

    public function testGetAppApiSecret()
    {
        $appApiSecret = 'appSecret';
        $instance = new PushProcessor('appId', $appApiSecret);
        $this->assertEquals($instance->getAppApiSecret(), $appApiSecret);
    }

    public function testGetPushEndPoint()
    {
        $pushEndPoint = 'testEndPoint';
        $instance = new PushProcessor('appId', 'appSecret', $pushEndPoint);
        $this->assertEquals($instance->getPushEndPoint(), $pushEndPoint);
    }

    public function testSetPushEndPoint()
    {
        $newEndPoint = 'newEndPoint';
        $instance = new PushProcessor('appId', 'appSecret');
        $instance->setPushEndPoint($newEndPoint);

        $this->assertEquals($instance->getPushEndPoint(), $newEndPoint);
    }

    /**
     * @expectedException \Dmitrovskiy\IonicPush\Exception\PermissionDeniedException
     */
    public function testNotifyPermissionDenied()
    {
        $instance = new PushProcessor('appID', 'appEndPoint');
        $instance->notify(array(), array());
    }

    /**
     * @expectedException \Dmitrovskiy\IonicPush\Exception\RequestException
     */
    public function testNotifyFailedRequest()
    {
        $instance = new PushProcessor(
            'appID', 'appEndPoint', 'wrong http address'
        );
        $instance->notify(array(), array());
    }
}
