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

namespace Fisharebest\Webtrees;

use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Note::class)]
class NoteTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClassExists(): void
    {
        self::assertTrue(class_exists(Note::class));
    }

    public function testNoteName(): void
    {
        $tree = $this->createMock(Tree::class);
        $note = new Note('X123', "0 @X123@ NOTE 1\n1 CONT\n1 CONT 2\n1 CONT 3\n1 CONT 4", null, $tree);

        self::assertSame('<bdi>1</bdi>', $note->fullName());
    }

    public function testNoteNameWithHtmlEntities(): void
    {
        $tree = $this->createMock(Tree::class);
        $text = '"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt." "a quote"';
        $note = new Note('X123', '0 @X123@ NOTE ' . $text, null, $tree);

        self::assertSame('<bdi>&quot;Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt.&quot; &quot;a quot…</bdi>', $note->fullName());
    }
}
