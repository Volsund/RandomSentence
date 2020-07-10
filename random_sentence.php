<?php

$rand_sentence = "{Please|Just} make this {cool|awesome|random} test sentence {rotate {quickly|fast} and random|spin and be random}";


//Function searches for options in curly braces, trims the braces and returns array of first level options.
function getOptions(string $str): array
{
    $pattern = "/{(?:[^{}]+|(?R))*}/";
    preg_match_all($pattern, $str, $matches);

    return array_map(function ($string) {
        return trim($string, "\{\}");
    }, $matches[0]);
}

$firstLevelOptions = getOptions($rand_sentence);

//Checking if there are any nested options and if there are,
//we deal with them first so they don't interfere with next step.
foreach ($firstLevelOptions as $option) {
    if (getOptions($option)) {
        $optionsTogether = getOptions($option)[0];
        $secondLevelOptions = explode('|', $optionsTogether);
        $whatOption = $secondLevelOptions[array_rand($secondLevelOptions)];
        $rand_sentence = str_replace("{" . $optionsTogether . "}", $whatOption, $rand_sentence);
    }
}

//As second level options are dealt with once again we get final first level options.
$clearOptions = getOptions($rand_sentence);

//Randomly choosing options from first level options only and replacing them in sentence.
function randomReplace(array $arr, string $sentence): string
{
    $result = $sentence;
    foreach ($arr as $options) {
        $optionsDivided = explode('|', $options);
        $whatOption = $optionsDivided[array_rand($optionsDivided)];
        $result = str_replace("{" . $options . "}", $whatOption, $result);
    }
    return $result;
}

echo (randomReplace($clearOptions, $rand_sentence)) . PHP_EOL;