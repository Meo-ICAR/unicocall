<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ExcelImportHelpers
{
    /**
     * Parse date from various Excel formats (Italian, English, Excel serial dates)
     */
    public static function parseDate($value): ?\DateTime
    {
        if (empty($value)) {
            return null;
        }

        try {
            // Try Excel serial date format first
            if (is_numeric($value)) {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value);
                return $date;
            }

            // Check if the date looks like M/D/Y format (second part > 12)
            if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $value, $matches)) {
                $first = (int) $matches[1];
                $second = (int) $matches[2];

                // If second part > 12, it must be day (M/D/Y format)
                if ($second > 12) {
                    $date = \DateTime::createFromFormat('m/d/Y', $value);
                    if ($date) {
                        return $date;
                    }
                    $date = \DateTime::createFromFormat('m/d/y', $value);
                    if ($date) {
                        return $date;
                    }
                }
            }

            // Excel files typically use M/D/Y format, convert to Italian D/M/Y
            if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $value, $matches)) {
                $excelMonth = (int) $matches[1];  // First part is month in Excel
                $excelDay = (int) $matches[2];  // Second part is day in Excel
                $year = (int) $matches[3];

                // Convert Excel M/D/Y to Italian D/M/Y
                if (checkdate($excelMonth, $excelDay, $year)) {
                    try {
                        $date = \DateTime::createFromFormat('Y-m-d', sprintf('%04d-%02d-%02d', $year, $excelMonth, $excelDay));
                        if ($date) {
                            return $date;
                        }
                    } catch (\Exception $e) {
                        Log::warning("Invalid Excel date: {$value} - " . $e->getMessage());
                    }
                }
            } else {
                // Try Italian D/M/Y format
                $date = \DateTime::createFromFormat('d/m/Y', $value);
                if ($date) {
                    return $date;
                }

                $date = \DateTime::createFromFormat('d/m/y', $value);
                if ($date) {
                    return $date;
                }

                // Try English M/D/Y format only for obvious cases (month > 12)
                $date = \DateTime::createFromFormat('m/d/Y', $value);
                if ($date) {
                    return $date;
                }

                $date = \DateTime::createFromFormat('m/d/y', $value);
                if ($date) {
                    return $date;
                }
            }

            // Try ISO format
            $date = \DateTime::createFromFormat('Y-m-d', $value);
            if ($date) {
                return $date;
            }

            // Try to create from string (last resort)
            return new \DateTime($value);
        } catch (\Exception $e) {
            Log::warning("Could not parse date: {$value} - " . $e->getMessage());
            return null;
        }
    }

    protected function parseDateTime($value)
    {
        if (empty($value)) {
            return null;
        }

        // Handle Italian datetime formats
        $dateTimeFormats = [
            'd/m/Y H:i', 'd/m/Y H:i:s', 'd-m-Y H:i',
            'd/m/y H:i', 'd-m-y H:i', 'Y-m-d H:i:s'
        ];

        foreach ($dateTimeFormats as $format) {
            try {
                $dateTime = Carbon::createFromFormat($format, $value);
                if ($dateTime) {
                    return $dateTime->format('Y-m-d H:i:s');
                }
            } catch (\Exception $e) {
                // Continue to next format
            }
        }

        return null;
    }

    /**
     * Parse integer from Excel cell (handles various formats)
     */
    public static function parseInteger($value): ?int
    {
        if (empty($value)) {
            return null;
        }

        try {
            // Remove thousands separators and spaces
            $cleaned = str_replace(['.', ' ', ','], '', $value);

            // Remove any remaining non-numeric characters except minus
            $cleaned = preg_replace('/[^0-9-]/', '', $cleaned);

            return (int) $cleaned;
        } catch (\Exception $e) {
            Log::warning("Could not parse integer: {$value} - " . $e->getMessage());
            return null;
        }
    }

    /**
     * Parse boolean from various Excel formats (Sì/No, Yes/No, 1/0, Vero/Falso, etc.)
     */
    public static function parseBoolean($value): bool
    {
        if (empty($value)) {
            return false;
        }

        // Convert to string and normalize
        $stringValue = strtolower(trim((string) $value));

        // Italian values
        if (in_array($stringValue, ['sì', 'si', 'vero', '1', 'true'])) {
            return true;
        }

        // English values
        if (in_array($stringValue, ['yes', 'y', 'true', '1'])) {
            return true;
        }

        // Numeric values
        if (is_numeric($stringValue) && (float) $stringValue > 0) {
            return true;
        }

        // Check for common positive indicators
        if (in_array($stringValue, ['x', 'ok', 'attivo', 'active', 'abilitato', 'enabled'])) {
            return true;
        }

        return false;
    }

    /**
     * Clean string value (trim, remove extra spaces)
     */
    public static function cleanString(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        return trim(preg_replace('/\s+/', ' ', $value));
    }

    /**
     * Parse amount from Italian/Excel currency format (handles both comma and decimal points)
     */
    public static function parseAmount(?string $value): ?float
    {
        if (empty($value)) {
            return 0.0;
        }

        try {
            // Remove currency symbols and spaces
            $cleaned = str_replace(['EUR', 'EUR ', ' ', '', ''], '', $value);

            // Handle different decimal formats:
            // Italian: 1.234,56 -> 1234.56 (dot = thousands, comma = decimal)
            // English/Excel: 1,234.56 -> 1234.56 (comma = thousands, dot = decimal)

            if (strpos($cleaned, ',') !== false && strpos($cleaned, '.') !== false) {
                // Both separators present - determine which is decimal
                $parts = explode('.', $cleaned);
                if (count($parts) === 2 && strlen($parts[1]) <= 2) {
                    // Dot is decimal (English format): 1,234.56
                    $cleaned = str_replace(',', '', $cleaned);
                } else {
                    // Comma is decimal (Italian format): 1.234,56
                    $cleaned = str_replace('.', '', $cleaned);
                    $cleaned = str_replace(',', '.', $cleaned);
                }
            } elseif (strpos($cleaned, ',') !== false) {
                // Only comma present - check if it's decimal or thousands
                $parts = explode(',', $cleaned);
                if (count($parts) === 2 && strlen($parts[1]) <= 2) {
                    // Comma is decimal (Italian): 1234,56
                    $cleaned = str_replace(',', '.', $cleaned);
                } else {
                    // Comma is thousands (English): 1,234
                    $cleaned = str_replace(',', '', $cleaned);
                }
            }
            // If only dot present, assume it's decimal (English/Excel)

            // Remove any remaining non-numeric characters except dot and minus
            $cleaned = preg_replace('/[^0-9.-]/', '', $cleaned);

            return (float) $cleaned;
        } catch (\Exception $e) {
            Log::warning("Could not parse amount: {$value} - " . $e->getMessage());
            return 0.0;
        }
    }

    /**
     * Clean and validate VAT number (Italian P.IVA format)
     */
    public static function cleanVatNumber(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // Remove spaces and common separators
        $cleaned = preg_replace('/[\s\-\.]/', '', $value);

        // Remove country prefix if present (IT, etc.)
        if (preg_match('/^[A-Z]{2}/', $cleaned)) {
            $cleaned = substr($cleaned, 2);
        }

        // Return uppercase version
        return strtoupper(trim($cleaned));
    }

    /**
     * Clean and validate Italian fiscal code (Codice Fiscale)
     */
    public static function cleanFiscalCode(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // Remove spaces and common separators
        $cleaned = preg_replace('/[\s\-\.]/', '', $value);

        // Return uppercase version (Italian fiscal codes are uppercase)
        return strtoupper(trim($cleaned));
    }

    /**
     * Parse percentage value (handles % symbol and decimal format)
     */
    public static function parsePercentage(?string $value): ?float
    {
        if (empty($value)) {
            return null;
        }

        try {
            // Remove % symbol and spaces
            $cleaned = str_replace(['%', ' '], '', $value);

            // Replace comma with dot for decimal
            $cleaned = str_replace(',', '.', $cleaned);

            // If value is > 1, assume it's already a percentage
            $percentage = (float) $cleaned;

            // If value is <= 1, convert to percentage (e.g., 0.22 -> 22)
            if ($percentage <= 1) {
                $percentage = $percentage * 100;
            }

            return $percentage;
        } catch (\Exception $e) {
            Log::warning("Could not parse percentage: {$value} - " . $e->getMessage());
            return null;
        }
    }

    /**
     * Validate Italian VAT number format
     */
    public static function isValidItalianVatNumber(?string $vatNumber): bool
    {
        if (empty($vatNumber)) {
            return false;
        }

        // Italian VAT number must be 11 digits
        return preg_match('/^[0-9]{11}$/', $vatNumber) === 1;
    }

    /**
     * Validate Italian fiscal code format
     */
    public static function isValidItalianFiscalCode(?string $fiscalCode): bool
    {
        if (empty($fiscalCode)) {
            return false;
        }

        // Italian fiscal code is 16 characters (letters and numbers)
        return preg_match('/^[A-Z0-9]{16}$/', $fiscalCode) === 1;
    }

    /**
     * Format currency for display
     */
    public static function formatCurrency(?float $amount, string $currency = 'EUR'): string
    {
        if (empty($amount)) {
            return '0,00 ' . $currency;
        }

        return number_format($amount, 2, ',', '.') . ' ' . $currency;
    }

    /**
     * Format date for display (Italian format)
     */
    public static function formatDate(?\DateTime $date): string
    {
        if (empty($date)) {
            return '';
        }

        return $date->format('d/m/Y');
    }
}
