<?php

namespace App\Helpers;

use App\ValueObjects\Currency;
use Illuminate\Support\Arr;

class CurrencyConverter
{
    public static function toWord(float $amount, ?string $currencyName = null)
    {
        if (! $currencyName) {
            $currencyName = config('config.system.currency');
        }

        $currency = Currency::from($currencyName)->getCurrencyDetail();

        if ($currencyName == 'INR') {
            return self::toIndianFormat($amount, $currency);
        }

        return self::toGlobalFormat($amount, $currency);
    }

    public static function toIndianFormat(float $amount, array $currency)
    {
        if ($amount < 0) {
            $amount = abs($amount);
        }

        $unitName = Arr::get($currency, 'unit_name', 'Rupees');
        $subUnitName = Arr::get($currency, 'sub_unit_name', 'Paise');

        $words = '';

        $units = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
        $teens = ['Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', 'Ten', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
        $thousands = ['', 'Thousand', 'Lakh', 'Crore'];

        $unitPart = (int) floor($amount);
        $subUnitPart = (int) round(($amount - $unitPart) * 100);

        $unitPartArray = array_reverse(str_split($unitPart));

        $chunks = array_chunk($unitPartArray, 2, true);

        // foreach ($chunks as $index => $chunk) {
        //     if (count($chunk) > 0) {
        //         $chunkWords = '';
        //         $chunkWords .= ($chunk[1] ?? 0) > 0 ? $units[$chunk[1]] . ' Hundred ' : '';
        //         $chunkWords .= ($chunk[0] ?? 0) > 0 ? $tens[$chunk[0]] . ' ' : '';

        //         if ($index > 0 && (array_sum($chunk) > 0)) {
        //             $words = $chunkWords . $thousands[$index] . ' ' . $words;
        //         } else {
        //             $words = $chunkWords . $words;
        //         }
        //     }
        // }

        $subUnitPartWords = '';
        if ($subUnitPart > 0) {
            $subUnitPartWords = 'and '.$tens[floor($subUnitPart / 10)].' '.$units[$subUnitPart % 10].' '.$subUnitName;
        }

        return ucfirst(trim($words)).' '.$unitName.' '.$subUnitPartWords;
    }

    public function toGlobalFormat(float $amount, array $currency)
    {
        if ($amount < 0) {
            $amount = abs($amount);
        }

        $unitName = Arr::get($currency, 'unit_name', 'Rupees');
        $subUnitName = Arr::get($currency, 'sub_unit_name', 'Cent');

        $words = '';

        $units = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
        $teens = ['Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', 'Ten', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
        $thousands = ['', 'Thousand', 'Million', 'Billion', 'Trillion'];

        $unitPart = floor($amount);
        $subUnitPart = round(($amount - $unitPart) * 100);

        $unitPartArray = array_reverse(str_split($unitPart));
        $chunks = array_chunk($unitPartArray, 3, true);

        foreach ($chunks as $index => $chunk) {
            if (count($chunk) > 0) {
                $chunkWords = '';
                $chunkWords .= ($chunk[2] ?? 0) > 0 ? $units[$chunk[2]].' Hundred ' : '';
                $chunkWords .= ($chunk[1] ?? 0) > 1 ? $tens[$chunk[1]].' ' : '';
                $chunkWords .= ($chunk[1] ?? 0) == 1 ? $teens[$chunk[0]].' ' : $units[$chunk[0]].' ';

                $words = $chunkWords.$thousands[$index].' '.$words;
            }
        }

        $subUnitPartWords = '';
        if ($subUnitPart > 0) {
            $subUnitPartWords = 'and '.$tens[floor($subUnitPart / 10)].' '.$units[$subUnitPart % 10].' '.$subUnitName;
        }

        return ucfirst(trim($words)).' '.$unitName.' '.$subUnitPartWords;
    }
}
