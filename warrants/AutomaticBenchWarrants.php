<?php
/*
MIT License

Copyright (c) 2021 Jacob Fear and Red Robot

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

So Basically, in a very rough manner, it will get any citations from 14 days ago, that are unpaid and still active, and automatically make a Warrant with the Judge name
"Automatic Bench Warrant", and will also send a embed to your set discord channel(Say the dispatch one) with a embed showing deets on it. 
https://gyazo.com/f782df6411070a07b161605f84a9f15e is a example, with info blurred out. 

You'll need to know how to set a cron job up to run this script daily, we can not help you with that. 
*/

$DiscordWebhookURL = "YOURWEBHOOKHERE";
$townID = "YOURAPEXTOWNIDHERE";
$APIKEY = "APEXAPIKEYHERE";

function send_to_discord($cite)
{
    $json_data = json_encode([
    // Message
    "content" => "Automatic Bench Warrant",
    // Username
    "username" => "Deskuty Dave",
    // Avatar URL.
    // Uncoment to replace image set in webhook
    //"avatar_url" =>
    // Text-to-speech
    "tts" => false,
    // File upload
    // "file" => "",
    // Embeds Array
    "embeds" => [[
    // Embed Title
    "title" => "Automatic Bench Warrant",
    // Embed Type
    "type" => "rich",
    // Embed Description
    "description" => $cite['summoryOfCharges'],
    // Timestamp of embed must be formatted as ISO8601
    "timestamp" => date("c", strtotime("now")) ,
    // Embed left border color in HEX
    "color" => "12320855",
    // Author
    "author" => ["name" => "A Warrant has been issued for: " . $cite['name']],
    // Additional Fields array
    "fields" => [
    // Field 1
    ["name" => "SL Username", "value" => $cite['slUsername'], "inline" => true],
    // Field 2
    ["name" => "Date of Birth", "value" => $cite['dateOfBirth'], "inline" => true], ["name" => "Gender", "value" => $cite['sex'], "inline" => true], ["name" => "Species", "value" => $cite['race'], "inline" => true], ["name" => "Eye Color", "value" => $cite['eyeColor'], "inline" => true], ["name" => "Hair Color", "value" => $cite['hairColor'], "inline" => true], ["name" => "Height", "value" => $cite['height'], "inline" => true], ["name" => "Weight", "value" => $cite['weight'], "inline" => true]
    // Etc..
    ]]]], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $ch = curl_init($GLOBALS['DiscordWebhookURL']);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-type: application/json'
    ));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    // If you need to debug, or find out why you can't send message uncomment line below, and execute script.
    // echo $response;
    curl_close($ch);
}

function notification()
{
    $json_data = json_encode([
    // Message
    "content" => "Running ABW Routine",
    // Username
    "username" => "Deskuty Dave",
    // Avatar URL.
    // Uncoment to replace image set in webhook
    //"avatar_url" => "https://ru.gravatar.com/userimage/28503754/1168e2bddca84fec2a63addb348c571d.jpg?size=512",
    // Text-to-speech
    "tts" => false,
    // File upload
    // "file" => "",
    // Embeds Array
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $ch = curl_init($GLOBALS['DiscordWebhookURL']);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-type: application/json'
    ));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    // If you need to debug, or find out why you can't send message uncomment line below, and execute script.
    // echo $response;
    curl_close($ch);
}

function create_warrant($info)
{
    echo "CREATING WARRANT";
    $citationinfo = array();
    $citationdate = date_create(NULL);
    $citationinfo['srn'] = $info->srn;
    $citationinfo['casenumber'] = "ABW" . $info->number;
    $citationinfo['slUsername'] = $info->slUsername;
    $citationinfo['name'] = $info->name;
    $citationinfo['dateOfBirth'] = $info->dateOfBirth;
    $citationinfo['cardState'] = $info->cardState;
    $citationinfo['cardNumber'] = $info->cardNumber;
    $citationinfo['sex'] = $info->sex;
    $citationinfo['race'] = $info->race;
    $citationinfo['height'] = $info->height;
    $citationinfo['weight'] = $info->weight;
    $citationinfo['eyeColor'] = $info->eyeColor;
    $citationinfo['hairColor'] = $info->hairColor;
    $citationinfo['summoryOfCharges'] = $info->charges[0]->remarks;
    $citationinfo['judgesName'] = "Automatic Bench Warrant";
    $citationinfo['townId'] = $townID;
    $citationinfo['dateIssued'] = date_format($citationdate, "c");
    $citationinfo['address'] = $info->address;
    $citationinfo['city'] = $info->city;
    $citationinfo['state'] = $info->state;
    $citationinfo['zip'] = $info->zipCode;

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://www.apexdesignssl.com/api/towncontrol/Warrant/Create/' . $townID,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
    "townId": "' . $citationinfo['townId'] . '",
    "srn": "' . $citationinfo['srn'] . '",
    "caseNumber": "' . $citationinfo['casenumber'] . '",
    "slUsername": "' . $citationinfo['slUsername'] . '",
    "name": "' . $citationinfo['name'] . '",
    "dateOfBirth": "' . $citationinfo['dateOfBirth'] . '",
    "cardState": "' . $citationinfo['cardState'] . '",
    "cardNumber": "' . $citationinfo['cardNumber'] . '",
    "sex": "' . $citationinfo['sex'] . '",
    "race": "' . $citationinfo['race'] . '",
    "height": ' . $citationinfo['height'] . ',
    "weight": ' . $citationinfo['weight'] . ',
    "eyeColor": "' . $citationinfo['eyeColor'] . '",
    "hairColor": "' . $citationinfo['hairColor'] . '",
    "address": "' . $citationinfo['address'] . '",
    "city": "' . $citationinfo['city'] . '",
    "state":"' . $citationinfo['state'] . '",
    "zip": "' . $citationinfo['zip'] . '",
    "summaryOfCharges": "' . $citationinfo['summoryOfCharges'] . '",
    "judgesName": "' . $citationinfo['judgesName'] . '",
    "dateIssued": "' . $citationinfo['dateIssued'] . '",
    "extraditable": false,
    "executed": false,
    "bondPaid": false,
    "finePaid": false
}',
        CURLOPT_HTTPHEADER => array(
            "token: " . $APIKEY,
            'Content-Type: application/json'
        ) ,
    ));

    $response = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);
    echo $response;

    send_to_discord($citationinfo);
}

date_default_timezone_set("UTC");

$date = date_create(NULL);
//echo date_format($date,"c");
$date->modify('-15 days');

$date2 = date_create(NULL);
$date2->modify('-14 days');
$now = new DateTime();

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.apexdesignssl.com/api/towncontrol/Citation/List/' . $townID,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
        'token: ' . $APIKEY,
    ) ,
));

$response = curl_exec($curl);

curl_close($curl);

notification();
$d1 = json_decode($response);
foreach ($d1 as $query)
{
    $issueddate = new DateTime($query->issued);
    if ($issueddate > $date & $issueddate < $date2 & $query->status == "ACTIVE")
    {
        echo "OK FOUND : " . $query->name;
        create_warrant($query);
    }

}

?>
