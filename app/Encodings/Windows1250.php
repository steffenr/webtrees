<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Encodings;

/**
 * Convert between Windows Code Page 1250 and UTF-8.
 *
 * @link https://en.wikipedia.org/wiki/Windows-1250
 */
class Windows1250 extends AbstractEncoding
{
    public const NAME = 'CP1250';

    protected const TO_UTF8 = [
        "\x80" => UTF8::EURO_SIGN,
        "\x81" => UTF8::REPLACEMENT_CHARACTER,
        "\x82" => UTF8::SINGLE_LOW_9_QUOTATION_MARK,
        "\x83" => UTF8::REPLACEMENT_CHARACTER,
        "\x84" => UTF8::DOUBLE_LOW_9_QUOTATION_MARK,
        "\x85" => UTF8::HORIZONTAL_ELLIPSIS,
        "\x86" => UTF8::DAGGER,
        "\x87" => UTF8::DOUBLE_DAGGER,
        "\x88" => UTF8::REPLACEMENT_CHARACTER,
        "\x89" => UTF8::PER_MILLE_SIGN,
        "\x8A" => UTF8::LATIN_CAPITAL_LETTER_S_WITH_CARON,
        "\x8B" => UTF8::SINGLE_LEFT_POINTING_ANGLE_QUOTATION_MARK,
        "\x8C" => UTF8::LATIN_CAPITAL_LETTER_S_WITH_ACUTE,
        "\x8D" => UTF8::LATIN_CAPITAL_LETTER_T_WITH_CARON,
        "\x8E" => UTF8::LATIN_CAPITAL_LETTER_Z_WITH_CARON,
        "\x8F" => UTF8::LATIN_CAPITAL_LETTER_Z_WITH_ACUTE,
        "\x90" => UTF8::REPLACEMENT_CHARACTER,
        "\x91" => UTF8::LEFT_SINGLE_QUOTATION_MARK,
        "\x92" => UTF8::RIGHT_SINGLE_QUOTATION_MARK,
        "\x93" => UTF8::LEFT_DOUBLE_QUOTATION_MARK,
        "\x94" => UTF8::RIGHT_DOUBLE_QUOTATION_MARK,
        "\x95" => UTF8::BULLET,
        "\x96" => UTF8::EN_DASH,
        "\x97" => UTF8::EM_DASH,
        "\x98" => UTF8::REPLACEMENT_CHARACTER,
        "\x99" => UTF8::TRADE_MARK_SIGN,
        "\x9A" => UTF8::LATIN_SMALL_LETTER_S_WITH_CARON,
        "\x9B" => UTF8::SINGLE_RIGHT_POINTING_ANGLE_QUOTATION_MARK,
        "\x9C" => UTF8::LATIN_SMALL_LETTER_S_WITH_ACUTE,
        "\x9D" => UTF8::LATIN_SMALL_LETTER_T_WITH_CARON,
        "\x9E" => UTF8::LATIN_SMALL_LETTER_Z_WITH_CARON,
        "\x9F" => UTF8::LATIN_SMALL_LETTER_Z_WITH_ACUTE,
        "\xA0" => UTF8::NO_BREAK_SPACE,
        "\xA1" => UTF8::CARON,
        "\xA2" => UTF8::BREVE,
        "\xA3" => UTF8::LATIN_CAPITAL_LETTER_L_WITH_STROKE,
        "\xA4" => UTF8::CURRENCY_SIGN,
        "\xA5" => UTF8::LATIN_CAPITAL_LETTER_A_WITH_OGONEK,
        "\xA6" => UTF8::BROKEN_BAR,
        "\xA7" => UTF8::SECTION_SIGN,
        "\xA8" => UTF8::DIAERESIS,
        "\xA9" => UTF8::COPYRIGHT_SIGN,
        "\xAA" => UTF8::LATIN_CAPITAL_LETTER_S_WITH_CEDILLA,
        "\xAB" => UTF8::LEFT_POINTING_DOUBLE_ANGLE_QUOTATION_MARK,
        "\xAC" => UTF8::NOT_SIGN,
        "\xAD" => UTF8::SOFT_HYPHEN,
        "\xAE" => UTF8::REGISTERED_SIGN,
        "\xAF" => UTF8::LATIN_CAPITAL_LETTER_Z_WITH_DOT_ABOVE,
        "\xB0" => UTF8::DEGREE_SIGN,
        "\xB1" => UTF8::PLUS_MINUS_SIGN,
        "\xB2" => UTF8::OGONEK,
        "\xB3" => UTF8::LATIN_SMALL_LETTER_L_WITH_STROKE,
        "\xB4" => UTF8::ACUTE_ACCENT,
        "\xB5" => UTF8::MICRO_SIGN,
        "\xB6" => UTF8::PILCROW_SIGN,
        "\xB7" => UTF8::MIDDLE_DOT,
        "\xB8" => UTF8::CEDILLA,
        "\xB9" => UTF8::LATIN_SMALL_LETTER_A_WITH_OGONEK,
        "\xBA" => UTF8::LATIN_SMALL_LETTER_S_WITH_CEDILLA,
        "\xBB" => UTF8::RIGHT_POINTING_DOUBLE_ANGLE_QUOTATION_MARK,
        "\xBC" => UTF8::LATIN_CAPITAL_LETTER_L_WITH_CARON,
        "\xBD" => UTF8::DOUBLE_ACUTE_ACCENT,
        "\xBE" => UTF8::LATIN_SMALL_LETTER_L_WITH_CARON,
        "\xBF" => UTF8::LATIN_SMALL_LETTER_Z_WITH_DOT_ABOVE,
        "\xC0" => UTF8::LATIN_CAPITAL_LETTER_R_WITH_ACUTE,
        "\xC1" => UTF8::LATIN_CAPITAL_LETTER_A_WITH_ACUTE,
        "\xC2" => UTF8::LATIN_CAPITAL_LETTER_A_WITH_CIRCUMFLEX,
        "\xC3" => UTF8::LATIN_CAPITAL_LETTER_A_WITH_BREVE,
        "\xC4" => UTF8::LATIN_CAPITAL_LETTER_A_WITH_DIAERESIS,
        "\xC5" => UTF8::LATIN_CAPITAL_LETTER_L_WITH_ACUTE,
        "\xC6" => UTF8::LATIN_CAPITAL_LETTER_C_WITH_ACUTE,
        "\xC7" => UTF8::LATIN_CAPITAL_LETTER_C_WITH_CEDILLA,
        "\xC8" => UTF8::LATIN_CAPITAL_LETTER_C_WITH_CARON,
        "\xC9" => UTF8::LATIN_CAPITAL_LETTER_E_WITH_ACUTE,
        "\xCA" => UTF8::LATIN_CAPITAL_LETTER_E_WITH_OGONEK,
        "\xCB" => UTF8::LATIN_CAPITAL_LETTER_E_WITH_DIAERESIS,
        "\xCC" => UTF8::LATIN_CAPITAL_LETTER_E_WITH_CARON,
        "\xCD" => UTF8::LATIN_CAPITAL_LETTER_I_WITH_ACUTE,
        "\xCE" => UTF8::LATIN_CAPITAL_LETTER_I_WITH_CIRCUMFLEX,
        "\xCF" => UTF8::LATIN_CAPITAL_LETTER_D_WITH_CARON,
        "\xD0" => UTF8::LATIN_CAPITAL_LETTER_D_WITH_STROKE,
        "\xD1" => UTF8::LATIN_CAPITAL_LETTER_N_WITH_ACUTE,
        "\xD2" => UTF8::LATIN_CAPITAL_LETTER_N_WITH_CARON,
        "\xD3" => UTF8::LATIN_CAPITAL_LETTER_O_WITH_ACUTE,
        "\xD4" => UTF8::LATIN_CAPITAL_LETTER_O_WITH_CIRCUMFLEX,
        "\xD5" => UTF8::LATIN_CAPITAL_LETTER_O_WITH_DOUBLE_ACUTE,
        "\xD6" => UTF8::LATIN_CAPITAL_LETTER_O_WITH_DIAERESIS,
        "\xD7" => UTF8::MULTIPLICATION_SIGN,
        "\xD8" => UTF8::LATIN_CAPITAL_LETTER_R_WITH_CARON,
        "\xD9" => UTF8::LATIN_CAPITAL_LETTER_U_WITH_RING_ABOVE,
        "\xDA" => UTF8::LATIN_CAPITAL_LETTER_U_WITH_ACUTE,
        "\xDB" => UTF8::LATIN_CAPITAL_LETTER_U_WITH_DOUBLE_ACUTE,
        "\xDC" => UTF8::LATIN_CAPITAL_LETTER_U_WITH_DIAERESIS,
        "\xDD" => UTF8::LATIN_CAPITAL_LETTER_Y_WITH_ACUTE,
        "\xDE" => UTF8::LATIN_CAPITAL_LETTER_T_WITH_CEDILLA,
        "\xDF" => UTF8::LATIN_SMALL_LETTER_SHARP_S,
        "\xE0" => UTF8::LATIN_SMALL_LETTER_R_WITH_ACUTE,
        "\xE1" => UTF8::LATIN_SMALL_LETTER_A_WITH_ACUTE,
        "\xE2" => UTF8::LATIN_SMALL_LETTER_A_WITH_CIRCUMFLEX,
        "\xE3" => UTF8::LATIN_SMALL_LETTER_A_WITH_BREVE,
        "\xE4" => UTF8::LATIN_SMALL_LETTER_A_WITH_DIAERESIS,
        "\xE5" => UTF8::LATIN_SMALL_LETTER_L_WITH_ACUTE,
        "\xE6" => UTF8::LATIN_SMALL_LETTER_C_WITH_ACUTE,
        "\xE7" => UTF8::LATIN_SMALL_LETTER_C_WITH_CEDILLA,
        "\xE8" => UTF8::LATIN_SMALL_LETTER_C_WITH_CARON,
        "\xE9" => UTF8::LATIN_SMALL_LETTER_E_WITH_ACUTE,
        "\xEA" => UTF8::LATIN_SMALL_LETTER_E_WITH_OGONEK,
        "\xEB" => UTF8::LATIN_SMALL_LETTER_E_WITH_DIAERESIS,
        "\xEC" => UTF8::LATIN_SMALL_LETTER_E_WITH_CARON,
        "\xED" => UTF8::LATIN_SMALL_LETTER_I_WITH_ACUTE,
        "\xEE" => UTF8::LATIN_SMALL_LETTER_I_WITH_CIRCUMFLEX,
        "\xEF" => UTF8::LATIN_SMALL_LETTER_D_WITH_CARON,
        "\xF0" => UTF8::LATIN_SMALL_LETTER_D_WITH_STROKE,
        "\xF1" => UTF8::LATIN_SMALL_LETTER_N_WITH_ACUTE,
        "\xF2" => UTF8::LATIN_SMALL_LETTER_N_WITH_CARON,
        "\xF3" => UTF8::LATIN_SMALL_LETTER_O_WITH_ACUTE,
        "\xF4" => UTF8::LATIN_SMALL_LETTER_O_WITH_CIRCUMFLEX,
        "\xF5" => UTF8::LATIN_SMALL_LETTER_O_WITH_DOUBLE_ACUTE,
        "\xF6" => UTF8::LATIN_SMALL_LETTER_O_WITH_DIAERESIS,
        "\xF7" => UTF8::DIVISION_SIGN,
        "\xF8" => UTF8::LATIN_SMALL_LETTER_R_WITH_CARON,
        "\xF9" => UTF8::LATIN_SMALL_LETTER_U_WITH_RING_ABOVE,
        "\xFA" => UTF8::LATIN_SMALL_LETTER_U_WITH_ACUTE,
        "\xFB" => UTF8::LATIN_SMALL_LETTER_U_WITH_DOUBLE_ACUTE,
        "\xFC" => UTF8::LATIN_SMALL_LETTER_U_WITH_DIAERESIS,
        "\xFD" => UTF8::LATIN_SMALL_LETTER_Y_WITH_ACUTE,
        "\xFE" => UTF8::LATIN_SMALL_LETTER_T_WITH_CEDILLA,
        "\xFF" => UTF8::DOT_ABOVE,
    ];
}
