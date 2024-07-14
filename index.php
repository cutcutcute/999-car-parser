<?php 
    
    include 'request_generator.php';
    include 'utils.php';
    include 'parser.php';
    include 'params.php';
    include 'logger_settings.php';


    // Здесь настраиваются все входные параметры
    $make = MAKE;
    $models = MODELS;
    $mileage = MILLEAGE;
    $gearbox = GEARBOX;
    $year = YEAR;



    $r_generator = new RequestGenerator($logger);
    $req_params = "";
    $milleage_unit="km";
    $marks_id = array(); // Глобальная переменная id марок
    $model_params = ""; // Глобальная переменная моделей


    

    $logger->info("Скрипт запущен");
    if (strlen($make) == 0){
        $logger->info("Информация о марках отсутствует");
    } else {
        $arra = get_array_from_string($make);  
        
        $logger->info("Найдено ".count($arra)." марка(-и)");
        $marks_id = $r_generator -> get_marks_id($arra); // Заполняем id марок
        
    }



    if (empty(trim($models))){
        $logger->info("Модели отсутствуют");
    } else {

        $models_array = get_array_from_string($models);

        $logger->info("Найдено ".count($models_array)." модели");
        $models_query_params = $r_generator -> get_models_id($models_array, $marks_id);
        $model_params = generate_models_query_params($models_query_params);
        
    }

$req_params.=!empty($marks_id)?"&".MAKE_ATTRIBUTE."=".join(",",array_unique($marks_id)):""; // Формировка параметров с марками
$req_params.=!empty(trim($model_params))?$model_params:""; // Формировка параметров с моделями

if ($mileage !== null){ // Формировка параметров пробега
    $req_params.="&".MILEAGE_FROM."=".strval(intval($mileage) - 20000);
    $req_params.="&".MILEAGE_TO."=".strval(intval($mileage) + 20000);
    $req_params.="&".MILLEAGE_UNIT."=".$milleage_unit;
}

if ($gearbox != null) // Формировка параметров коробки передач
        $req_params.="&".GEARBOX_TYPE."=".($gearbox===1?MECHANICAL_GEARBOX:AUTOMATIC_GEARBOX);


if ($year !== null){ // Формировка параметров года
    $req_params.="&".YEAR_FROM."=".strval(intval($year)-2);
    $req_params.="&".YEAR_TO."=".strval(intval($year)+2);
}

$req_params.="&".NEGOTIABLE."=no";
$req_params.="&".CURRENCY."=".LEI; // LEI соответственно можно заменить на EUR_CURRENCY или USD_CURRENCY
    
$result_request_string = BASE_URL.SUPPORT_REQUEST_PARAMS.$req_params; // Итоговая строка запроса

$logger->info("Сгенерированная строка поиска: ".$result_request_string);


 try{
        $parser = new Parser($logger);
        $result_statistic = $parser -> global_parse($result_request_string); // Итоговая статистика
        print_r($result_statistic);
    }   
    catch (GuzzleHttp\Exception\ConnectException $e){
        $logger->error("\nНе удалось отправить запрос.\n Проверьте ваше интернет соединение\n\n".$e->getMessage());
    }

    catch (Exception $e){
        $logger->error("Произошла непредвиденная ошибка - ".e->getMessage());
    }

   
    