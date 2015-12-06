<?php
/**
 */

require __DIR__ . "/vendor/autoload.php";


define('APPLICATION_ID',    "71fd369b");
define('APPLICATION_KEY',   "cc8bba3692e4445f39e48838b12cdf6a");
define('MIN_TEXT_LENGTH', 10);

// SENTIMENT ANALYSIS

define('MSG_POSITIVE', "You seem like a happy person!");
define('MSG_NEGATIVE',"Cheer up.");
define('MSG_NEUTRAL',"");
define('CONFIDENCE_THRESHOLD', 0.9);

$textapi = new AYLIEN\TextAPI(APPLICATION_ID, APPLICATION_KEY);




$errortext;
$language = "en";
$negative = false;
$positive = false;
$neutral = false;
$moody = false;
$message;
$image;
$class;

$output;

function getPolarity($textapi, $text, $language) {
    $sentiment = $textapi->Sentiment(array('text' => $text, 'language' => $language));
    $polarity = $sentiment->polarity;
    $confidence = $sentiment->polarity_confidence;
    return array($polarity, $confidence, $text);
}
//
if(isset($_POST['submit']) && (!empty($_POST['q1']) || !empty($_POST['q2']) || !empty($_POST['q3']))) {
    $q1 = $_POST['q1'];
    $q2 = $_POST['q2'];
    $q3 = $_POST['q3'];
    $all = trim($q1 . " ". $q2 . " ". $q3);


    if (strlen($all) > MIN_TEXT_LENGTH) {
        $answers = array($q1, $q2, $q3);
        $sentiments = array();

        $confidence = 0;
        $negative_score = 0;
        $positive_score = 0;
        $neutral_score = 0;

        foreach ($answers as $answer) {
            if ($answer){
                $sentiment = getPolarity($textapi, $answer, $language);
                $sentiments[] = $sentiment;

                $confidence += $sentiment[1];
                if($sentiment[0] == "positive") {
                    $positive_score++;
                }
                elseif ($sentiment[0] == "negative") {
                    $negative_score++;
                }
                else {
                    $neutral_score++;
                }
            } else {
                // ignore 'empty' answer for now
            }
        }
        $confidence = $confidence/count($sentiments);

       // echo var_dump($sentiments);
       // echo "<p>positive: ". $positive_score . "</p>";
       // echo "<p>negative: ". $negative_score . "</p>";
       // echo "<p>neutral: ". $neutral_score . "</p>";

        $confident = false;
        if ($confidence > CONFIDENCE_THRESHOLD && count($sentiments) >= 2) {
            $confident = true;
        }

        if ($positive_score == count($sentiments)) {
            if ($confident) {
                $message = "You are the happiest person ever.";
                $image = "img/veryhappy.jpg";
            } else {
                $message = "Congratulations. You appear to be in a good mood.";
                $image = "img/happy.jpg";
            }
            $positive = true;
            $class = "happy";


        } elseif ($negative_score == count($sentiments)) {
            if ($confident) {
                $message = "You seem to be seriously depressed. Get help!";
                $image = "img/hug.jpg";
            } else {
                $message = "Having a rough day, huh?";
                $image = "img/rough.jpg";
            }
            $negative = true;
            $class = "sad";

        } elseif ($negative_score >= 1 && $positive_score >= 1) {
            if ($confident) {
                $message = "Very moody.";
            } else {
                $message = "You appear to be a little moody.";
            }
            $moody = true;
            $class = "moody";
            $image = "img/moody.jpg";

        } elseif ($positive_score >= (count($sentiments) - 1)){
            $message = "You are generally in a good mood.";
            $positive = true;
            $class = "good";
            $image = "img/good.jpg";
        }
        elseif ($negative_score >= (count($sentiments) - 1)){
            $message = "Your mood could use some improvement.";
            $negative = true;
            $image = "img/grumpy.jpg";
            $class = "grumpy";
        } else {
            $message = "neutral";
            $neutral = true;
            $image = "img/shopping.jpg";
            $class = "neutral";
        }



    } else {

        $errortext = "<p>Don't be shy. Tell me a little more than just <i>\"" . $all . "\".</i></p>";

    }

} elseif (isset($_POST['submit'])) {
    $errortext = "<p id='plankton'>Are you too depressed to fill out the form? <br>... <br>Why not give me something to work with and I might surprise you!</p>";
    $image = "img/planktonfreud.jpg";
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width">
    <title>Mood Analysis</title>
    <link href="style.css" rel="stylesheet" type="text/css"/>
    <script src="https://code.jquery.com/jquery-2.1.3.min.js" type="text/javascript"></script>
</head>
<body>


<h1>*magic* moooood analyzer</h1>
<div class="guidelines"><p>Please follow these simple guidelines for best results:</p>
<ul>
    <li>Provide the requested information in <b>full sentences.</b></li>
    <li><b>Always</b> refer to yourself in the 3rd person.</li>
    <li>Frequently <b>use your first name</b> instead of personal pronouns (like <i>he/she, him/her)</i>. </li>
</ul></div>
<div class="leftContainer">
    <h2>Input</h2>

    <form id="form" method="post">

        <label for="q1">How would you describe yourself as an individual?</label><textarea id="q1" name="q1"></textarea>
        <label for="q2">Why did you get out of bed this morning and was it worth it?</label><textarea id="q2" name="q2"></textarea>
        <label for="q3">Suggest anything that would make today even more perfect than it already is:</label><textarea id="q3" name="q3"></textarea>
        <input type="submit" name="submit" value="analyse">
    </form>
</div>
<div class="rightContainer">
    <h2>Output</h2>
    <div id="output"<?php if ($class) { echo " class='". $class ."'";} ?> >
        <?php
        if ($errortext) { echo $errortext;}
        if ($message) { echo "<p>" . $message . "</p>"; }
        if ($image) { echo "<img src='" . $image . "' height='300px'";}
        ?>

    </div>
</div>

<?php if(!empty($all)) {}

?>

<?php if (isset($_POST['submit']) && !$errortext) {
    echo "<div class='bottomContainer'><h2>Here's what you wrote:</h2>";

    echo $all;

    $params = array('title' => 'What I have to say about my life', 'text' => $all, 'sentences_percentage' => 80,  'language' => 'en');
    $summary = $textapi->Summarize($params);
    if (count($summary->sentences)) {
        echo "<br><h3>which can be summarized into:</h3>";
        foreach ($summary->sentences as $sentence) { echo $sentence,"\n"; }
    }

echo "<br><h3>The stats:</h3><ol>";
foreach ($sentiments as $sentiment) {

    echo "<li><b>".$sentiment[0] . " (". round($sentiment[1], 2 ). ")</b>  " . $sentiment[2] ."</li>"; }

    echo "</ol></div>";
}
    ?>

<?php
?>

</body><html>
