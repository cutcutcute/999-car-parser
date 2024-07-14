<?php

include "request_args.php";
require 'vendor/autoload.php';

class Parser{

    private const PEM_PATH = CACERT_PEM_PATH;

    private  $global_prices = array();

    private $logger;

    public function __construct($logger){
        $this->logger=$logger;

    }

    public function global_parse($query){
        $page = 1;
        $client = new GuzzleHttp\Client(["base_uri"=>$query, "verify"=>Parser::PEM_PATH]);
        while (true){
            $response = $this->parse_page($query, $page, $client);
            if ($response === null) break;
            $this->logger->debug("Обработана страница - ".$page.PHP_EOL);
            $page++;
        }
        $statistic = $this->calculateArrayStatistics($this->global_prices);
        $this->logger->info("Полученная статистика: ".implode(', ', $statistic));
        return $statistic;
        
    }


    private function calculateArrayStatistics($numbers) {
        $result = array();
      
        try{
            // Максимальное значение в массиве
            $result['max'] = max($numbers);
            // Минимальное значение в массиве
            $result['min'] = min($numbers);
            // Среднее значение в массиве
            $result['avg'] = array_sum($numbers) / count($numbers);

        } catch (ValueError $e){
                // Максимальное значение в массиве
            $result['max'] = 0;
            // Минимальное значение в массиве
            $result['min'] = 0;
            // Среднее значение в массиве
            $result['avg'] = 0;

            $this->logger->error("По вашему запросу ничего не найдено");
        }
        
      
        return $result;
    }






    public function parse_page($request_string, $page, $client){
        $response = $client->request("GET", $request_string."&page=".$page,);
        $html = (string) $response -> getBody();

        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $doc->encoding = 'UTF-8'; // Установка кодировки UTF-8
        $xpath = new DOMXPath($doc);
        
        $item_dict = array();
        $res = $xpath -> evaluate('//ul[contains(@class, \'ads-list-photo\')]/li');
        
        if ($res -> length == 0){
            $this->logger->debug("На странице закончились данные");
            return null;
        }

        foreach ($res as $item){
            $priceVal = $xpath->evaluate('string(./div[@class="ads-list-photo-item-price"]/span)', $item);
            $extracted_price = $this->extract_price_from_string($priceVal);

            if ($extracted_price != 0)
                $this->global_prices[] = $this->extract_price_from_string($priceVal);
            
        }

        return "ok";
        
        

        
    }


    
    private function extract_price_from_string($string) {
        preg_match_all('/\d+/', $string, $matches);
        $numbers = implode('', $matches[0]);
        return intval($numbers);
    }

}



