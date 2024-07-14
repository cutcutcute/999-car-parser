<?php

    include "request_args.php";
    include "logger_settings.php";
    require 'vendor/autoload.php';

    
    class Models{
    
    private $logger;
    private $global_dictionary = array();
    private const PEM_PATH = CACERT_PEM_PATH;
    private $html;

    public function __construct($logger){
        $client = new GuzzleHttp\Client(["base_uri"=>BASE_URL, "verify"=>Models::PEM_PATH]);
        $response = $client->request("GET", BASE_URL, [
            'query' => ['aof' => 1, 'eo'=>'249']
        ]);
        $this->html = (string) $response -> getBody();
        $this->logger = $logger;
    }


    public function parse_dom(): array{
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML(mb_convert_encoding($this->html, 'HTML-ENTITIES', 'UTF-8'));
        $doc->encoding = 'UTF-8'; // Установка кодировки UTF-8
        $xpath = new DOMXPath($doc);
    
        $all_marks = $this->get_all_marks($xpath);

        return $all_marks;
    }


    private function get_all_marks($xpath): array{
        // Возвращает массив со всеми марками
        $dictionary = array();
    
        $makes = $xpath->evaluate('//*[@id="js-ads-filters"]/div[2]/div/ul/li/div/label');
        
        $all_models_id_array = array();
        $client = new GuzzleHttp\Client(["base_uri"=>BASE_URL, "verify"=>Models::PEM_PATH]);
        $iter_count = count($makes);
        $extractedTitles = [];
        foreach ($makes as $make) {
            
            
            $inputValue = $xpath->evaluate('string(./input/@value)', $make);
            $extractedTitles[] = $make->textContent.PHP_EOL;
            
            $mark_name = mb_strtolower(trim(str_replace(">","",$make->textContent)), "UTF-8");
            $mark_id = $inputValue;
            $models = array();

            
            $this->global_dictionary[] = ["name"=>$mark_name, "id"=>$mark_id, "models"=>$this->request_for_models($mark_id, $client)];
            
            
            $this->logger->info("Данные записываются. Осталось записать: ".($iter_count--));
        }
        
        print_r($this->global_dictionary);
        // Преобразуем словарь в JSON
        $json_data = json_encode($this->global_dictionary);

        // Сохраняем JSON в файл
        file_put_contents('data.json', $json_data);
       
        return $dictionary;
    }

    private function request_for_models($mark_id, $client){
        
        $response = $client->request("GET", BASE_URL, [
            'query' => ['aof' => 1, 'eo'=>$mark_id]
        ]);

        $all_models_open_html = (string) $response -> getBody();
        
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML(mb_convert_encoding($all_models_open_html, 'HTML-ENTITIES', 'UTF-8'));
        $doc->encoding = 'UTF-8'; // Установка кодировки UTF-8
        $s_xpath = new DOMXPath($doc);

        $makes = $s_xpath->evaluate('//*[@id="js-ads-filters"]/div[2]/div/ul/li/ul/li/div/label');
        
        $models = array();
        foreach ($makes as $make) {
            
            
            $inputValue = $s_xpath->evaluate('string(./input/@value)', $make);
            $extractedTitles[] = $make->textContent.PHP_EOL;
            
            $model_name = mb_strtolower(trim(str_replace(">","",$make->textContent)), "UTF-8");
            $model_id = $inputValue;

            $models[]=["model_name"=>$model_name, "model_id"=>$model_id];
        }

        
        return $models;
    }
}


$models = new Models($logger);
$models ->parse_dom();