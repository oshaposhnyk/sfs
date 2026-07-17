<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Ukrainian strings for local_sfsresources.
 *
 * @package    local_sfsresources
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Ресурси та стандарти';
$string['resources'] = 'Ресурси та стандарти';
$string['kicker'] = 'Центр урядування';
$string['filter:all'] = 'Всі';
$string['lede'] = 'Усі протоколи, договори та довідкові документи в одному місці — з пошуком, тегами за ролями й готові до поширення серед персоналу шкіл і постачальників.';
$string['library'] = 'Бібліотека ресурсів';
$string['open'] = 'Відкрити';
$string['nodocuments'] = 'Документи ще не опубліковано.';
$string['documents'] = 'Документи (JSON)';
$string['documents_desc'] = 'JSON-список, напр. [{"title": "HACCP Protocol", "sub": "Cold chain", "audience": "Director", "kind": "pdf", "updated": "2 дні тому", "url": "https://…"}]. "kind" — pdf, doc, xls, zip або link. Порожнє поле показує лише файли, завантажені до бібліотеки.';
$string['privacy:metadata'] = 'Плагін «Ресурси та стандарти» лише відображає лінки, підібрані адміністратором.';
$string['tool_plans'] = 'Навчальні плани';
$string['tool_plans_desc'] = 'Створюйте плани, упорядковуйте курси, зараховуйте учнів.';
$string['tool_cohorts'] = 'Когорти';
$string['tool_cohorts_desc'] = 'Керуйте когортами, пов’язаними з навчальними планами.';
$string['tool_badges'] = 'Бейджі';
$string['tool_badges_desc'] = 'Налаштуйте досягнення, що показуються у Future Food.';
$string['tool_courses'] = 'Курси';
$string['tool_courses_desc'] = 'Керуйте структурою курсів і категоріями.';
$string['tool_count_active'] = 'Активні';
$string['tool_count_configured'] = 'Налаштовано';
$string['stats'] = 'KPI-картки (JSON)';
$string['stats_desc'] = 'JSON-список, напр. [{"label": "Персонал навчено", "value": "145", "suffix": "", "percent": 72, "sub": "Завершені курси L4C", "variant": "teal"}]. "variant" — teal, amber або deep. Порожнє поле приховує KPI-картки.';
$string['documentfiles'] = 'Файли бібліотеки';
$string['documentfiles_desc'] = 'Завантажте сюди самі документи. Вони з’являться вгорі бібліотеки; завантаження доступне лише авторизованим користувачам.';
$string['settings:pageheading'] = 'Сторінка ресурсів';
$string['settings:pageheading_desc'] = 'No-code керування сторінкою «Ресурси та стандарти» для користувачів.';
$string['settings:showresourcesheader'] = 'Показувати заголовок сторінки';
$string['settings:showresourcesheader_desc'] = 'Відображати надзаголовок, заголовок і вступний текст над сторінкою ресурсів.';
$string['settings:showresourcesstats'] = 'Показувати KPI-картки';
$string['settings:showresourcesstats_desc'] = 'Відображати KPI-картки, налаштовані адміністратором через JSON.';
$string['settings:showresourcestools'] = 'Показувати інструменти керування';
$string['settings:showresourcestools_desc'] = 'Відображати службові ярлики до навчальних планів, когорт, бейджів і курсів для персоналу.';
$string['settings:showresourcesfilters'] = 'Показувати фільтри аудиторій';
$string['settings:showresourcesfilters_desc'] = 'Відображати фільтри над бібліотекою документів.';
$string['settings:showresourceslibrary'] = 'Показувати бібліотеку ресурсів';
$string['settings:showresourceslibrary_desc'] = 'Відображати картку бібліотеки документів.';
$string['settings:resourceskicker'] = 'Ресурси — надзаголовок';
$string['settings:resourcestitle'] = 'Ресурси — заголовок';
$string['settings:resourceslede'] = 'Ресурси — вступний текст';
$string['settings:resourceslibrarytitle'] = 'Заголовок бібліотеки';
$string['settings:resourcesempty'] = 'Повідомлення без документів';
$string['settings:fallback_desc'] = 'Порожнє поле використовує типовий текст сторінки ресурсів.';
