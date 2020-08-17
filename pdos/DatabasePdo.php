<?php

//DB 정보
function pdoSqlConnect()
{
    try {
        $DB_HOST = "3.34.167.226";
        $DB_NAME = "schema_new";
        $DB_USER = "root";
        $DB_PW = "secret";
        $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME", $DB_USER, $DB_PW);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("SET NAMES UTF8");
        return $pdo;
    } catch (\Exception $e) {
        echo $e->getMessage();
    }
}