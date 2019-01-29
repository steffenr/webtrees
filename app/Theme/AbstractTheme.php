<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees\Theme;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Module\ModuleMenuInterface;
use Fisharebest\Webtrees\Module\PedigreeChartModule;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;

/**
 * Common functions for all themes.
 */
abstract class AbstractTheme
{
    /**
     * Where are our CSS, JS and other assets?
     */
    protected const THEME_DIR  = '_common';
    public const    ASSET_DIR  = 'themes/' . self::THEME_DIR . '/css-2.0.0/';
    protected const STYLESHEET = self::ASSET_DIR . 'style.css';

    protected const PERSON_BOX_CLASSES = [
        'M' => 'person_box',
        'F' => 'person_boxF',
        'U' => 'person_boxNN',
    ];

    /** @var  Request */
    protected $request;

    /** @var Tree|null */
    protected $tree;

    /**
     * Where are our CSS, JS and other assets?
     *
     * @deprecated - use the constant directly
     * @return string A relative path, such as "themes/foo/"
     */
    public function assetUrl(): string
    {
        return self::ASSET_DIR;
    }

    /**
     * Add markup to the secondary menu.
     *
     * @return string
     */
    public function formatSecondaryMenu(): string
    {
        return
            '<ul class="nav wt-secondary-menu">' .
            implode('', array_map(function (Menu $menu): string {
                return $this->formatSecondaryMenuItem($menu);
            }, $this->secondaryMenu())) .
            '</ul>';
    }

    /**
     * Add markup to an item in the secondary menu.
     *
     * @param Menu $menu
     *
     * @return string
     */
    public function formatSecondaryMenuItem(Menu $menu): string
    {
        return $menu->bootstrap4();
    }

    /**
     * Allow themes to do things after initialization (since they cannot use
     * the constructor).
     *
     * @return void
     */
    public function hookAfterInit()
    {
    }

    /**
     * Display an icon for this fact.
     *
     * @param Fact $fact
     *
     * @return string
     */
    public function icon(Fact $fact): string
    {
        $icon = 'images/facts/' . $fact->getTag() . '.png';
        if (file_exists(self::ASSET_DIR . $icon)) {
            return '<img src="' . self::ASSET_DIR . $icon . '" title="' . GedcomTag::getLabel($fact->getTag()) . '">';
        }

        if (file_exists(self::ASSET_DIR . 'images/facts/NULL.png')) {
            // Spacer image - for alignment - until we move to a sprite.
            return '<img src="' . Theme::theme()->assetUrl() . 'images/facts/NULL.png">';
        }

        return '';
    }

    /**
     * Display an individual in a box - for charts, etc.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function individualBox(Individual $individual): string
    {
        $person_box_class = self::PERSON_BOX_CLASSES[$individual->getSex()];

        if ($individual->canShow() && $individual->tree()->getPreference('SHOW_HIGHLIGHT_IMAGES')) {
            $thumbnail = $individual->displayImage(40, 50, 'crop', []);
        } else {
            $thumbnail = '';
        }

        $content = '<span class="namedef name1">' . $individual->getFullName() . '</span>';
        $icons   = '';
        if ($individual->canShow()) {
            $content = '<a href="' . e($individual->url()) . '">' . $content . '</a>' .
                '<div class="namedef name1">' . $individual->getAddName() . '</div>';
            $icons   = '<div class="icons">' .
                '<span class="iconz icon-zoomin" title="' . I18N::translate('Zoom in/out on this box.') . '"></span>' .
                '<div class="itr"><i class="icon-pedigree"></i><div class="popup">' .
                '<ul class="' . $person_box_class . '">' . implode('', array_map(function (Menu $menu): string {
                    return $menu->bootstrap4();
                }, $this->individualBoxMenu($individual))) . '</ul>' .
                '</div>' .
                '</div>' .
                '</div>';
        }

        return
            '<div data-xref="' . e($individual->xref()) . '" data-tree="' . e($individual->tree()->name()) . '" class="person_box_template ' . $person_box_class . ' box-style1" style="width: ' . $this->parameter('chart-box-x') . 'px; height: ' . $this->parameter('chart-box-y') . 'px">' .
            $icons .
            '<div class="chart_textbox" style="max-height:' . $this->parameter('chart-box-y') . 'px;">' .
            $thumbnail .
            $content .
            '<div class="inout2 details1">' . $this->individualBoxFacts($individual) . '</div>' .
            '</div>' .
            '<div class="inout"></div>' .
            '</div>';
    }

    /**
     * Display an empty box - for a missing individual in a chart.
     *
     * @return string
     */
    public function individualBoxEmpty(): string
    {
        return '<div class="person_box_template person_boxNN box-style1" style="width: ' . $this->parameter('chart-box-x') . 'px; min-height: ' . $this->parameter('chart-box-y') . 'px"></div>';
    }

