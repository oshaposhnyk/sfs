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
$string['region-content-top'] = 'Верх вмісту';
$string['region-content-bottom'] = 'Низ вмісту';

// Settings tabs.
$string['generaltab'] = 'Загальні';
$string['brandtab'] = 'Бренд';
$string['navigationtab'] = 'Навігація';
$string['blockstab'] = 'Блоки';
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
$string['moreactions'] = 'Ще';
$string['switchmode'] = 'Перемкнути режим інтерфейсу';
$string['switchtostandard'] = 'Перемкнутися на стандартний інтерфейс Moodle';
$string['switchtosecurefood'] = 'Перемкнутися на інтерфейс SecureFood';

// Brand tab.
$string['brandname'] = 'Назва сайту в інтерфейсі';
$string['brandname_desc'] = 'Назва, що відображається в оболонці SecureFood. Порожнє поле — коротка назва сайту Moodle.';
$string['brandlogoheading'] = 'Логотипи';
$string['brandlogoheading_desc'] = 'Завантажте власні логотипи оболонки SecureFood. Порожнє поле використовує вбудований дизайн-ресурс. Завантажені логотипи приймають лише PNG/JPG; вбудовані SVG за замовчуванням залишаються доступними.';
$string['logofulllight'] = 'Повний логотип — світла схема';
$string['logofulllight_desc'] = 'Повний логотип для бічної панелі у світлій колірній схемі. Рекомендовано: прозорий PNG.';
$string['logofulldark'] = 'Повний логотип — темна схема';
$string['logofulldark_desc'] = 'Повний логотип для бічної панелі у темній колірній схемі. Рекомендовано: прозорий PNG.';
$string['logoiconlight'] = 'Іконка логотипа — світла схема';
$string['logoiconlight_desc'] = 'Компактний логотип для згорнутої бічної панелі у світлій колірній схемі.';
$string['logoicondark'] = 'Іконка логотипа — темна схема';
$string['logoicondark_desc'] = 'Компактний логотип для згорнутої бічної панелі у темній колірній схемі.';
$string['favicon'] = 'Favicon';
$string['favicon_desc'] = 'Необов’язкова іконка браузера для теми SecureFood. Приймає ICO або PNG. Порожнє поле використовує стандартний Moodle/core favicon.';

// Navigation tab.
$string['navigation'] = 'Бічна навігація (JSON)';
$string['navigation_desc'] = 'Власне бічне меню у форматі JSON-масиву секцій. Порожнє поле — типове меню. Формат: <pre>[{"label": "Навчання", "items": [{"title": "Студентська лабораторія", "url": "/local/learningplans/my.php", "icon": "school", "visibility": "loggedin"}, {"title": "Адмін", "url": "/admin/search.php", "icon": "admin_panel_settings", "visibility": "capability", "capability": "moodle/site:config"}]}]</pre> "icon" — назва піктограми Material Icons; "visibility" — "all", "loggedin" або "capability". Некоректний JSON у runtime повертає типове меню, але не може бути збережений у формі налаштувань.';
$string['navigationjson_invalidjson'] = 'JSON навігації некоректний: {$a}';
$string['navigationjson_rootinvalid'] = 'JSON навігації має бути масивом секцій.';
$string['navigationjson_sectioninvalid'] = 'Секція навігації {$a} має бути об’єктом з масивом items.';
$string['navigationjson_iteminvalid'] = 'Елемент навігації {$a->item} у секції {$a->section} має містити title і url.';
$string['navigationjson_visibilityinvalid'] = 'Елемент навігації {$a->item} у секції {$a->section} має непідтримуване значення visibility. Використовуйте all, loggedin або capability.';
$string['navigationjson_capabilityinvalid'] = 'Елемент навігації {$a->item} у секції {$a->section} використовує visibility=capability, але не має валідного Moodle capability.';
$string['navigationjson_noitems'] = 'JSON навігації має містити принаймні один придатний елемент.';

