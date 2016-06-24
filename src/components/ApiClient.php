<?php
namespace GdsApiClient\components;

use GdsApiClient\components\DictService as DictService;
use GdsApiClient\components\CruiseService as CruiseService;

class ApiClient
{
    protected $_token;

    protected $_version = 'v1';

    protected $_baseUrl = 'http://91.240.86.250:8000/api/';

    const HTTP_STATUS_OK = 200;

    public function __construct($token)
    {
        $this->_token = $token;
    }

    public function setVersion($version)
    {
        $this->_version = $version;
    }

    protected function getUrl()
    {
        return $this->_baseUrl . $this->_version . '/';
    }

    public function getApiService($serviceName)
    {
        $className = 'GdsApiClient\\components\\'.mb_convert_case($serviceName, MB_CASE_TITLE) . 'Service';
        $service = new $className();
        $service->setClient($this);
        return $service;
    }

    public function makeRequest($httpMethod, $apiResource, $paramRequest = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Access-Token: " . $this->_token));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        $token_param = array('token' => $this->_token);
        $query = '';
        $params = $token_param;

        if(isset($paramRequest) && !empty($paramRequest)) {
            $params = array_merge($params, $paramRequest);
        }
        $query = http_build_query($params);

        $url = $this->getUrl() . $apiResource;

        switch($httpMethod) {
            case 'get':
                if(!empty($query)) $url .= '?' . $query;
                break;

            case 'post':
                $url .= '?' . http_build_query($token_param);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
                break;
        }

        curl_setopt($ch, CURLOPT_URL, $url);

        $response = curl_exec($ch);

        $infoCurlHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($infoCurlHttpCode !== self::HTTP_STATUS_OK)
        {
            $jsonDecodeData = json_decode($response, true);
            throw new \Exception($infoCurlHttpCode.":".(isset($jsonDecodeData['result']['ErrorMessage'])?$jsonDecodeData['result']['ErrorMessage']:'HTTP Error'), $infoCurlHttpCode);
        }

        return $response;
    }

    public function makeResponse($jsonString)
    {
        $jsonDecodeData = json_decode($jsonString, true);
        if (!$jsonDecodeData)
        {
            throw new \Exception("Unable to decode json response: $jsonString");
        } else
        {
            if(isset($jsonDecodeData['result']['ErrorCode']) && $jsonDecodeData['result']['ErrorCode'] > 0) throw new \Exception("{$jsonDecodeData['result']['ErrorCode']}:".(isset($jsonDecodeData['result']['ErrorMessage'])?$jsonDecodeData['result']['ErrorMessage']:''), $jsonDecodeData['result']['ErrorCode']);
        }
        return $jsonDecodeData;
    }
}