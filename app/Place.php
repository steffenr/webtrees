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

use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Module\ModuleListInterface;
use Fisharebest\Webtrees\Module\PlaceHierarchyListModule;
use Fisharebest\Webtrees\Services\ModuleService;
use Illuminate\Support\Collection;

use function e;
use function is_object;
use function preg_split;
use function strip_tags;
use function trim;

use const PREG_SPLIT_NO_EMPTY;

/**
 * A GEDCOM place (PLAC) object.
 */
class Place
{
    // "Westminster, London, England"
    private string $place_name;

    /** @var Collection<int,string> The parts of a place name, e.g. ["Westminster", "London", "England"] */
    private Collection $parts;

    private Tree $tree;

    /**
     * Create a place.
     *
     * @param string $place_name
     * @param Tree   $tree
     */
    public function __construct(string $place_name, Tree $tree)
    {
        // Ignore any empty parts in place names such as "Village, , , Country".
        $place_name  = trim($place_name);
        $this->parts = new Collection(preg_split(Gedcom::PLACE_SEPARATOR_REGEX, $place_name, -1, PREG_SPLIT_NO_EMPTY));

        // Rebuild the placename in the correct format.
        $this->place_name = $this->parts->implode(Gedcom::PLACE_SEPARATOR);

        $this->tree = $tree;
    }

    /**
     * Find a place by its ID.
     *
     * @param int  $id
     * @param Tree $tree
     *
     * @return Place
     */
    public static function find(int $id, Tree $tree): Place
    {
        $parts = new Collection();

        while ($id !== 0) {
            $row = DB::table('places')
                ->where('p_file', '=', $tree->id())
                ->where('p_id', '=', $id)
                ->first();

            if (is_object($row)) {
                $id = (int) $row->p_parent_id;
                $parts->add($row->p_place);
            } else {
                $id = 0;
            }
        }

        $place_name = $parts->implode(Gedcom::PLACE_SEPARATOR);

        return new Place($place_name, $tree);
    }

    /**
     * Get the higher level place.
     *
     * @return Place
     */
    public function parent(): Place
    {
        return new self($this->parts->slice(1)->implode(Gedcom::PLACE_SEPARATOR), $this->tree);
    }

    /**
     * The database row that contains this place.
     * Note that due to database collation, both "Quebec" and "Québec" will share the same row.
     *
     * @return int
     */
    public function id(): int
    {
        return Registry::cache()->array()->remember('place-' . $this->place_name, function (): int {
            // The "top-level" place won't exist in the database.
            if ($this->parts->isEmpty()) {
                return 0;
            }

            $parent_place_id = $this->parent()->id();

            $place_id = (int) DB::table('places')
                ->where('p_file', '=', $this->tree->id())
                ->where('p_place', '=', mb_substr($this->parts->first(), 0, 120))
                ->where('p_parent_id', '=', $parent_place_id)
                ->value('p_id');

            if ($place_id === 0) {
                $place = $this->parts->first();

                DB::table('places')->insert([
                    'p_file'        => $this->tree->id(),
                    'p_place'       => mb_substr($place, 0, 120),
                    'p_parent_id'   => $parent_place_id,
                    'p_std_soundex' => Soundex::russell($place),
                    'p_dm_soundex'  => Soundex::daitchMokotoff($place),
                ]);

                $place_id = DB::lastInsertId();
            }

            return $place_id;
        });
    }

    /**
     * @return Tree
     */
    public function tree(): Tree
    {
        return $this->tree;
    }

    /**
     * Extract the locality (first parts) of a place name.
     *
     * @param int $n
     *
     * @return Collection<int,string>
     */
    public function firstParts(int $n): Collection
    {
        return $this->parts->slice(0, $n);
    }

    /**
     * Extract the country (last parts) of a place name.
     *
     * @param int $n
     *
     * @return Collection<int,string>
     */
    public function lastParts(int $n): Collection
    {
        return $this->parts->slice(-$n);
    }

    /**
     * Get the lower level places.
     *
     * @return array<Place>
     */
    public function getChildPlaces(): array
    {
        if ($this->place_name !== '') {
            $parent_text = Gedcom::PLACE_SEPARATOR . $this->place_name;
        } else {
            $parent_text = '';
        }

        return DB::table('places')
            ->where('p_file', '=', $this->tree->id())
            ->where('p_parent_id', '=', $this->id())
            ->pluck('p_place')
            ->sort(I18N::comparator())
            ->map(fn (string $place): Place => new self($place . $parent_text, $this->tree))
            ->all();
    }

    /**
     * Create a URL to the place-hierarchy page.
     *
     * @return string
     */
    public function url(): string
    {
        //find a module providing the place hierarchy
        $module = Registry::container()->get(ModuleService::class)
            ->findByComponent(ModuleListInterface::class, $this->tree, Auth::user())
            ->first(static fn (ModuleInterface $module): bool => $module instanceof PlaceHierarchyListModule);

        if ($module instanceof PlaceHierarchyListModule) {
            return $module->listUrl($this->tree, [
                'place_id' => $this->id(),
                'tree'     => $this->tree->name(),
            ]);
        }

        // The place-list module is disabled...
        return '#';
    }

    /**
     * Format this place for GEDCOM data.
     *
     * @return string
     */
    public function gedcomName(): string
    {
        return $this->place_name;
    }

    /**
     * Format this place for display on screen.
     */
    public function placeName(): string
    {
        $place_name = $this->parts->first() ?? I18N::translate('unknown');

        return '<bdi>' . e($place_name) . '</bdi>';
    }

    /**
     * Generate the place name for display, including the full hierarchy.
     */
    public function fullName(bool $link = false): string
    {
        if ($this->parts->isEmpty()) {
            return '';
        }

        $full_name = $this->parts->implode(I18N::$list_separator);

        if ($link) {
            $url = $this->url();

            if ($url !== '#') {
                return '<a class="ut" href="' . e($url) . '">' . e($full_name) . '</a>';
            }
        }

        return '<bdi>' . e($full_name) . '</bdi>';
    }

    /**
     * For lists and charts, where the full name won’t fit.
     */
    public function shortName(bool $link = false): string
    {
        $SHOW_PEDIGREE_PLACES = (int) $this->tree->getPreference('SHOW_PEDIGREE_PLACES');

        // Abbreviate the place name, for lists
        if ($this->tree->getPreference('SHOW_PEDIGREE_PLACES_SUFFIX') === '1') {
            $parts = $this->lastParts($SHOW_PEDIGREE_PLACES);
        } else {
            $parts = $this->firstParts($SHOW_PEDIGREE_PLACES);
        }

        $short_name = $parts->implode(I18N::$list_separator);

        if ($link) {
            $url = $this->url();

            if ($url !== '#') {
                $title = strip_tags($this->fullName());

                return '<a class="ut" href="' . e($url) . '" title="' . e($title) . '">' . e($short_name) . '</a>';
            }
        }

        return '<span class="ut">' . e($short_name) . '</span>';
    }
}
