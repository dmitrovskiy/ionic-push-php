<?php

namespace Dmitrovskiy\IonicPush\Tests\Unit;

use Dmitrovskiy\IonicPush\PushProcessor;
use Dmitrovskiy\IonicPush\Tests\TestCase;
use Psr\Http\Message\ResponseInterface;

class PushProcessorTest extends TestCase
{

    public function testCreation()
    {
        $instance = new PushProcessor('profile', 'token');
        $this->assertNotNull($instance);
    }

    public function testGetProfile()
    {
        $profile = 'profile';
        $instance = new PushProcessor($profile, 'token');
        $this->assertEquals($instance->getProfile(), $profile);
    }

    public function testGetAppApiSecret()
    {
        $token = 'token';
        $instance = new PushProcessor('profile', $token);
        $this->assertEquals($instance->getToken(), $token);
    }

    public function testGetPushEndPoint()
    {
        $pushEndPoint = 'testEndPoint';
        $instance = new PushProcessor('profile', 'token', $pushEndPoint);
        $this->assertEquals($instance->getPushEndPoint(), $pushEndPoint);
    }

    public function testSetPushEndPoint()
    {
        $newEndPoint = 'newEndPoint';
        $instance = new PushProcessor('profile', 'token');
        $instance->setPushEndPoint($newEndPoint);

        $this->assertEquals($instance->getPushEndPoint(), $newEndPoint);
    }

    /**
     * @expectedException \Dmitrovskiy\IonicPush\Exception\PermissionDeniedException
     */
    public function testNotifyPermissionDenied()
    {
        $instance = new PushProcessor('profile', 'token');
        $instance->notify(array(), array());
    }

    /**
     * @expectedException \Dmitrovskiy\IonicPush\Exception\RequestException
     */
    public function testNotifyFailedRequest()
    {
        $instance = new PushProcessor(
            'profile', 'token', 'wrong http address'
        );
        $instance->notify(array(), array());
    }

    public function testNotifySuccess(){
        $credentials = require __DIR__. '/../credentials.php';
        $instance = new PushProcessor(
            $credentials['profile'], $credentials['token']
        );
        /** @var ResponseInterface $response */
        $response = $instance->notify($credentials['device_tokens'],[
            'message' => 'Hello World!!'
        ]);
        $this->assertEquals(201,$response->getStatusCode());
    }
}
