<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

namespace Fisharebest\Webtrees\CommonMark;

use Fisharebest\Webtrees\GedcomRecord;
use League\CommonMark\Node\Node;

/**
 * Convert XREFs within markdown text to links
 */
class XrefNode extends Node
{
    private GedcomRecord $record;

    /**
     * @param GedcomRecord $record
     */
    public function __construct(GedcomRecord $record)
    {
        parent::__construct();

        $this->record = $record;
    }

    /**
     * @return GedcomRecord
     */
    public function record(): GedcomRecord
    {
        return $this->record;
    }
}
