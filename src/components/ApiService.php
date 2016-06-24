<?php
namespace GdsApiClient\components;

abstract class ApiService
{
    protected $_client;

    public function setClient($client)
    {
        $this->_client = $client;
    }

    protected function _callApi($client, $httpMethod, $methodName, $params = array()) {
        $responseJson = $client->makeRequest($httpMethod, $methodName, $params);
        $response = $this->_client->makeResponse($responseJson);
        return $response[$methodName . '_result']['Data'];
    }
}