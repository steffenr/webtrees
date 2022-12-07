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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Localization\Locale\LocaleDe;
use Fisharebest\Localization\Locale\LocaleInterface;
use Illuminate\Database\Query\Builder;

/**
 * Class LanguageGerman.
 */
class LanguageGerman extends AbstractModule implements ModuleLanguageInterface
{
    use ModuleLanguageTrait;

    /**
     * Some languages treat certain letter-combinations as equivalent.
     *
     * @return array<string,string>
     */
    public function equivalentLetters(): array
    {
        return ['ä' => 'ae', 'ö' => 'oe',  'ü' => 'ue', 'ẞ' => 'ss', 'Ä' => 'AE', 'Ö' => 'OE',  'Ü' => 'UE', 'ß' => 'SS'];
    }

    /**
     * @param string  $column
     * @param string  $letter
     * @param Builder $query
     *
     * @return void
     */
    public function initialLetterSQL(string $column, string $letter, Builder $query): void
    {
        $query->where($column . ' /*! COLLATE utf8_german2_ci */', 'LIKE', '\\' . $letter . '%');
    }

    /**
     * @return LocaleInterface
     */
    public function locale(): LocaleInterface
    {
        return new LocaleDe();
    }
}
