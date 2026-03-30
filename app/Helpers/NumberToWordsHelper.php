<?php

namespace App\Helpers;

class NumberToWordsHelper
{
    public static function convert($amount)
    {
        $ones = array(
            "", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine",
            "Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen",
            "Seventeen", "Eighteen", "Nineteen"
        );

        $tens = array(
            "", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"
        );

        $num = floor($amount);
        $cents = round(($amount - $num) * 100);

        $dollars = self::convertNumber($num, $ones, $tens);
        $cents_text = $cents > 0 ? " and " . self::convertNumber($cents, $ones, $tens) . " Cents" : " Only";

        return $dollars . " Dollars" . $cents_text;
    }

    private static function convertNumber($num, $ones, $tens)
    {
        if ($num < 20) {
            return $ones[$num];
        } elseif ($num < 100) {
            $ten = floor($num / 10);
            $unit = $num % 10;
            return $tens[$ten] . ($unit > 0 ? "-" . $ones[$unit] : "");
        } elseif ($num < 1000) {
            $hundred = floor($num / 100);
            $remainder = $num % 100;
            $words = $ones[$hundred] . " Hundred";
            if ($remainder > 0) {
                $words .= " and " . self::convertNumber($remainder, $ones, $tens);
            }
            return $words;
        } elseif ($num < 1000000) {
            $thousand = floor($num / 1000);
            $remainder = $num % 1000;
            $words = self::convertNumber($thousand, $ones, $tens) . " Thousand";
            if ($remainder > 0) {
                if ($remainder < 100) {
                    $words .= " and";
                }
                $words .= " " . self::convertNumber($remainder, $ones, $tens);
            }
            return $words;
        } elseif ($num < 1000000000) {
            $million = floor($num / 1000000);
            $remainder = $num % 1000000;
            $words = self::convertNumber($million, $ones, $tens) . " Million";
            if ($remainder > 0) {
                $words .= " " . self::convertNumber($remainder, $ones, $tens);
            }
            return $words;
        }

        return "Number too large";
    }
}
