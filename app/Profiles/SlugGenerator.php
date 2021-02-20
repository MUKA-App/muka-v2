<?php

namespace App\Profiles;

class SlugGenerator
{
    public static function generate(string $name): string
    {
        $generator = new self();
        //strip whitespace and replace it with the character -
        $asciiName = ltrim(rtrim(preg_replace('/[^\x00-\x7F]/', "", $name)));
        $nameWithDashes = preg_replace('/\s+/', '-', $asciiName);
        $lowerCaseName = strtolower($nameWithDashes);

        //strip punctuation marks except the character - and _
        $lowerCaseName = preg_replace("/(?![-_])\p{P}/u", "", $lowerCaseName);

        if (strlen($lowerCaseName) > 29) {
            $lowerCaseName = substr($lowerCaseName, 0, 29);
        }
        $hashNumber = $generator->getRandomHash();

        return $lowerCaseName === "" ? $hashNumber : $lowerCaseName . '-' . $hashNumber;
    }

    private function getRandomHash(): string
    {
        $hashTime = sha1(time());
        $randomDigits = mt_rand(10000, 1000000000);
        $randomMix = str_shuffle($hashTime . $randomDigits);
        return substr($randomMix, 0, 6);
    }
}
