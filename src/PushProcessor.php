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
    protected $profile;

    /** @var string */
    protected $token;

    /** @var string */
    protected $ionicPushEndPoint;

    /**
     * @param string $profile
     * @param string $token
     * @param string $ionicPushEndPoint
     */
    public function __construct(
        $profile,
        $token,
        $ionicPushEndPoint = 'https://api.ionic.io/push/notifications'
    ) {
        $this->profile = $profile;
        $this->token = $token;
        $this->ionicPushEndPoint = $ionicPushEndPoint;
    }

    public function getProfile()
    {
        return $this->profile;
    }

    public function getToken()
    {
        return $this->token;
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
        $authorization = sprintf("Bearer %s", $this->getToken());

        return array(
            'Authorization'          => $authorization,
            'Content-Type'           => 'application/json'
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
            'profile' => $this->getProfile(),
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
                case 401:
                case 403: {
                    throw new PermissionDeniedException(
                        "Permission denied sending push", $e->getCode(), $e
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
}