// Blocks tab.
$string['showblockcontenttop'] = 'Показувати регіон блоків над вмістом';
$string['showblockcontenttop_desc'] = 'Дозволити блокам у регіоні content-top відображатися над основним вмістом сторінки в режимі SecureFood.';
$string['showblockside'] = 'Показувати правий регіон блоків';
$string['showblockside_desc'] = 'Дозволити блокам у регіоні side-pre відображатися як права колонка в режимі SecureFood.';
$string['showblockcontentbottom'] = 'Показувати регіон блоків під вмістом';
$string['showblockcontentbottom_desc'] = 'Дозволити блокам у регіоні content-bottom відображатися під основним вмістом сторінки в режимі SecureFood.';

// Shell.
$string['sidebarnav'] = 'Основна навігація';
$string['skiptocontent'] = 'Перейти до основного вмісту';
$string['togglesidebar'] = 'Згорнути/розгорнути бічну панель';
$string['togglescheme'] = 'Перемкнути колірну схему';
$string['searchplaceholder'] = 'Пошук курсів, документів…';

// Login page.
$string['loginpanel_kicker'] = 'SecureFood School';
$string['loginpanel_title'] = 'Щит для продовольчих систем майбутнього';
$string['loginpanel_lede'] = 'Отримуйте доступ до навчальних маршрутів, місій Future Food і практичних ресурсів для безпечних та стійких продовольчих систем.';
$string['loginpanel_chipslabel'] = 'Акценти навчання SecureFood';
$string['loginpanel_chip_skills'] = 'Прикладні навички';
$string['loginpanel_chip_labs'] = 'Living Labs';
$string['loginpanel_chip_network'] = 'Мережа ЄС';

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
$string['customfooter'] = 'Власний футер';
$string['footerhtml'] = 'HTML футера';
$string['footerhtml_desc'] = 'Необов’язковий вміст футера, що відображається в оболонці SecureFood над стандартним футером Moodle. Вміст проходить через текстові фільтри Moodle.';
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
$string['showaboutpage'] = 'Показувати вміст сторінки About';
$string['showaboutpage_desc'] = 'Відображати вміст SecureFood About/front-page перед стандартним вмістом головної сторінки Moodle.';
$string['showaboutstats'] = 'Показувати статистику hero на About';
$string['showaboutstats_desc'] = 'Відображати ряд статистики під hero-блоком сторінки About.';
$string['showaboutlayers'] = 'Показувати shield і layers на About';
$string['showaboutlayers_desc'] = 'Відображати картку shield і секцію reinforcing layers на сторінці About.';
$string['showabouthubs'] = 'Показувати мапу хабів на About';
$string['showabouthubs_desc'] = 'Відображати секцію мапи/списку Living Labs і партнерів на сторінці About.';
$string['showaboutfeed'] = 'Показувати стрічку оновлень на About';
$string['showaboutfeed_desc'] = 'Відображати картки стрічки оновлень на сторінці About.';
$string['aboutkicker'] = 'Сторінка About — надзаголовок';
$string['abouttitle'] = 'Сторінка About — заголовок';
$string['aboutlede'] = 'Сторінка About — вступний текст';
$string['aboutstats'] = 'Сторінка About — статистика hero (JSON)';
$string['aboutkpis'] = 'Сторінка About — ряд KPI (JSON)';
$string['aboutfallback_desc'] = 'Залиште порожнім, щоб використати текст із дизайну SecureFood.';
$string['aboutjson_desc'] = 'JSON-список, напр. [{"value": "14", "label": "Living Labs", "sub": "Примітка"}]. Порожнє поле — типові значення дизайну; некоректний JSON повертає їх.';
$string['aboutfeedtitle'] = 'Сторінка About — заголовок стрічки';
$string['aboutfeed'] = 'Сторінка About — стрічка оновлень (JSON)';
$string['aboutfeed_desc'] = 'Ручний fallback JSON-список, напр. [{"chip": "Digital twin", "title": "…", "text": "…", "time": "2 дні тому", "variant": "twin"}]. Live-дані Moodle WS мають пріоритет, якщо увімкнені й доступні. Залиште порожнім, щоб приховати стрічку до появи реальних даних.';
$string['aboutfeedwsheading'] = 'Moodle WS для «Останнє з мережі»';
$string['aboutfeedwsheading_desc'] = 'Дозволяє зовнішньому сервісу замінювати картки стрічки на About-сторінці через Moodle External Services API.';
$string['aboutfeedwsendpoint'] = 'Функція Moodle WS';
$string['aboutfeedwsendpoint_desc'] = 'Викликайте <code>{$a->endpoint}</code> з <code>wsfunction={$a->function}</code>, валідним Moodle webservice token для користувача з <code>moodle/site:config</code> і <code>moodlewsrestformat=json</code>. Обов’язковий параметр: <code>items</code>; кожен елемент може містити <code>chip</code>, <code>title</code>, <code>text</code>, <code>time</code>, <code>variant</code> і <code>url</code>.';
$string['aboutfeedwsenabled'] = 'Увімкнути WS-оновлення стрічки мережі';
$string['aboutfeedwsenabled_desc'] = 'Дозволити функції Moodle web service замінювати live-стрічку About. Доступ додатково контролюється Moodle webservice tokens і capability moodle/site:config.';
$string['aboutfeedwsmaxitems'] = 'Максимум live-карток стрічки';
$string['aboutfeedwsmaxitems_desc'] = 'Скільки карток від зовнішнього сервісу зберігати й відображати. Значення понад 12 обрізаються.';
$string['aboutfeeduselive'] = 'Використовувати live-дані стрічки, якщо доступні';
$string['aboutfeeduselive_desc'] = 'Відображати останній прийнятий Moodle WS payload перед ручним fallback JSON. Якщо обидва джерела порожні, стрічка приховується.';
$string['feedwsdisabled'] = 'Web service стрічки SecureFood вимкнено.';
$string['abouthubstitle'] = 'Сторінка About — заголовок блоку хабів';
$string['abouthubs'] = 'Сторінка About — Living Labs та партнери (JSON)';
$string['abouthubs_desc'] = 'JSON-список, напр. [{"name": "Kyiv Lab", "country": "Ukraine", "type": "lab", "lat": 50.45, "lon": 30.52}]. "type" — "lab" або "partner". Додайте числові "lat" і "lon", щоб розмістити точку на мапі — запис без координат зʼявиться у списку, але не на мапі. Порожнє поле — типові значення дизайну.';
$string['abouthub_lab'] = 'Living Lab';
$string['abouthub_partner'] = 'Партнер';
$string['abouthubmarkerlabel'] = '{$a->name}, {$a->country}, {$a->status}. Перейти до деталей хабу.';
$string['nav_futurefood'] = 'Future Food';
$string['nav_resources'] = 'Ресурси та стандарти';
$string['appearancetitle'] = 'Вигляд SecureFood';
$string['colourscheme'] = 'Колірна схема';
$string['scheme_light'] = 'Світла';
$string['scheme_dark'] = 'Темна';
$string['scheme_system'] = 'Як у системі';
$string['allpreferences'] = 'Усі налаштування';
$string['plancontext_position'] = 'Курс {$a->position} з {$a->total}';
$string['plancontext_progress'] = 'Прогрес курсу';
$string['settingshub'] = 'Налаштування';
$string['settingshub_lede'] = 'Обліковий запис, вигляд і приватність — усе в одному місці.';
$string['hub_profile'] = 'Профіль';
$string['hub_editprofile'] = 'Редагувати профіль';
$string['hub_language'] = 'Мова та регіон';
$string['hub_changelanguage'] = 'Бажана мова';
$string['hub_password'] = 'Пароль';
$string['hub_password_desc'] = 'Оновіть пароль для входу.';
$string['hub_notifications_desc'] = 'Оберіть, які сповіщення отримувати і як.';
$string['hub_privacy_desc'] = 'Керуйте запитами щодо даних і налаштуваннями приватності.';
$string['hub_changepassword'] = 'Змінити пароль';
$string['hub_appearance'] = 'Налаштування вигляду';
$string['hub_notifications'] = 'Сповіщення';
$string['hub_notificationprefs'] = 'Налаштування сповіщень';
$string['hub_privacy'] = 'Приватність';
$string['hub_datarequests'] = 'Мої запити на дані';
$string['hub_allpreferences'] = 'Усі налаштування';
$string['abouttitleaccent'] = 'Сторінка About — акцентні слова заголовка';
$string['aboutshieldchip'] = 'Сторінка About — чип секції "щит"';
$string['aboutshieldtitle'] = 'Сторінка About — заголовок секції "щит"';
$string['aboutshieldbody'] = 'Сторінка About — текст секції "щит" (HTML)';
$string['aboutlayerstitle'] = 'Сторінка About — заголовок шарів';
$string['aboutlayers'] = 'Сторінка About — підсилювальні шари (JSON)';
$string['showaboutapproach'] = 'Показувати секцію «Навчання для змін»';
$string['showaboutapproach_desc'] = 'Показувати на сторінці «Про проєкт» підхід «Навчання для змін» і його чотири кроки.';
$string['aboutapproachkicker'] = 'Сторінка About — надзаголовок підходу';
$string['aboutapproachtitle'] = 'Сторінка About — заголовок підходу';
$string['aboutapproachbody'] = 'Сторінка About — опис підходу';
$string['aboutapproachsteps'] = 'Сторінка About — кроки підходу (JSON)';
$string['aboutapproachsteps_desc'] = 'JSON-список, напр. [{"number":"01 · Рефлексія","title":"Побачте систему","text":"…"}]. Порожнє поле використовує локалізовані типові значення дизайну.';
$string['aboutdefault_kicker'] = 'SecureFood School';
$string['aboutdefault_title'] = 'Навчання для стійкості у';
$string['aboutdefault_titleaccent'] = 'світі, що змінюється';
$string['aboutdefault_lede'] = 'Освітній хаб проєкту SecureFood, який перетворює складні наукові знання й цифрові інструменти на практичні навички для фахівців, управлінців та агентів змін, що будують продовольчі системи майбутнього.';
$string['aboutdefault_shieldchip'] = 'Про проєкт SecureFood · Horizon Europe';
$string['aboutdefault_shieldtitle'] = 'Щит для продовольчих систем майбутнього';
$string['aboutdefault_shieldbody'] = '<p>Наш світ стикається з безпрецедентними викликами — від кліматичних потрясінь до глобальних збоїв у ланцюгах постачання. SecureFood створює «щит» для продовольчих систем майбутнього, поєднуючи цифрові двійники, механізми врядування та громадські Living Labs в одну операційну екосистему.</p><p>Школу засновано як <strong>один із ключових результатів проєкту</strong>: її мета — перетворити наукові напрацювання на навички, які люди можуть застосовувати у школах, кухнях і ланцюгах постачання.</p>';
$string['aboutdefault_layerstitle'] = 'Три взаємопідсилювальні рівні';
$string['aboutdefault_layer1_title'] = 'Цифрові двійники';
$string['aboutdefault_layer1_text'] = 'Моделюйте стресові сценарії у віртуальних середовищах, щоб прогнозувати наслідки до їх появи на практиці.';
$string['aboutdefault_layer2_title'] = 'Механізми врядування';
$string['aboutdefault_layer2_text'] = 'Нові стандарти кризового управління й формування політик для європейських продовольчих ланцюгів.';
$string['aboutdefault_layer3_title'] = 'Living Labs';
$string['aboutdefault_layer3_text'] = 'Простори під проводом громад, де інноваційні рішення випробовують у реальних умовах.';
$string['aboutdefault_approachkicker'] = 'Наш підхід · L4C';
$string['aboutdefault_approachtitle'] = 'Навчання для змін';
$string['aboutdefault_approachbody'] = '<p>Ми виходимо за межі традиційної теорії. <strong>Трансформаційне навчання (L4C)</strong> спонукає критично осмислювати наявні системи та підтримує спільні інновації. Результат — <strong>агенти змін</strong>, здатні діяти й вести інших під час криз, а не лише описувати їх.</p>';
$string['aboutdefault_approach1_number'] = '01 · Рефлексія';
$string['aboutdefault_approach1_title'] = 'Побачте систему';
$string['aboutdefault_approach1_text'] = 'Відобразіть свій контекст і чесно визначте точки напруження та зацікавлені сторони.';
$string['aboutdefault_approach2_number'] = '02 · Переклад';
$string['aboutdefault_approach2_title'] = 'Поєднайте докази';
$string['aboutdefault_approach2_text'] = 'Перетворіть дані датчиків і дослідження на мову, зрозумілу вашій громаді.';
$string['aboutdefault_approach3_number'] = '03 · Співтворення';
$string['aboutdefault_approach3_title'] = 'Будуйте разом';
$string['aboutdefault_approach3_text'] = 'Створюйте прототипи рішень у Living Lab разом із менторами та колегами.';
$string['aboutdefault_approach4_number'] = '04 · Дія';
$string['aboutdefault_approach4_title'] = 'Очольте зміни';
$string['aboutdefault_approach4_text'] = 'Перевіряйте рішення на практиці та повертайте висновки до процесів врядування.';
$string['aboutdefault_hubstitle'] = 'Living Labs і партнери в Європі';
$string['abouthubsnetworktitle'] = 'Сторінка About — заголовок панелі мережі';
$string['abouthubsnetworklede'] = 'Сторінка About — опис панелі мережі';
$string['aboutdefault_hubsnetworktitle'] = 'Мережа';
$string['aboutdefault_hubsnetworklede'] = 'SecureFood обʼєднує партнерів по ЄС та Україні — дослідні інститути, харчові компанії, НУО та інноваторів. Оберіть рядок, щоб сфокусувати мапу.';
$string['aboutdefault_feedtitle'] = 'Останнє з мережі';
$string['aboutdefault_stat_horizon_value'] = '€8M';
$string['aboutdefault_stat_horizon_label'] = 'Horizon Europe';
$string['aboutdefault_stat_partners_value'] = '21';
$string['aboutdefault_stat_partners_label'] = 'Партнер';
$string['aboutdefault_stat_labs_value'] = '10+';
$string['aboutdefault_stat_labs_label'] = 'Living Labs';
$string['aboutdefault_stat_method_value'] = 'L4C';
$string['aboutdefault_stat_method_label'] = 'Методологія';
$string['rail_aria'] = 'Огляд курсу';
$string['rail_nextup'] = 'Далі за планом';
$string['rail_continue'] = 'Продовжити';
$string['rail_courseinfo'] = 'Про курс';
$string['rail_teachers'] = 'Викладачі';
$string['rail_teacher'] = 'Викладач';
$string['rail_sections'] = 'Секції';
$string['rail_activities'] = 'Активності';
$string['rail_progress'] = 'Прогрес';
$string['rail_language'] = 'Мова';
$string['rail_format'] = 'Формат';
$string['rail_complete'] = 'Завершено';
$string['rail_inprogress'] = 'У процесі';
$string['rail_upnext'] = 'Попереду';
$string['helpurl'] = 'Посилання довідки';
$string['helpurl_desc'] = 'Адреса для кнопки «?» у верхній панелі. Порожнє поле — кнопка прихована.';
$string['aboutmaplabel'] = 'Мапа Living Labs і партнерів по Європі';
