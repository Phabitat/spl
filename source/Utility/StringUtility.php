<?php

namespace Spl\Utility;

final class StringUtility extends AbstractUtility
{

    /**
     * Returns the specified string in camelCase format replacing old prefix with the new prefix. If no new prefix is
     * specified, will not replace the prefixes. If no old prefix is specified, it will replace all characters with the
     * new prefix.
     *
     * @param string $string
     * @param string $newPrefix String to replace the old prefix if its specified.
     * @param string $oldPrefix Old prefix regex that will be replaced with the new prefix if its specified.
     * @return string
     */
    public static function camelise($string, $newPrefix = null, $oldPrefix = null)
    {

        // todo: Review, this might be better rewritten with native ucwords().

        /*
         * Match lower case characters followed by other lower case characters.
         *
         *   test       test
         *   test_test  testTest
         *   test test  testTest
         *   test1test  test1Test
         */

        isset($oldPrefix) || $oldPrefix = '/.+/';

        return lcfirst(preg_replace_callback('/([^\\p{L}]*)(\\p{L}+)/', function (array $matches) use ($newPrefix, $oldPrefix) {
            return (isset($newPrefix, $oldPrefix) ? preg_replace($oldPrefix, $newPrefix, $matches[1]) : $matches[1]) . ucfirst(strtolower($matches[2]));
        }, $string));
    }

    /**
     * Returns the specified string with upper case characters converted to lower case and preceded by the specified prefix.
     *
     * @param string $string
     * @param string $newPrefix
     * @param string $oldPrefix
     * @return string
     */
    public static function uncamelise($string, $newPrefix = null, $oldPrefix = null)
    {

        /*
         * Match upper case characters followed by other upper case characters.
         *
         *   Test       test
         *   TEST       test
         *   testTest   test test
         *   testTEST   test test
         *   testTEst   test test
         *   test1Test  test1 test
         */

        isset($oldPrefix) || $oldPrefix = '/^.*$/';

        /*
         * In case you need more info – read about unicode stuff at http://www.regular-expressions.info/unicode.html
         *
         * (^\p{Lu}+)           – match the first capital letter, or…
         * ([^\p{L}]*)(\p{Lu}+) – not a letter followed by a capital letter.
         */

        return preg_replace_callback('/(^\\p{Lu}+)|([^\\p{L}]*)(\\p{Lu}+)/', function (array $matches) use ($newPrefix, $oldPrefix) {

            // If we matched the beginning of the string starting with a capital, the matches will contain only to values.

            return isset($matches[3])
                ? (isset($newPrefix, $oldPrefix) ? preg_replace($oldPrefix, $newPrefix, $matches[2]) : $matches[2]) . strtolower($matches[3])
                : strtolower($matches[1]);
        }, $string);
    }

    /**
     * Generates a random string based on the specified configuration. For each value you can pass custom set of characters,
     * not just true or false. Default symbols include every character from the printable ASCII character set, except for
     * letters, digits and space – that is 32 out of total 95.
     *
     * The method will also ensure that at least one of each specified character group is present in the generated string,
     * if it's length allows, e.g., if it's greater than the total number of character groups.
     *
     * @param int $length
     * @param bool $upperCase
     * @param bool $lowerCase
     * @param bool $digits
     * @param bool $symbols
     * @return string
     */
    public static function random($length, $upperCase = true, $lowerCase = true, $digits = false, $symbols = false)
    {

        // Use default character group if no custom characters are specified.

        $upperCaseCharacters = $upperCase === true ? 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' : ($upperCase === false ? '' : (string) $upperCase);
        $lowerCaseCharacters = $lowerCase === true ? 'abcdefghijklmnopqrstuvwxyz' : ($lowerCase === false ? '' : (string) $lowerCase);
        $digitCharacters     = $digits === true ? '0123456789' : ($digits === false ? '' : (string) $digits);
        $symbolCharacters    = $symbols === true ? '!"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~' : ($symbols === false ? '' : (string) $symbols);

        $characters     = $upperCaseCharacters . $lowerCaseCharacters . $digitCharacters . $symbolCharacters;
        $characterCount = strlen($characters) - 1;
        $string         = '';

        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[rand(0, $characterCount)];
        }

        // Next we ensure that all requested characters are present in the generated string. We don't proceed if its
        // length is shorter than the minimum length of combination of one character from each group.

        $checkUpperCase = !empty($upperCaseCharacters);
        $checkLowerCase = !empty($lowerCaseCharacters);
        $checkDigits    = !empty($digitCharacters);
        $checkSymbols   = !empty($symbolCharacters);

        if ($length < (($checkUpperCase ? 1 : 0) + ($checkLowerCase ? 1 : 0) + ($checkDigits ? 1 : 0) + ($checkSymbols ? 1 : 0))) {
            return $string;
        }

        $upperCaseRegex = $checkUpperCase ? '/[' . preg_quote($upperCaseCharacters, '/') . ']/' : null;
        $lowerCaseRegex = $checkLowerCase ? '/[' . preg_quote($lowerCaseCharacters, '/') . ']/' : null;
        $digitsRegex    = $checkDigits ? '/[' . preg_quote($digitCharacters, '/') . ']/' : null;
        $symbolsRegex   = $checkSymbols ? '/[' . preg_quote($symbolCharacters, '/') . ']/' : null;

        while (true) {

            // Ensure all requested characters are present in the generated string by checking it against the regex. If
            // it's not found, we remember the required character and replace it below.

            if ($checkUpperCase && preg_match($upperCaseRegex, $string) === 0) {
                $character = $upperCaseCharacters[rand(0, strlen($upperCaseCharacters) - 1)];
            } elseif ($checkLowerCase && preg_match($lowerCaseRegex, $string) === 0) {
                $character = $lowerCaseCharacters[rand(0, strlen($lowerCaseCharacters) - 1)];
            } elseif ($checkDigits && preg_match($digitsRegex, $string) === 0) {
                $character = $digitCharacters[rand(0, strlen($digitCharacters) - 1)];
            } elseif ($checkSymbols && preg_match($symbolsRegex, $string) === 0) {
                $character = $symbolCharacters[rand(0, strlen($symbolCharacters) - 1)];
            } else {
                break;
            }

            // If we ever get to this point, we start tracking indexes that were modified, so we don't change them again
            // which might result in running in circles. Perhaps not the most resource-friendly solution, but eliminates
            // that 0.0000001% probability of fucking everything up with infinite loop or invalid value…

            isset($indexes) || $indexes = range(0, --$length);
            $string[$key = array_rand($indexes)] = $character;
            unset($indexes[$key]);
        }

        return $string;
    }
} 