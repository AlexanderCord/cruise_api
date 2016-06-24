<?php
namespace GdsApiClient\components;

class DictService extends ApiService
{
    public function getDictList()
    {
        $httpMethod = 'get';
        $methodName = 'get_dict_list';
        $responseData = $this->_callApi($this->_client, $httpMethod, $methodName);
        return $responseData;
    }

    public function getDict($params)
    {
        $httpMethod = 'get';
        $methodName = 'get_dict';
        $responseData = $this->_callApi($this->_client, $httpMethod, $methodName, $params);
        return $responseData;
    }
}