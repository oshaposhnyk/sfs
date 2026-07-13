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
 * Ukrainian strings for local_sfsgame.
 *
 * @package    local_sfsgame
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Future Food';
$string['futurefood'] = 'Future Food';
$string['kicker'] = 'Адаптивні місії';
$string['lede'] = 'Короткі сценарні місії, що перетворюють теорію на практичні дії з харчової безпеки. Заробляйте XP, відкривайте бейджі та впливайте на меню майбутнього.';
$string['level'] = 'Рівень';
$string['xp'] = 'XP';
$string['xptonext'] = '{$a} XP до наступного рівня';
$string['achievements'] = 'Досягнення';
$string['noachievements'] = 'Бейджі ще не налаштовані. Досягнення з’являться тут після налаштування та отримання бейджів.';
$string['earned'] = 'Отримано';
$string['locked'] = 'Заблоковано';
$string['criteria_preview'] = 'Попередній перегляд критеріїв';
$string['privacy:metadata'] = 'Плагін Future Food лише відображає дані бейджів і завершень, що зберігаються в інших компонентах.';
$string['missions'] = 'Місії (JSON)';
$string['missions_desc'] = 'JSON-список, напр. [{"badge": "Featured", "tags": ["Data"], "title": "…", "text": "…", "duration": "11 хв", "xp": 40, "url": "/course/view.php?id=2"}]. Порожнє поле означає, що місії не показуються.';
$string['missionstitle'] = 'Активні місії';
$string['nomissions'] = 'Місії ще не опубліковано.';
$string['startmission'] = 'Почати';
$string['currentrank'] = 'Поточний ранг';
$string['totalxp'] = 'Усього XP';
$string['progresstolevel'] = 'Прогрес до рівня {$a->next} · {$a->current} / {$a->target} XP';
$string['badgecount'] = 'Бейджів: {$a}';
$string['startdailymission'] = 'Почати щоденну місію';
$string['showdecision'] = 'Показувати точку рішення';
$string['showdecision_desc'] = 'Показувати на сторінці Future Food блок сценарного вибору.';
$string['decisionfallback_desc'] = 'Порожнє поле — текст із дизайну.';
$string['decisionkicker'] = 'Надзаголовок точки рішення';
$string['decisiontitle'] = 'Заголовок точки рішення';
$string['decisionbody'] = 'Опис точки рішення';
$string['decisionbody_desc'] = 'Короткий сценарій над посиланнями на активність.';
$string['decisionchoices'] = 'Варіанти рішення (JSON)';
$string['decisionchoices_desc'] = 'JSON-список реальних посилань на активності, напр. [{"label":"Reject & report","icon":"verified_user","note":"Open the real Moodle quiz or choice activity.","url":"/mod/quiz/view.php?id=10"}]. Порожнє поле показує empty state.';
$string['decisionempty'] = 'Реальну активність для рішення ще не налаштовано.';
$string['decisionhint'] = 'Кожне посилання відкриває реальну Moodle-активність. Зворотний зв’язок лишається всередині неї.';
$string['decisionpill'] = 'Жива активність';
$string['default_decisionkicker'] = 'Маршрут сценарію';
$string['default_decisiontitle'] = 'Точка рішення';
$string['default_decisionbody'] = 'Прив’яжіть кожен варіант сценарію до реальної Moodle-активності типу quiz або choice. Тема лише відображає маршрут; оцінювання та зворотний зв’язок залишаються в Moodle.';
$string['herokicker'] = 'Hero — надзаголовок';
$string['herotitle'] = 'Hero — заголовок';
$string['herotext'] = 'Hero — текст';
$string['herofallback_desc'] = 'Порожнє поле — текст із дизайну.';
$string['default_herokicker'] = 'Режим агента активний';
$string['default_herotitle'] = 'Місія: нульовий голод';
$string['default_herotext'] = 'Збирайте докази, проходьте симуляції рішень і заробляйте XP для наступного рангу агента. Кожна ваша дія впливає на реальний протокол, який використовують у Living Labs.';
