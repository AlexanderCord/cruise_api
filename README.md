# cruise_api
Cruise search API


[Документация по использованию (тестовая среда)](https://docs.google.com/document/d/1GvT1r8IagWQJz-sbUOFpQily0beMa40o6CXfVHOYqT8/edit?usp=drive_web)

Пример использования на PHP (см. файл demo.php):
```php
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
```


Пример использования на .NET C# (см. файл demo.cs или [на dotnet fiddle](https://dotnetfiddle.net/hG1AdM)):

```csharp
using System;
using System.Web;
using Newtonsoft.Json;
using Newtonsoft.Json.Linq;
using System.IO;
using System.Collections.Generic;


class ApiClient {

    private string token;
    private string version;
    private string baseUrl;
    private bool debug;
    
    public ApiClient(string myToken, bool debugFlag = false) {
        version = "v1";
        baseUrl = "http://91.240.86.250:8000/";
        token = myToken;
        debug = debugFlag;
        
    }
    
    public ApiResult call(string method, Dictionary<string, dynamic> paramList) {
        try { 
            var builder = new UriBuilder(this.baseUrl);
            builder.Path = "/api/" + this.version + "/" + method;
            var query = HttpUtility.ParseQueryString(builder.Query);
            
            query["token"] = this.token;
            foreach(KeyValuePair<string, dynamic> pair in paramList) {
                if(pair.Value.GetType() == typeof(System.Collections.Generic.List<string>)) {
                    int multiValIndex = 0;
                    for(multiValIndex = 0; multiValIndex < pair.Value.Count; multiValIndex ++) {
                        query.Add( pair.Key + "[]", pair.Value[multiValIndex]);
                    } 
                } else {
                    query[ pair.Key ] = pair.Value;
                }
            }
            
            builder.Query = query.ToString();
            string url = builder.ToString();
            
            if(debug) {
                Console.WriteLine(url);
            } 
            
            var jsonString = @"{}";
            
            
            using (var webClient = new System.Net.WebClient()) {
                webClient.Encoding = System.Text.Encoding.UTF8;
                jsonString = webClient.DownloadString(url);
            }   
            
            if(debug) {
                var debugJsonString = jsonString;
                Console.WriteLine(debugJsonString);
                JsonTextReader reader = new JsonTextReader(new StringReader(debugJsonString));
                while (reader.Read())
                {
                    if (reader.Value != null)
                    {
                        Console.WriteLine("Token: {0}, Value: {1}", reader.TokenType, reader.Value);
                    }
                    else
                    {
                       Console.WriteLine("Token: {0}", reader.TokenType);
                    }
                }
            }

            ApiResult result = new ApiResult( jsonString, method );
            return result;
                                 
        } catch (Exception e) {
             
            ApiResult errorResult = new ApiResult("", method);
            errorResult.ErrorMessage = e.ToString();
            return errorResult;
        }
    }
}


public class ApiResult {
    
    public string ErrorMessage { 
        get; set;
    }
    public int ErrorCode { 
        get; set;
    }
    public dynamic Data { 
        get; set;
    }
    
    public ApiResult(string jsonString, string method) {
        //Console.WriteLine(jsonString); 
        string arrayKey = method + "_result";

        if(jsonString.Length > 0) {
            JObject result = JObject.Parse(jsonString);
            Console.WriteLine(result[arrayKey]["ErrorMessage"]);
            this.ErrorCode = 0;
            this.ErrorMessage = "";
            this.Data = result[arrayKey]["Data"];
        } else {
            this.ErrorMessage = "Empty result returned from API";
        }
        
    }
    

}



public class Program
{
    public static void Main()
    {  
        
        string myToken = "tutorial_token";
        
            
        ApiClient client = new ApiClient( myToken , false /* set to "true" to turn on the debug outout */);
    
    
        
        // Get cruise list
        Dictionary<string, dynamic> paramList = new Dictionary<string, dynamic>()
        { 
                {"region" , new List<string> {"16","17","18","22","28"} }, 
                {"company" , new List<string> {"17","18","19"}},
                {"departure_port" , new List<string> {"1624","1614","57"}},
                {"port" , "1631"},
                {"ship" , new List<string> {"54","192"}},
                {"price_to","80000"},
                {"days_from","3"},
                {"days_to","10"},
                {"date_start_from","2016-09-04"},
                {"date_start_to","2018-01-01"},
                {"response_limit","10"},
                {"offset","0"}


        };
        ApiResult result = client.call( "get_cruise_list", paramList );
        
        if(result.ErrorMessage.Length > 0) {
            Console.WriteLine("Error occured: " + result.ErrorMessage);
            
        } else {
            
            Console.WriteLine("Cruise list:");
            var data = result.Data;
            
            JArray items = (JArray)data; 
    
            
            string ports;
            int cruise_id;
            int ship_cabin_type_id;
            string date_start;
            double price_rub;
            
            for(var i=0; i<items.Count; i++ ) {
                
                ports = (string) items[i]["ports"];
                
                cruise_id = (int) items[i]["cruise_id"];
                
                Console.WriteLine(ports+", cruise_id=" + cruise_id);
                
                JArray prices = (JArray) items[i]["prices"];
                
                for(var j=0; j<prices.Count; j++ ){
                    date_start = (string)prices[j]["date_start"];
                    ship_cabin_type_id = (int)prices[j]["ship_cabin_type_id"];
                    price_rub = (double)prices[j]["price_rub"];
                    
                    Console.WriteLine(date_start +", ship_cabin_type_id=" + ship_cabin_type_id + ", price in rub=" + price_rub);
                
                }
            }
        }   
        
        
        // Get dict list
        paramList = new Dictionary<string, dynamic>()
        { 
                
        };
        result = client.call( "get_dict_list", paramList );
        
        if(result.ErrorMessage.Length > 0) {
            Console.WriteLine("Error occured: " + result.ErrorMessage);
            
        } else {
            Console.WriteLine("Available dictionary list:");
            var data = result.Data;
            
            JArray items = (JArray)data; 
    
            string dict_name;
            
            for(var i=0; i<items.Count; i++ ) {
                dict_name = (string) items[i];
                Console.WriteLine(dict_name);
            }
        }   
        
        
        
        
        
        // Get dict_port
        paramList = new Dictionary<string, dynamic>()
        { 
                {"name", "port"},
        };
        result = client.call( "get_dict", paramList );
        
        if(result.ErrorMessage.Length > 0) {
            Console.WriteLine("Error occured: " + result.ErrorMessage);
            
        } else {
            
            Console.WriteLine("Dict port:");
            var data = result.Data;
            
            JArray items = (JArray)data; 
    
            
            string port_name;
            int port_id;
            int country_id;
            
            for(var i=0; i<items.Count; i++ ) {
                port_name = (string) items[i]["name"];
                port_id = (int) items[i]["port_id"];
                country_id = (int) items[i]["country_id"];
                
                Console.WriteLine(port_name+", id=" + port_id + ", country=" + country_id);
            }
        }   
        
    }
        
}

```
