
<?php
    // Базовый URL
    define("BASE_URL", "https://999.md/ru/list/transport/cars");
    define("SUPPORT_REQUEST_PARAMS", "?aof=1&hide_duplicates=no&applied=1&show_all_checked_childrens=no");

    define("CACERT_PEM_PATH", "cacert.pem"); // Путь до сертификата

    // Год
    define("YEAR_FROM", "r_7_19_from");
    define("YEAR_TO", 'r_7_19_to');

    // Пробег
    define("MILEAGE_FROM","r_1081_104_from");
    define("MILEAGE_TO","r_1081_104_to");
    define("MILLEAGE_UNIT", "r_1081_104_unit");

    //Коробка передач
    define("GEARBOX_TYPE", "o_5_101");
    define("MECHANICAL_GEARBOX",4);
    define("AUTOMATIC_GEARBOX", 16);
    

    // Валюта
    define("CURRENCY","selected_currency");
    define("EUR_CURRENCY", "eur");
    define("USD_CURRENCY", "usd");
    define("LEI", "mdl");

    

    define("NEGOTIABLE", "r_6_2_negotiable"); // С параметром noказывает объявления без договорной цены 

    //Аттрибут марки
    define("MAKE_ATTRIBUTE","o_1_20");

    // 1 расскрывает полный список марок
    define("AOF", "aof");

    //Если передать в строке id марок, то раскроются аттрибуты с моделями
    define("MARKS", "eo");

    // Значения аттрибута модели + id марки
    define("MODELS_STARTSWITH", "o_1_21_");



    define("PAGGINATION_X_PATH",'//*[@id="js-pjax-container"]/div/div[1]/nav');


    