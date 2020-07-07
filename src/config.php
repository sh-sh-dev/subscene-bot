<?php

// CONFIGURATION VARIABLES

$timezone = "Asia/Tehran";
$token = "123456:xyz";

$dbConfiguration = [
    "host" => "localhost",
    "user" => "root",
    "password" => "mysql",
    "name" => "subscene-bot"
];

$subscene = [
    "email" => "subscene.admin@gmail.com",
    "password" => "123456789"
];


date_default_timezone_set($timezone);

$db = new mysqli($dbConfiguration["host"], $dbConfiguration["user"], $dbConfiguration["password"], $dbConfiguration["name"]);

$db->query("SET NAMES 'utf8mb4'");
$db->query("SET CHARACTER SET 'utf8mb4'");
$db->query("SET character_set_connection = 'utf8mb4'");
