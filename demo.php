<?php

foreach (glob("src/components/*.php") as $filename) {
    include_once($filename);
}
use GdsApiClient\components\ApiClient as ApiClient;
use GdsApiClient\components\ApiService as ApiService;
use GdsApiClient\components\ParseData as ParseData;

try {
    $token = 'tutorial_token';
    $clientApi = new ApiClient($token);
    $parser = new ParseData();

    $dictService = $clientApi->getApiService('dict');
    $dictServiceDictListData = $dictService->getDictList();
    $dictServiceDictList = $parser->setData($dictServiceDictListData)->parseDictList();
    echo $dictServiceDictList;

    $dictServiceDictData = $dictService->getDict(array('name' => 'city'));
    $dictServiceDict = $parser->setData($dictServiceDictData)->parseDict();
    echo $dictServiceDict;


    $dictCruise = $clientApi->getApiService('cruise');
    $dictCruiseListData = $dictCruise->getCruiseList(array(
        'region' => array(16, 17, 18, 22, 28),
        'company' => array(17, 18, 19),
        'departure_port' => array(1624, 1614, 57),
        'port' => 1631,
        'ship' => array(54, 192),
        'price_to'=>80000,
        'days_from'=>3,
        'days_to'=>10,
        'date_start_from'=>'2016-09-04',
        'date_start_to'=>'2018-01-01',
        'response_limit'=>10,
        'offset'=>0
    ));
    $dictCruiseList = $parser->setData($dictCruiseListData)->parseCruiseList();
    echo $dictCruiseList;

    $dictPriceListData = $dictCruise->getPriceList(array('cruise_id' => '94248'));
    $dictPriceList = $parser->setData($dictPriceListData)->parsePriceList();
    echo $dictPriceList;

} catch (Exception $e) {
    $errorMessage = $e->getMessage();
    print_r($errorMessage);
}
?>