    /**
     * Display an individual in a box - for charts, etc.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function individualBoxLarge(Individual $individual): string
    {
        $person_box_class = self::PERSON_BOX_CLASSES[$individual->getSex()];

        if ($individual->tree()->getPreference('SHOW_HIGHLIGHT_IMAGES')) {
            $thumbnail = $individual->displayImage(40, 50, 'crop', []);
        } else {
            $thumbnail = '';
        }

        $content = '<span class="namedef name1">' . $individual->getFullName() . '</span>';
        $icons   = '';
        if ($individual->canShow()) {
            $content = '<a href="' . e($individual->url()) . '">' . $content . '</a>' .
                '<div class="namedef name2">' . $individual->getAddName() . '</div>';
            $icons   = '<div class="icons">' .
                '<span class="iconz icon-zoomin" title="' . I18N::translate('Zoom in/out on this box.') . '"></span>' .
                '<div class="itr"><i class="icon-pedigree"></i><div class="popup">' .
                '<ul class="' . $person_box_class . '">' . implode('', array_map(function (Menu $menu): string {
                    return $menu->bootstrap4();
                }, $this->individualBoxMenu($individual))) . '</ul>' .
                '</div>' .
                '</div>' .
                '</div>';
        }

        return
            '<div data-xref="' . e($individual->xref()) . '" data-tree="' . e($individual->tree()->name()) . '" class="person_box_template ' . $person_box_class . ' box-style2">' .
            $icons .
            '<div class="chart_textbox" style="max-height:' . $this->parameter('chart-box-y') . 'px;">' .
            $thumbnail .
            $content .
            '<div class="inout2 details2">' . $this->individualBoxFacts($individual) . '</div>' .
            '</div>' .
            '<div class="inout"></div>' .
            '</div>';
    }

    /**
     * Display an individual in a box - for charts, etc.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function individualBoxSmall(Individual $individual): string
    {
        $person_box_class = self::PERSON_BOX_CLASSES[$individual->getSex()];

        if ($individual->tree()->getPreference('SHOW_HIGHLIGHT_IMAGES')) {
            $thumbnail = $individual->displayImage(40, 50, 'crop', []);
        } else {
            $thumbnail = '';
        }

        return
            '<div data-xref="' . $individual->xref() . '" class="person_box_template ' . $person_box_class . ' iconz box-style0" style="width: ' . $this->parameter('compact-chart-box-x') . 'px; min-height: ' . $this->parameter('compact-chart-box-y') . 'px">' .
            '<div class="compact_view">' .
            $thumbnail .
            '<a href="' . e($individual->url()) . '">' .
            '<span class="namedef name0">' . $individual->getFullName() . '</span>' .
            '</a>' .
            '<div class="inout2 details0">' . $individual->getLifeSpan() . '</div>' .
            '</div>' .
            '<div class="inout"></div>' .
            '</div>';
    }

    /**
     * Display an individual in a box - for charts, etc.
     *
     * @return string
     */
    public function individualBoxSmallEmpty(): string
    {
        return '<div class="person_box_template person_boxNN box-style1" style="width: ' . $this->parameter('compact-chart-box-x') . 'px; min-height: ' . $this->parameter('compact-chart-box-y') . 'px"></div>';
    }

