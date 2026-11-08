<?php

namespace App\Support;

class Search
{
    /**
     * Kolom id_asset dan nipp bercharset ascii. Pencarian LIKE yang berisi
     * karakter non-ASCII pada kolom itu menimbulkan error collation (500),
     * jadi kolom tersebut hanya dicari bila kata kunci murni ASCII.
     */
    public static function isAscii(string $text): bool
    {
        return ! preg_match('/[^\x00-\x7F]/', $text);
    }
}