
<?php
include "models.php";
class RequestGenerator{

    private $logger;
    
    private $array_data = array();

    public function __construct($logger){
        // Загрузка данных из файла JSON
        $json_data = file_get_contents('data.json');

        // Преобразование JSON в массив
        $this->array_data = json_decode($json_data, true);

        $this->logger = $logger;

        $logger->debug("Данные загружены");
    }
  

    public function get_marks_id($names_array): array{
        $names_array = $this -> lower_case_array($names_array);
        
        $marks_array = array();
        foreach($this->array_data as $key => $marks){
            
            if (in_array($marks["name"], $names_array)){
                $marks_array[] = $marks["id"];
            }
        }

        return $marks_array;
    }

    public function get_models_id($models_array, &$marks_id){
        $query_params_array = array();
        foreach ($this -> array_data as $key => $marks){
            $models_data = array();
            foreach ($marks["models"] as $model)
                {

                    
                    if (in_array($model["model_name"], $models_array)){
                        $this->logger->info("Нашелся ".$model["model_name"]." id = ".$marks["id"]);
                        if (in_array($marks["id"], $marks_id)){
                            $key = array_search($marks["id"], $marks_id);
                            if ($key !== false) { 
                                unset($marks_id[$key]); 
                            }
                        }
                        
                        $models_data[]=$model["model_id"];
                        $query_params_array[MODELS_STARTSWITH.$marks["id"]] = ["models"=>$models_data];
                    }
                }

                
            }

            return $query_params_array;
    }


    public function lower_case_array($array_strings){
        foreach($array_strings as &$element) {
            $element = str_replace(" ","",mb_strtolower($element, "UTF-8"));
          }
          return $array_strings;
    }

   
    
}