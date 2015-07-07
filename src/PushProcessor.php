<?php

namespace Dmitrovskiy\IonicPush;

use Dmitrovskiy\IonicPush\Exception\BadRequestException;
use Dmitrovskiy\IonicPush\Exception\PermissionDeniedException;
use Dmitrovskiy\IonicPush\Exception\RequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;

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

    /** @var string */
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

    public function getPushEndPoint()
    {
        return $this->ionicPushEndPoint;
    }

    public function setPushEndPoint($ionicPushEndpoint)
    {
        $this->ionicPushEndPoint = $ionicPushEndpoint;
    }

    /**
     * @param array $devices
     * @param array $notification
     *
     * @return mixed
     */
    public function notify(array $devices, array $notification)
    {
        $headers = $this->getNotificationHeaders();
        $body = $this->getNotificationBody($devices, $notification);

        return $this->sendRequest($headers, $body);
    }

    protected function getNotificationHeaders()
    {
        $encodedApiSecret = $this->getEncodedApiSecret();
        $authorization = sprintf("Basic %s", $encodedApiSecret);

        return array(
            'Authorization'          => $authorization,
            'Content-Type'           => 'application/json',
            'X-Ionic-Application-Id' => $this->appId
        );
    }

    /**
     * @param array $devices
     * @param array $notification
     *
     * @return string
     */
    protected function getNotificationBody(array $devices, array $notification)
    {
        $body = array(
            'tokens'       => $devices,
            'notification' => $notification
        );

        return json_encode($body);
    }

    /**
     * @param $headers
     * @param $body
     *
     * @return mixed|null
     * @throws BadRequestException
     * @throws PermissionDeniedException
     * @throws RequestException
     */
    protected function sendRequest($headers, $body)
    {
        $request = new Request(
            'POST',
            $this->ionicPushEndPoint,
            $headers,
            $body
        );
        $client = new Client();

        try {
            $response = $client->send($request);
            return $response;
        } catch (ClientException $e) {
            switch ($e->getCode()) {
                case 403: {
                    throw new PermissionDeniedException(
                        "Permission denied sending push", 403, $e
                    );
                }
                case 400: {
                    throw new BadRequestException(
                        "Bad request sending push", 400, $e
                    );
                }
            }
        } catch (\Exception $e) {
            throw new RequestException(
                "An error occurred when sending push request with message: {$e->getMessage()}",
                $e->getCode(),
                $e
            );
        }

        return null;
    }

    protected function getEncodedApiSecret()
    {
        return base64_encode($this->appApiSecret . ':');
    }
}