    /**
     * Generate the facts, for display in charts.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function individualBoxFacts(Individual $individual): string
    {
        $html = '';

        $opt_tags = preg_split('/\W/', $individual->tree()->getPreference('CHART_BOX_TAGS'), 0, PREG_SPLIT_NO_EMPTY);
        // Show BIRT or equivalent event
        foreach (Gedcom::BIRTH_EVENTS as $birttag) {
            if (!in_array($birttag, $opt_tags)) {
                $event = $individual->getFirstFact($birttag);
                if ($event) {
                    $html .= $event->summary();
                    break;
                }
            }
        }
        // Show optional events (before death)
        foreach ($opt_tags as $key => $tag) {
            if (!in_array($tag, Gedcom::DEATH_EVENTS)) {
                $event = $individual->getFirstFact($tag);
                if ($event !== null) {
                    $html .= $event->summary();
                    unset($opt_tags[$key]);
                }
            }
        }
        // Show DEAT or equivalent event
        foreach (Gedcom::DEATH_EVENTS as $deattag) {
            $event = $individual->getFirstFact($deattag);
            if ($event) {
                $html .= $event->summary();
                if (in_array($deattag, $opt_tags)) {
                    unset($opt_tags[array_search($deattag, $opt_tags)]);
                }
                break;
            }
        }
        // Show remaining optional events (after death)
        foreach ($opt_tags as $tag) {
            $event = $individual->getFirstFact($tag);
            if ($event) {
                $html .= $event->summary();
            }
        }

        return $html;
    }

    /**
     * Links, to show in chart boxes;
     *
     * @param Individual $individual
     *
     * @return Menu[]
     */
    public function individualBoxMenu(Individual $individual): array
    {
        $menus = array_merge(
            $this->individualBoxMenuCharts($individual),
            $this->individualBoxMenuFamilyLinks($individual)
        );

        return $menus;
    }

    /**
     * Chart links, to show in chart boxes;
     *
     * @param Individual $individual
     *
     * @return Menu[]
     */
    public function individualBoxMenuCharts(Individual $individual): array
    {
        $menus = [];
        foreach (Module::findByComponent('chart', $this->tree, Auth::user()) as $chart) {
            $menu = $chart->chartBoxMenu($individual);
            if ($menu) {
                $menus[] = $menu;
            }
        }

        usort($menus, function (Menu $x, Menu $y) {
            return I18N::strcasecmp($x->getLabel(), $y->getLabel());
        });

        return $menus;
    }

    /**
     * Family links, to show in chart boxes.
     *
     * @param Individual $individual
     *
     * @return Menu[]
     */
    public function individualBoxMenuFamilyLinks(Individual $individual): array
    {
        $menus = [];

        foreach ($individual->getSpouseFamilies() as $family) {
            $menus[] = new Menu('<strong>' . I18N::translate('Family with spouse') . '</strong>', $family->url());
            $spouse  = $family->getSpouse($individual);
            if ($spouse && $spouse->canShowName()) {
                $menus[] = new Menu($spouse->getFullName(), $spouse->url());
            }
            foreach ($family->getChildren() as $child) {
                if ($child->canShowName()) {
                    $menus[] = new Menu($child->getFullName(), $child->url());
                }
            }
        }

        return $menus;
    }

    /**
     * Initialise the theme. We cannot pass these in a constructor, as the construction
     * happens in a theme file, and we need to be able to change it.
     *
     * @param Request   $request
     * @param Tree|null $tree The current tree (if there is one).
     *
     * @return void
     */
    final public function init(Request $request, Tree $tree = null)
    {
        $this->request = $request;
        $this->tree    = $tree;

        $this->hookAfterInit();
    }

    /**
     * Generate a menu item to change the blocks on the current (index.php) page.
     *
     * @return Menu|null
     */
    public function menuChangeBlocks()
    {
        if (Auth::check() && $this->request->get('route') === 'user-page') {
            return new Menu(I18N::translate('Customize this page'), route('user-page-edit', ['ged' => $this->tree->name()]), 'menu-change-blocks');
        }

        if (Auth::isManager($this->tree) && $this->request->get('route') === 'tree-page') {
            return new Menu(I18N::translate('Customize this page'), route('tree-page-edit', ['ged' => $this->tree->name()]), 'menu-change-blocks');
        }

        return null;
    }

    /**
     * Generate a menu item for the control panel.
     *
     * @return Menu|null
     */
    public function menuControlPanel()
    {
        if (Auth::isAdmin()) {
            return new Menu(I18N::translate('Control panel'), route('admin-control-panel'), 'menu-admin');
        }

        if (Auth::isManager($this->tree)) {
            return new Menu(I18N::translate('Control panel'), route('admin-control-panel-manager'), 'menu-admin');
        }

        return null;
    }

    /**
     * A menu to show a list of available languages.
     *
     * @return Menu|null
     */
    public function menuLanguages()
    {
        $menu = new Menu(I18N::translate('Language'), '#', 'menu-language');

        foreach (I18N::activeLocales() as $locale) {
            $language_tag = $locale->languageTag();
            $class        = 'menu-language-' . $language_tag . (WT_LOCALE === $language_tag ? ' active' : '');
            $menu->addSubmenu(new Menu($locale->endonym(), '#', $class, [
                'onclick'       => 'return false;',
                'data-language' => $language_tag,
            ]));
        }

        if (count($menu->getSubmenus()) > 1) {
            return $menu;
        }

        return null;
    }

