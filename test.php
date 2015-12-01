<?php
# To use the SDK either use Composer's autoload
require __DIR__ . "/vendor/autoload.php";


define('APPLICATION_ID',    "71fd369b");
define('APPLICATION_KEY',   "cc8bba3692e4445f39e48838b12cdf6a");

function call_api($endpoint, $parameters) {
    $ch = curl_init('https://api.aylien.com/api/v1/' . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'X-AYLIEN-TextAPI-Application-Key: ' . APPLICATION_KEY,
        'X-AYLIEN-TextAPI-Application-ID: '. APPLICATION_ID
    ));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
    $response = curl_exec($ch);
    return json_decode($response);
}

$params = array('text' => 'John is a very good football player! Bet he sucks at playing the piano. Don\'t worry though, he does not even own a piano. Everything is ok. The sun is shining, the weather is great. I love my new pair of shoes.');
$sentiment = call_api('sentiment', $params);
$language = call_api('language', $params);

echo sprintf("Sentiment: %s (%F)", $sentiment->polarity, $sentiment->polarity_confidence),
PHP_EOL;
echo sprintf("Language: %s (%F)", $language->lang, $language->confidence),
PHP_EOL;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width">
    <title>Semantic Text Analysis</title>
    <link href="style.css" rel="stylesheet" type="text/css"/>
    <script src="https://code.jquery.com/jquery-2.1.3.min.js" type="text/javascript"></script>
    <script src="script.js" type="text/javascript"></script>
</head>
<body>
<h1>*magic* Text Analysis</h1>
<div class="leftContainer">
    <h2>Input</h2>
    <form id="form">
        <textarea id="textInput" name="textInput"></textarea>
        <input type="submit" value="analyse">
    </form>
</div>
<div class="rightContainer">
    <h2>Output</h2>
    <div id="output">
    </div>
</div>



<span style="clear:both;"></span>

</body>
</html>

