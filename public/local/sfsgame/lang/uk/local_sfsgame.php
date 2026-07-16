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
$string['nav:manageachievements'] = 'Керувати досягненнями';
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
$string['settings:pageheading'] = 'Сторінка Future Food';
$string['settings:pageheading_desc'] = 'No-code керування сторінкою Future Food для учнів.';
$string['settings:heroheading'] = 'Hero-секція';
$string['settings:heroheading_desc'] = 'Налаштування mission hero та CTA першої місії.';
$string['settings:decisionheading'] = 'Точка рішення';
$string['settings:decisionheading_desc'] = 'Налаштування сценарного блоку рішення та посилань на реальні Moodle-активності.';
$string['settings:missionsheading'] = 'Картки місій';
$string['settings:missionsheading_desc'] = 'Налаштування повторюваних відео-карток місій під досягненнями.';
$string['showpagehead'] = 'Показувати заголовок сторінки';
$string['showpagehead_desc'] = 'Відображати надзаголовок, заголовок і вступний текст сторінки.';
$string['showhero'] = 'Показувати hero-секцію';
$string['showhero_desc'] = 'Відображати mission hero, XP-картку та CTA щоденної місії.';
$string['showachievements'] = 'Показувати досягнення';
$string['showachievements_desc'] = 'Відображати секцію досягнень на основі бейджів.';
$string['showmissions'] = 'Показувати картки місій';
$string['showmissions_desc'] = 'Відображати секцію налаштованих карток місій.';
$string['pagefallback_desc'] = 'Порожнє поле використовує типовий текст Future Food.';
$string['labelfallback_desc'] = 'Порожнє поле використовує типовий підпис.';
$string['setting:pagekicker'] = 'Надзаголовок сторінки';
$string['setting:pagetitle'] = 'Заголовок сторінки';
$string['setting:pagelede'] = 'Вступний текст сторінки';
$string['setting:achievementstitle'] = 'Заголовок досягнень';
$string['setting:noachievements'] = 'Повідомлення без досягнень';
$string['setting:missionstitle'] = 'Заголовок місій';
$string['setting:nomissions'] = 'Повідомлення без місій';
$string['setting:startdailylabel'] = 'Підпис hero CTA';
$string['setting:startmissionlabel'] = 'Підпис CTA місії';
$string['setting:currentranklabel'] = 'Підпис поточного рангу';
$string['setting:totalxplabel'] = 'Підпис загального XP';
$string['setting:decisionkicker'] = 'Надзаголовок рішення';
$string['setting:decisiontitle'] = 'Заголовок рішення';
$string['setting:decisionempty'] = 'Повідомлення без варіантів рішення';
$string['setting:decisionhint'] = 'Підказка рішення';
$string['setting:decisionpill'] = 'Підпис бейджа рішення';
$string['missions'] = 'Картки місій';
$string['missions_desc'] = 'Додавайте, редагуйте або видаляйте картки місій. Щоб нараховувати налаштований XP, URL має вести на Moodle-активність (посилання /mod/…, напр. тест чи сторінку) з увімкненим completion — XP нараховується учневі після завершення цієї активності. Посилання на курс (/course/view.php) або зовнішній URL теж показують картку, але XP не нараховується, тож «+XP» на таких картках приховується.';
$string['missionstitle'] = 'Активні місії';
$string['nomissions'] = 'Місії ще не опубліковано.';
$string['missionrewardxp'] = 'Нагорода: +{$a} XP';
$string['missionrewardavailable'] = 'Доступно після Moodle completion: +{$a} XP';
$string['missionrewardawarded'] = 'Нараховано: +{$a} XP';
$string['missionrewardnottracked'] = 'Автоматичний XP для цього посилання не нараховується. Використайте Moodle-активність з completion, щоб нарахувати +{$a} XP.';
$string['missionchip:completed'] = 'Завершено';
$string['missionchip:incomplete'] = 'У процесі';
$string['missionnoaccess'] = 'У вас поки немає доступу до цієї місії.';
$string['missioncompletion:completed'] = 'Завершено · XP нараховано';
$string['missioncompletion:incomplete'] = 'Завершіть активність, щоб отримати XP';
$string['missioncompletion:notcompletable'] = 'Completion для активності не увімкнено';
$string['missioncompletion:untracked'] = 'Зовнішнє посилання або не активність';
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
$string['decisionchoices'] = 'Варіанти рішення';
$string['decisionchoices_desc'] = 'Додавайте, редагуйте або видаляйте посилання на реальні Moodle-активності для точки рішення.';
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
$string['settings:addchoice'] = 'Додати варіант рішення';
$string['settings:addmission'] = 'Додати картку місії';
$string['settings:choiceblock'] = 'Варіант рішення {$a}';
$string['settings:choiceblocknew'] = 'Новий варіант рішення';
$string['settings:choiceicon'] = 'Іконка';
$string['settings:choicelabel'] = 'Підпис';
$string['settings:choicenote'] = 'Примітка';
$string['settings:choiceurl'] = 'URL активності';
$string['settings:deletechoice'] = 'Видалити цей варіант';
$string['settings:deletemission'] = 'Видалити цю місію';
$string['settings:invalidurl'] = 'Використайте відносний Moodle URL, що починається з /, або http/https URL.';
$string['settings:invalidxp'] = 'XP має бути цілим числом.';
$string['settings:missionbadge'] = 'Підпис бейджа';
$string['settings:missionblock'] = 'Картка місії {$a}';
$string['settings:missionblocknew'] = 'Нова картка місії';
$string['settings:missionduration'] = 'Тривалість';
$string['settings:missionreward'] = 'Текст нагороди';
$string['settings:missionreward_help'] = 'Необов’язково. Порожнє поле покаже «Нагорода: +XP».';
$string['settings:missiontags'] = 'Теги';
$string['settings:missiontags_help'] = 'Розділяйте теги комами.';
$string['settings:missiontext'] = 'Опис';
$string['settings:missiontitle'] = 'Заголовок';
$string['settings:missionurl'] = 'URL відео або активності';
$string['settings:missionurl_help'] = 'Використайте внутрішній URL Moodle-активності на кшталт /mod/page/view.php?id=123 з увімкненим completion, щоб нараховувати XP. Зовнішні http/https URL можна показувати як відео-ресурси, але вони не нараховують XP автоматично.';
$string['settings:missionxp'] = 'XP';
$string['settings:requiredlabel'] = 'Кожен налаштований варіант рішення повинен мати підпис.';
$string['settings:requiredtitle'] = 'Кожна налаштована картка місії повинна мати заголовок.';
$string['settings:requiredurl'] = 'Кожен налаштований варіант рішення повинен мати URL активності.';
$string['settings:url_help'] = 'Використайте внутрішній Moodle шлях на кшталт /course/view.php?id=2 або повний https URL.';