    /**
     * A login menu option (or null if we are already logged in).
     *
     * @return Menu|null
     */
    public function menuLogin()
    {
        if (Auth::check()) {
            return null;
        }

        // Return to this page after login...
        $url = $this->request->getRequestUri();

        // ...but switch from the tree-page to the user-page
        $url = str_replace('route=tree-page', 'route=user-page', $url);

        return new Menu(I18N::translate('Sign in'), route('login', ['url' => $url]), 'menu-login', ['rel' => 'nofollow']);
    }

    /**
     * A logout menu option (or null if we are already logged out).
     *
     * @return Menu|null
     */
    public function menuLogout()
    {
        if (Auth::check()) {
            return new Menu(I18N::translate('Sign out'), route('logout'), 'menu-logout');
        }

        return null;
    }

    /**
     * A link to allow users to edit their account settings.
     *
     * @return Menu|null
     */
    public function menuMyAccount()
    {
        if (Auth::check()) {
            return new Menu(I18N::translate('My account'), route('my-account'));
        }

        return null;
    }

    /**
     * A link to the user's individual record (individual.php).
     *
     * @return Menu|null
     */
    public function menuMyIndividualRecord()
    {
        $record = Individual::getInstance($this->tree->getUserPreference(Auth::user(), 'gedcomid'), $this->tree);

        if ($record) {
            return new Menu(I18N::translate('My individual record'), $record->url(), 'menu-myrecord');
        }

        return null;
    }

    /**
     * A link to the user's personal home page.
     *
     * @return Menu
     */
    public function menuMyPage(): Menu
    {
        return new Menu(I18N::translate('My page'), route('user-page', ['ged' => $this->tree->name()]), 'menu-mypage');
    }

    /**
     * A menu for the user's personal pages.
     *
     * @return Menu|null
     */
    public function menuMyPages()
    {
        if (Auth::id() && $this->tree !== null) {
            return new Menu(I18N::translate('My pages'), '#', 'menu-mymenu', [], array_filter([
                $this->menuMyPage(),
                $this->menuMyIndividualRecord(),
                $this->menuMyPedigree(),
                $this->menuMyAccount(),
                $this->menuControlPanel(),
                $this->menuChangeBlocks(),
            ]));
        }

        return null;
    }

    /**
     * A link to the user's individual record.
     *
     * @return Menu|null
     */
    public function menuMyPedigree()
    {
        $gedcomid = $this->tree->getUserPreference(Auth::user(), 'gedcomid');

        $pedigree_chart = Module::findByComponent('chart', $this->tree, Auth::user())
            ->filter(function (ModuleInterface $module): bool {
                return $module instanceof PedigreeChartModule;
            });

        if ($gedcomid !== '' && $pedigree_chart instanceof PedigreeChartModule) {
            return new Menu(
                I18N::translate('My pedigree'),
                route('pedigree', [
                    'xref' => $gedcomid,
                    'ged'  => $this->tree->name(),
                ]),
                'menu-mypedigree'
            );
        }

        return null;
    }

    /**
     * Create a pending changes menu.
     *
     * @return Menu|null
     */
    public function menuPendingChanges()
    {
        if ($this->pendingChangesExist()) {
            $url = route('show-pending', [
                'ged' => $this->tree ? $this->tree->name() : '',
                'url' => $this->request->getRequestUri(),
            ]);

            return new Menu(I18N::translate('Pending changes'), $url, 'menu-pending');
        }

        return null;
    }

    /**
     * Themes menu.
     *
     * @return Menu|null
     */
    public function menuThemes()
    {
        if ($this->tree !== null && Site::getPreference('ALLOW_USER_THEMES') === '1' && $this->tree->getPreference('ALLOW_THEME_DROPDOWN') === '1') {
            $submenus = [];
            foreach (Theme::installedThemes() as $theme) {
                $class      = 'menu-theme-' . $theme->themeId() . ($theme === $this ? ' active' : '');
                $submenus[] = new Menu($theme->themeName(), '#', $class, [
                    'onclick'    => 'return false;',
                    'data-theme' => $theme->themeId(),
                ]);
            }

            usort($submenus, function (Menu $x, Menu $y): int {
                return I18N::strcasecmp($x->getLabel(), $y->getLabel());
            });

            $menu = new Menu(I18N::translate('Theme'), '#', 'menu-theme', [], $submenus);

            return $menu;
        }

        return null;
    }

