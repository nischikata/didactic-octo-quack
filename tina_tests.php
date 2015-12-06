<?php
/**

App name
    FH HAGENBERG's App
App ID
    71fd369b
Key
    cc8bba3692e4445f39e48838b12cdf6a
Endpoint
    https://api.aylien.com/api/v1

 *
 * By sending HTTP POST or GET requests to https://api.aylien.com/api/v1 followed by the
 * endpoint name, e.g. for a Concept Extraction call the full URL would be https://api.aylien.com/api/v1/concepts.
 *
 * Requests to the API must be authorized by adding the following headers:

X-AYLIEN-TextAPI-Application-Key must be set to your Application Key.
X-AYLIEN-TextAPI-Application-ID must be set to your Application ID.


 */

# To use the SDK either use Composer's autoload
require __DIR__ . "/vendor/autoload.php";


define('APPLICATION_ID',    "71fd369b");
define('APPLICATION_KEY',   "cc8bba3692e4445f39e48838b12cdf6a");
define('MIN_TEXT_LENGTH', 10);

// SENTIMENT ANALYSIS

define('MSG_POSITIVE', "You seem like a happy person!");
define('MSG_NEGATIVE',"Cheer up.");
define('MSG_NEUTRAL',"");

$textapi = new AYLIEN\TextAPI("71fd369b", "cc8bba3692e4445f39e48838b12cdf6a");

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



$text = 'John is a very good football player! But he sucks at playing the piano. Don\'t worry though, he does not even own a piano. Everything is ok. The sun is shining, the weather is great. I love my new pair of shoes.';
$errortext = "";
$negative = false;
$positive = false;

if(isset($_POST['textInput']))
{
    var_dump($_POST['textInput']);
 //   echo("First name:" . "\n");
 //   echo("   " . $_POST['textInput'] . "     ");
    $text = $_POST['textInput'];//htmlentities($_POST['textInput'], ENT_QUOTES, "UTF-8");

    if (strlen($text) >= MIN_TEXT_LENGTH) {


        $params = array('text' => $text);
        $sentiment = call_api('sentiment', $params);
        $language = call_api('language', $params);

        echo sprintf("Sentiment: %s (%F)", $sentiment->polarity, $sentiment->polarity_confidence),
        PHP_EOL;
        echo sprintf("Language: %s (%F)", $language->lang, $language->confidence),
        PHP_EOL;

        $negative = ($sentiment->polarity == "negative");
        $positive = ($sentiment->polarity == "positive");

        // SUMMARY
$params = array('title' => 'About', 'text' => $text, 'sentences_percentage' => 100,  'language' => $language);
$summary = $textapi->Summarize($params);
//echo $text;
//$summary = call_api('summarize', $params);//$textapi->Summarize(array('text' => $text, 'title' => 'About','sentences_number' => 4, language => "en"));
foreach ($summary->sentences as $sentece) { echo $sentece,"\n"; }
/// SUMMARY END

    } else {
        $errortext = "Your input text was too short. Please add a little more.";
    }
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width">
    <title>Semantic Text Analysis</title>
    <link href="style.css" rel="stylesheet" type="text/css"/>
    <script src="https://code.jquery.com/jquery-2.1.3.min.js" type="text/javascript"></script>

</head>
<body <?php if ($negative) { echo "class='happy'"; } else { echo "class='rainbow'";}?>>
<h1>*magic* Text Analysis</h1>
<div class="leftContainer">
    <h2>Input</h2>
    <form id="form" method="post">
        <textarea id="textInput" name="textInput"></textarea>
        <input type="submit" value="analyse">
    </form>
</div>
<div class="rightContainer">
    <h2>Output</h2>
    <div id="output"><p><?php
            if ($negative) {
                echo "<h3> Cheer up!</h3>";
            } else if ($positive) {
                echo "<h3> You seem like a happy person!</h3>";
            }


             ?></p>
    </div>
</div>



<span style="clear:both;"></span>
<div class="bottomContainer">
<h2>Here's what you wrote:</h2>
<?php
echo $text;
?>
</div>

</body>
</html>

