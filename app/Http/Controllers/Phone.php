<?php

namespace App\Http\Controllers;

/**
 * Helper class for normalizing Indonesian phone numbers.
 *
 * The checkout process expects the phone number to be stored in the
 * database as a string that starts with "08" followed by 8‑11 digits.
 * This class provides a static `normalize` method that converts various
 * common input formats (e.g. "+62 812-3456-7890", "0812 3456 7890")
 * into the required format.
 */
class Phone
{
    /**
     * Normalize a phone number to the format required by the validation
     * rule `regex:/^08\d{8,11}$/`.
     *
     * @param string|null $value Raw input value (may contain spaces, dashes,
     *                           leading '+62', etc.)
     * @return string Normalized phone number starting with "08".
     */
    public static function normalize(?string $value): string
    {
        if ($value === null) {
            return '';
        }
        // Remove all non‑digit characters.
        $digits = preg_replace('/\D+/', '', $value);
        // If the number starts with the country code 62 (Indonesia), replace it with leading 0.
        if (str_starts_with($digits, '62')) {
            $digits = '0' . substr($digits, 2);
        }
        // Ensure it starts with 0; if it doesn't, prepend 0 (fallback).
        if (!str_starts_with($digits, '0')) {
            $digits = '0' . $digits;
        }
        return $digits;
    }
}