    /**
     * Misecellaneous dimensions, fonts, styles, etc.
     *
     * @param string $parameter_name
     *
     * @return string|int|float
     */
    public function parameter($parameter_name)
    {
        $parameters = [
            'chart-background-f'             => 'dddddd',
            'chart-background-m'             => 'cccccc',
            'chart-background-u'             => 'eeeeee',
            'chart-box-x'                    => 250,
            'chart-box-y'                    => 80,
            'chart-font-color'               => '000000',
            'chart-spacing-x'                => 5,
            'chart-spacing-y'                => 10,
            'compact-chart-box-x'            => 240,
            'compact-chart-box-y'            => 50,
            'distribution-chart-high-values' => '555555',
            'distribution-chart-low-values'  => 'cccccc',
            'distribution-chart-no-values'   => 'ffffff',
            'distribution-chart-x'           => 440,
            'distribution-chart-y'           => 220,
            'line-width'                     => 1.5,
            'shadow-blur'                    => 0,
            'shadow-color'                   => '',
            'shadow-offset-x'                => 0,
            'shadow-offset-y'                => 0,
            'stats-small-chart-x'            => 440,
            'stats-small-chart-y'            => 125,
            'stats-large-chart-x'            => 900,
            'image-dline'                    => static::ASSET_DIR . 'images/dline.png',
            'image-dline2'                   => static::ASSET_DIR . 'images/dline2.png',
            'image-hline'                    => static::ASSET_DIR . 'images/hline.png',
            'image-spacer'                   => static::ASSET_DIR . 'images/spacer.png',
            'image-vline'                    => static::ASSET_DIR . 'images/vline.png',
            'image-minus'                    => static::ASSET_DIR . 'images/minus.png',
            'image-plus'                     => static::ASSET_DIR . 'images/plus.png',
        ];

        if (array_key_exists($parameter_name, $parameters)) {
            return $parameters[$parameter_name];
        }

        throw new \InvalidArgumentException($parameter_name);
    }

    /**
     * Are there any pending changes for us to approve?
     *
     * @return bool
     */
    public function pendingChangesExist(): bool
    {
        return $this->tree && $this->tree->hasPendingEdit() && Auth::isModerator($this->tree);
    }

    /**
     * Create a pending changes link. Some themes prefer an alert/banner to a menu.
     *
     * @return string
     */
    public function pendingChangesLink(): string
    {
        return '<a href="' . e(route('show-pending', ['ged' => $this->tree->name()])) . '">' . $this->pendingChangesLinkText() . '</a>';
    }

    /**
     * Text to use in the pending changes link.
     *
     * @return string
     */
    public function pendingChangesLinkText(): string
    {
        return I18N::translate('There are pending changes for you to moderate.');
    }

    /**
     * Generate a list of items for the main menu.
     *
     * @return Menu[]
     */
    public function primaryMenu(): array
    {
        return Module::findByComponent('menu', $this->tree, Auth::user())
            ->map(function (ModuleMenuInterface $menu): ?Menu {
                return $menu->getMenu($this->tree);
            })
            ->filter()
            ->all();
    }

    /**
     * Create the primary menu.
     *
     * @param Menu[] $menus
     *
     * @return string
     */
    public function primaryMenuContent(array $menus): string
    {
        return implode('', array_map(function (Menu $menu): string {
            return $menu->bootstrap4();
        }, $menus));
    }

    /**
     * Generate a list of items for the user menu.
     *
     * @return Menu[]
     */
    public function secondaryMenu(): array
    {
        return array_filter([
            $this->menuPendingChanges(),
            $this->menuMyPages(),
            $this->menuThemes(),
            $this->menuLanguages(),
            $this->menuLogin(),
            $this->menuLogout(),
        ]);
    }

    /**
     * A list of CSS files to include for this page.
     *
     * @return string[]
     */
    public function stylesheets(): array
    {
        return [
            self::STYLESHEET,
        ];
    }

    /**
     * A fixed string to identify this theme, in settings, etc.
     *
     * @return string
     */
    public function themeId(): string
    {
        return static::THEME_DIR;
    }

    /**
     * What is this theme called?
     *
     * @return string
     */
    abstract public function themeName(): string;
}
