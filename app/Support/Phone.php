<?php

namespace App\Support;

final class Phone
{
    public static function normalize(?string $value, string $defaultCountryCode = '34'): ?string
    {
        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }

        // Para lógica: dejamos solo dígitos y +
        $clean = preg_replace('/[^\d+]/', '', $raw) ?? $raw;

        // 00XX -> +XX
        if (str_starts_with($clean, '00')) {
            $clean = '+' . substr($clean, 2);
        }

        // Ya viene con +
        if (str_starts_with($clean, '+')) {
            $clean = '+' . ltrim(substr($clean, 1), '+');
            return $clean;
        }

        // Solo dígitos
        $digits = preg_replace('/\D/', '', $clean) ?? $clean;

        // Heurística ES: 9 dígitos empezando por 6/7/9 => +34
        if (strlen($digits) === 9 && preg_match('/^[679]/', $digits)) {
            return '+' . $defaultCountryCode . $digits;
        }

        // Si parece 34 + 9 dígitos sin + (34600111222) => +34600111222
        if (strlen($digits) === 11 && str_starts_with($digits, $defaultCountryCode)) {
            return '+' . $digits;
        }

        // No asumimos país si no está claro
        return $raw;
    }
}