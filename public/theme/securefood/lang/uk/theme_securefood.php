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
 * Ukrainian language strings for theme_securefood.
 *
 * @package    theme_securefood
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'SecureFood School';
$string['choosereadme'] = 'SecureFood School — дочірня тема Boost, що реалізує дизайн-систему SecureFood із перемикачем між стандартним інтерфейсом Moodle і кастомним режимом SecureFood.';
$string['configtitle'] = 'Налаштування SecureFood School';
$string['region-side-pre'] = 'Праворуч';

// Settings tabs.
$string['generaltab'] = 'Загальні';
$string['navigationtab'] = 'Навігація';
$string['colourstab'] = 'Кольори';
$string['advancedtab'] = 'Додатково';

// General tab.
$string['forcemode'] = 'Примусовий режим інтерфейсу';
$string['forcemode_desc'] = 'Примусово увімкнути один режим для всіх користувачів або дозволити кожному обирати між стандартним інтерфейсом Moodle і режимом SecureFood.';
$string['forcemode_userchoice'] = 'Вибір користувача';
$string['defaultmode'] = 'Типовий режим інтерфейсу';
$string['defaultmode_desc'] = 'Режим, який отримують користувачі, доки не зроблять власний вибір.';
$string['mode_standard'] = 'Стандартний Moodle';
$string['mode_securefood'] = 'Режим SecureFood';
$string['switchmode'] = 'Перемкнути режим інтерфейсу';
$string['switchtostandard'] = 'Перемкнутися на стандартний інтерфейс Moodle';
$string['switchtosecurefood'] = 'Перемкнутися на інтерфейс SecureFood';

// Navigation tab.
$string['navigation'] = 'Бічна навігація (JSON)';
$string['navigation_desc'] = 'Власне бічне меню у форматі JSON-масиву секцій. Порожнє поле — типове меню. Формат: <pre>[{"label": "Навчання", "items": [{"title": "Студентська лабораторія", "url": "/local/learningplans/my.php", "icon": "school", "visibility": "loggedin"}]}]</pre> "icon" — назва піктограми Material Icons; "visibility" — "all" або "loggedin". Некоректний JSON повертає типове меню.';

// Shell.
$string['sidebarnav'] = 'Основна навігація';
$string['skiptocontent'] = 'Перейти до основного вмісту';
$string['togglesidebar'] = 'Згорнути/розгорнути бічну панель';
$string['togglescheme'] = 'Перемкнути світлу/темну схему';
$string['searchplaceholder'] = 'Пошук курсів, документів…';

// Default navigation items.
$string['nav_learning'] = 'Навчання';
$string['nav_account'] = 'Обліковий запис';
$string['nav_about'] = 'Про проєкт';
$string['nav_studentlab'] = 'Студентська лабораторія';
$string['nav_mycourses'] = 'Мої курси';
$string['nav_notifications'] = 'Сповіщення';
$string['nav_messages'] = 'Повідомлення';
$string['nav_settings'] = 'Налаштування';

// Colours tab.
$string['colourintro'] = 'Кольори дизайн-токенів';
$string['colourintro_desc'] = 'Кожен колір інтерфейсу SecureFood походить із дизайн-токена. Залиште поле порожнім, щоб використати типове значення SecureFood. Перевизначення діють у режимі SecureFood в обох колірних схемах — світлій і темній. Змінюючи кольори, зберігайте контраст пар «текст/тло» щонайменше 4.5:1 (WCAG 2.1 AA).';
$string['colourlight'] = '{$a} — світла схема';
$string['colourdark'] = '{$a} — темна схема';
$string['colour_desc'] = 'CSS-властивість <code>{$a}</code>. Порожнє поле — типове значення SecureFood.';
$string['colourdarkrgba_desc'] = 'CSS-властивість <code>{$a}</code> (темна схема). Приймає будь-який CSS-колір, зокрема <code>rgba()</code> для прозорості. Порожнє поле — типове значення SecureFood.';

// Token names.
$string['token_bg'] = 'Тло сторінки';
$string['token_bg2'] = 'Заглиблене тло';
$string['token_surface'] = 'Поверхня';
$string['token_surface2'] = 'Поверхня (піднята)';
$string['token_ink'] = 'Заголовки та основний текст';
$string['token_ink2'] = 'Текст';
$string['token_muted'] = 'Другорядний текст';
$string['token_muted2'] = 'Текст-заповнювач';
$string['token_line'] = 'Межі';
$string['token_linestrong'] = 'Межі (виразні)';
$string['token_primary'] = 'Основний (бірюзовий)';
$string['token_primary700'] = 'Основний (наведення)';
$string['token_primary50'] = 'Основний (відтінок)';
$string['token_accent'] = 'Акцент (бурштиновий)';
$string['token_accent50'] = 'Акцент (відтінок)';
$string['token_teal'] = 'Бірюзовий';
$string['token_teal50'] = 'Бірюзовий (відтінок)';
$string['token_success'] = 'Успіх';
$string['token_warn'] = 'Попередження';
$string['token_danger'] = 'Небезпека';

// Advanced tab.
$string['rawscsspre'] = 'Початковий SCSS-код';
$string['rawscsspre_desc'] = 'SCSS-код, що додається перед усім іншим. Використовуйте його для визначення змінних, доступних решті компіляції.';
$string['rawscss'] = 'SCSS-код';
$string['rawscss_desc'] = 'SCSS-код, що додається в кінці таблиці стилів. Він має пріоритет над усіма стилями теми, включно з перевизначеннями токенів.';

// Privacy.
$string['privacy:metadata:preference:colourscheme'] = 'Колірна схема, якій надає перевагу користувач: світла, темна або згідно з операційною системою.';
$string['privacy:metadata:preference:sidebar'] = 'Чи тримає користувач бічну панель SecureFood розгорнутою або згорнутою.';
$string['privacy:metadata:preference:uimode'] = 'Чи використовує користувач стандартний інтерфейс Moodle або режим SecureFood.';

// Pages & content tab.
$string['pagestab'] = 'Сторінки та вміст';
$string['aboutkicker'] = 'Сторінка About — надзаголовок';
$string['abouttitle'] = 'Сторінка About — заголовок';
$string['aboutlede'] = 'Сторінка About — вступний текст';
$string['aboutstats'] = 'Сторінка About — статистика hero (JSON)';
$string['aboutkpis'] = 'Сторінка About — ряд KPI (JSON)';
$string['aboutfallback_desc'] = 'Залиште порожнім, щоб використати текст із дизайну SecureFood.';
$string['aboutjson_desc'] = 'JSON-список, напр. [{"value": "14", "label": "Living Labs", "sub": "Примітка"}]. Порожнє поле — типові значення дизайну; некоректний JSON повертає їх.';
$string['aboutfeedtitle'] = 'Сторінка About — заголовок стрічки';
$string['aboutfeed'] = 'Сторінка About — стрічка оновлень (JSON)';
$string['aboutfeed_desc'] = 'JSON-список, напр. [{"chip": "Digital twin", "title": "…", "text": "…", "time": "2 дні тому"}]. Порожнє поле — типові значення дизайну.';
$string['abouthubstitle'] = 'Сторінка About — заголовок блоку хабів';
$string['abouthubs'] = 'Сторінка About — Living Labs та партнери (JSON)';
$string['abouthubs_desc'] = 'JSON-список, напр. [{"name": "Kyiv Lab", "country": "Ukraine", "type": "lab"}]. "type" — "lab" або "partner". Порожнє поле — типові значення дизайну.';
$string['abouthub_lab'] = 'Living Lab';
$string['abouthub_partner'] = 'Партнер';
