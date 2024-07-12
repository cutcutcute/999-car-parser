<?php
    require 'vendor/autoload.php';
    class Models{
    

    private const PEM_PATH = 'cacert.pem';
    private $html;

    public function __construct(){
        $client = new GuzzleHttp\Client(["base_uri"=>BASE_URL, "verify"=>Models::PEM_PATH]);
        $response = $client->request("GET", BASE_URL, [
            'query' => ['aof' => 1, 'eo'=>'249']
        ]);
        $this->html = (string) $response -> getBody();
    }


    public function parse_dom(){
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML(mb_convert_encoding($this->html, 'HTML-ENTITIES', 'UTF-8'));
        $doc->encoding = 'UTF-8'; // Установка кодировки UTF-8
        $xpath = new DOMXPath($doc);
    
        $this->get_all_marks($xpath);
    }


    private function get_all_marks($xpath){
        $dictionary = array();
    
        $makes = $xpath->evaluate('//*[@id="js-ads-filters"]/div[2]/div/ul/li/div/label');
        
        $extractedTitles = [];
        foreach ($makes as $make) {
            $inputValue = $xpath->evaluate('string(./input/@value)', $make);
            $extractedTitles[] = $make->textContent.PHP_EOL;
          
            $dictionary[trim(str_replace(">","",$make->textContent))] = $inputValue;
        }
    
        print_r($dictionary);
    }
}