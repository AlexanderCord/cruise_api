<?php
namespace GdsApiClient\components;

class CruiseService extends ApiService
{
    public function getCruiseList($params)
    {
        $httpMethod = 'get';
        $methodName = 'get_cruise_list';
        $responseData = $this->_callApi($this->_client, $httpMethod, $methodName, $params);
        return $responseData;
    }

    public function getPriceList($params)
    {
        $httpMethod = 'get';
        $methodName = 'get_price_list';
        $responseData = $this->_callApi($this->_client, $httpMethod, $methodName, $params);
        return $responseData;
    }
}