<?php

require "vendor/autoload.php";

use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;

$data = json_decode(file_get_contents("php://input"));

$text = $data->text;

$client = new Client("AIzaSyCXgO6j5ftABxFQ2MKKiqWOvkWDRdm_MHw");

$respose = $client->geminiPro()->generateContent(
    new TextPart($text),
);

echo $respose->text();
