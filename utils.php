



<?php
/**
 *  Разбивает строку с переданными марками или моделями в массив
 * @param mixed $string_elemets
 * @return array
 */
function get_array_from_string($string_elemets): array{
    $arr_str =  explode(",", mb_strtolower($string_elemets));
    $string_elemets = array_map("trim_text", $arr_str);
    return $string_elemets;
}


function trim_text($text){
    return trim($text);
}


function generate_models_query_params($models_arr){
    $res_str = "";
    foreach($models_arr as $query => $val){
        $req_param_and_value = "&".$query."=".join(",", $val["models"]);
        $res_str.=$req_param_and_value;
    }

    return $res_str;
}