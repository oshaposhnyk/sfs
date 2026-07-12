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
 * Ukrainian strings for local_learningplans.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Навчальні плани';
$string['nav:manage'] = 'Керування навчальними планами';
$string['privacy:metadata'] = 'Плагін навчальних планів зберігає членства користувачів, повʼязані зарахування та знімки прогресу.';

$string['settings:title'] = 'Налаштування навчальних планів';
$string['settings:unenrolpolicy'] = 'Політика відрахування з плану';
$string['settings:unenrolpolicy_desc'] = 'Визначає, що робити із зарахуваннями на курси, створеними через членство в плані, при відрахуванні з плану.';
$string['settings:unenrolpolicy:remove'] = 'Відрахувати з курсів, якими керує навчальний план';
$string['settings:unenrolpolicy:keep'] = 'Зберегти зарахування на курси і лише видалити членство в плані';
$string['settings:defaultroleid'] = 'Роль курсу за замовчуванням';
$string['settings:defaultroleid_desc'] = 'Роль, яка призначається під час зарахування через навчальний план.';
$string['settings:cohortsync'] = 'Увімкнути синхронізацію з когортами';
$string['settings:cohortsync_desc'] = 'Якщо увімкнено, додавання користувача до когорти, повʼязаної з навчальним планом, автоматично зараховує його до цього плану, а видалення — відраховує (відповідно до політики відрахування). Вимкніть, щоб призупинити автоматичне зарахування через когорти, не видаляючи наявні звʼязки.';

$string['learningplans:view'] = 'Переглядати навчальні плани';
$string['learningplans:manage'] = 'Керувати навчальними планами';
$string['learningplans:enrolusers'] = 'Зараховувати користувачів до навчальних планів';
$string['learningplans:unenrolusers'] = 'Відраховувати користувачів із навчальних планів';
$string['learningplans:viewprogress'] = 'Переглядати прогрес навчального плану';
$string['learningplans:viewallprogress'] = 'Переглядати прогрес інших користувачів у навчальних планах';
$string['learningplans:configure'] = 'Налаштовувати плагін навчальних планів';

$string['list:title'] = 'Навчальні плани';
$string['list:mytitle'] = 'Мої навчальні плани';
$string['list:empty'] = 'Навчальні плани не знайдено.';
$string['list:create'] = 'Створити навчальний план';
$string['list:view'] = 'Перегляд';
$string['list:edit'] = 'Редагувати';
$string['list:archive'] = 'Архівувати';
$string['list:activate'] = 'Активувати';
$string['list:delete'] = 'Видалити';
$string['list:courses'] = 'Курси';
$string['list:members'] = 'Учасники';
$string['list:status'] = 'Статус';
$string['list:enabled'] = 'Активний';
$string['list:disabled'] = 'Архівований';

$string['plan:create:title'] = 'Створення навчального плану';
$string['plan:edit:title'] = 'Редагування навчального плану';
$string['plan:name'] = 'Назва навчального плану';
$string['plan:description'] = 'Опис';
$string['plan:enabled'] = 'Активний';
$string['plan:sequentialmode'] = 'Вимагати послідовне завершення';
$string['plan:enrolmentmode'] = 'Режим зарахування на курси';
$string['plan:enrolmentmode:immediate'] = 'Зараховувати одразу на всі курси плану';
$string['plan:enrolmentmode:sequentialrelease'] = 'Зараховувати лише на доступні поточні послідовні курси';
$string['plan:save'] = 'Зберегти зміни';
$string['plan:created'] = 'Навчальний план створено.';
$string['plan:updated'] = 'Навчальний план оновлено.';
$string['plan:deleted'] = 'Навчальний план видалено.';
$string['plan:archived'] = 'Навчальний план архівовано.';
$string['plan:activated'] = 'Навчальний план активовано.';
$string['plan:delete:confirm'] = 'Видалити навчальний план "{$a}"?';

$string['plan:view:title'] = 'Навчальний план';
$string['plan:view:manageenrolments'] = 'Керувати зарахуваннями';
$string['plan:view:managecohorts'] = 'Керувати когортами';
$string['plan:view:addcourse'] = 'Додати курс';
$string['plan:view:nocourses'] = 'Курси ще не додані.';
$string['plan:view:course'] = 'Курс';
$string['plan:view:order'] = 'Порядок';
$string['plan:view:actions'] = 'Дії';
$string['plan:view:remove'] = 'Видалити';
$string['plan:view:moveup'] = 'Вгору';
$string['plan:view:movedown'] = 'Вниз';
$string['plan:view:memberships'] = 'Учасники плану';
$string['plan:view:nomembers'] = 'Учасників ще не зараховано.';
$string['plan:view:user'] = 'Користувач';
$string['plan:view:progress'] = 'Прогрес';
$string['plan:view:nextcourse'] = 'Наступний обовʼязковий курс';
$string['plan:view:completedcourses'] = 'Завершено';
$string['plan:view:inprogresscourses'] = 'У процесі';
$string['plan:view:notstartedcourses'] = 'Не розпочато';

$string['course:add:title'] = 'Додати курс до навчального плану';
$string['course:add:courseid'] = 'Курс';
$string['course:add:submit'] = 'Додати курс';
$string['course:added'] = 'Курс додано до навчального плану.';
$string['course:removed'] = 'Курс видалено з навчального плану.';
$string['course:reordered'] = 'Порядок курсів у навчальному плані оновлено.';
$string['course:duplicate'] = 'Цей курс уже входить до навчального плану.';

$string['cohort:title'] = 'Повʼязані когорти';
$string['cohort:intro'] = 'Користувачі, які належать до повʼязаної когорти, автоматично зараховуються до цього навчального плану. Видалення користувача з усіх повʼязаних когорт відраховує його з плану, якщо він не був також зарахований вручну. Ручні зарахування ніколи не видаляються синхронізацією з когортами.';
$string['cohort:back'] = 'Назад до навчального плану';
$string['cohort:name'] = 'Когорта';
$string['cohort:none'] = 'До цього навчального плану ще не повʼязано жодної когорти.';
$string['cohort:add:title'] = 'Повʼязати когорту';
$string['cohort:add:cohort'] = 'Когорта';
$string['cohort:add:submit'] = 'Повʼязати когорту';
$string['cohort:linked'] = 'Когорту повʼязано. Учасників буде зараховано у фоновому режимі найближчим часом.';
$string['cohort:alreadylinked'] = 'Цю когорту вже повʼязано з навчальним планом.';
$string['cohort:unlink'] = 'Відʼєднати';
$string['cohort:unlinked'] = 'Когорту відʼєднано. Відповідних учасників буде оновлено у фоновому режимі найближчим часом.';
$string['cohort:missing'] = 'видалено';

$string['enrol:title'] = 'Керування зарахуваннями плану';
$string['enrol:selectusers'] = 'Користувачі';
$string['enrol:selectusers_help'] = 'Знайдіть і виберіть одного або кількох користувачів для зарахування до цього навчального плану.';
$string['enrol:submit'] = 'Зарахувати користувача(ів)';
$string['enrol:success'] = 'Користувача(ів) зараховано до навчального плану.';
$string['enrol:unenrol'] = 'Відрахувати';
$string['enrol:unenrolconfirm'] = 'Відрахувати користувача "{$a}" з навчального плану?';
$string['enrol:removed'] = 'Користувача відраховано з навчального плану.';
$string['enrol:failed'] = 'Не вдалося створити зарахування на курси для цього членства.';

$string['progress:label'] = '{$a}% завершено';
$string['progress:completed'] = 'Завершено';
$string['progress:inprogress'] = 'У процесі';
$string['progress:notstarted'] = 'Не розпочато';
$string['progress:total'] = 'Усього курсів';
$string['progress:none'] = 'Дані прогресу поки відсутні.';
$string['progress:usercompletedplan'] = 'Користувач завершив навчальний план.';
$string['my:empty'] = 'Ви ще не зараховані до жодного навчального плану.';

$string['error:plannotfound'] = 'Навчальний план не знайдено.';
$string['error:membershipnotfound'] = 'Членство в навчальному плані не знайдено.';
$string['error:cannotdeletewithactivememberships'] = 'Неможливо видалити навчальний план з активними учасниками.';
$string['error:coursenotfound'] = 'Курс не знайдено.';
$string['error:usernotfound'] = 'Користувача не знайдено.';
$string['error:invaliduserids'] = 'Виберіть принаймні одного коректного користувача.';
$string['error:invalidcourseorder'] = 'Передано некоректний порядок курсів.';
$string['error:nocoursesinplan'] = 'У цьому навчальному плані немає курсів.';
$string['error:insufficientcapability'] = 'У вас немає прав для цієї дії.';
$string['error:cohortnotfound'] = 'Когорту не знайдено.';

$string['event:learningplancreated'] = 'Створено навчальний план';
$string['event:learningplanupdated'] = 'Оновлено навчальний план';
$string['event:learningplandeleted'] = 'Видалено навчальний план';
$string['event:learningplanarchived'] = 'Архівовано навчальний план';
$string['event:courseadded'] = 'Курс додано до навчального плану';
$string['event:courseremoved'] = 'Курс видалено з навчального плану';
$string['event:coursesreordered'] = 'Змінено порядок курсів навчального плану';
$string['event:userenrolled'] = 'Користувача зараховано до навчального плану';
$string['event:userunenrolled'] = 'Користувача відраховано з навчального плану';
$string['event:usercompleted'] = 'Користувач завершив навчальний план';
$string['event:progressrecalculated'] = 'Прогрес навчального плану перераховано';
$string['event:enrolmentfailed'] = 'Помилка зарахування до навчального плану';
$string['event:courseenrolmentcreated'] = 'Створено зарахування на курс через навчальний план';
$string['event:courseenrolmentremoved'] = 'Видалено зарахування на курс, створене через навчальний план';
$string['event:cohortlinked'] = 'Когорту повʼязано з навчальним планом';
$string['event:cohortunlinked'] = 'Когорту відʼєднано від навчального плану';

$string['task:refreshprogress'] = 'Оновлення узгодженості прогресу навчальних планів';
$string['task:reconcilecohorts'] = 'Узгодження членств навчальних планів за когортами';

$string['privacy:metadata:local_learningplans_plan'] = 'Зберігає визначення навчальних планів.';
$string['privacy:metadata:local_learningplans_plan:name'] = 'Назва навчального плану.';
$string['privacy:metadata:local_learningplans_plan:description'] = 'Опис навчального плану.';
$string['privacy:metadata:local_learningplans_plan:createdby'] = 'Користувач, що створив план.';
$string['privacy:metadata:local_learningplans_plan:timecreated'] = 'Час створення плану.';
$string['privacy:metadata:local_learningplans_plan:timemodified'] = 'Час останнього оновлення плану.';

$string['privacy:metadata:local_learningplans_mem'] = 'Зберігає членства користувачів у навчальних планах.';
$string['privacy:metadata:local_learningplans_mem:planid'] = 'ID навчального плану.';
$string['privacy:metadata:local_learningplans_mem:userid'] = 'ID користувача, зарахованого до плану.';
$string['privacy:metadata:local_learningplans_mem:status'] = 'Статус членства.';
$string['privacy:metadata:local_learningplans_mem:source'] = 'Спосіб створення членства (вручну або через когорту).';
$string['privacy:metadata:local_learningplans_mem:enrolledby'] = 'ID користувача, що виконав зарахування.';
$string['privacy:metadata:local_learningplans_mem:timecreated'] = 'Час створення членства.';
$string['privacy:metadata:local_learningplans_mem:timemodified'] = 'Час останньої зміни членства.';
$string['privacy:metadata:local_learningplans_mem:timecompleted'] = 'Час фіксації завершення плану.';

$string['privacy:metadata:local_learningplans_enrl'] = 'Зберігає зарахування на курси, створені через членство у плані.';
$string['privacy:metadata:local_learningplans_enrl:membershipid'] = 'ID членства в плані.';
$string['privacy:metadata:local_learningplans_enrl:planid'] = 'ID навчального плану.';
$string['privacy:metadata:local_learningplans_enrl:courseid'] = 'ID курсу.';
$string['privacy:metadata:local_learningplans_enrl:userid'] = 'ID користувача.';
$string['privacy:metadata:local_learningplans_enrl:enrolid'] = 'ID екземпляра зарахування курсу.';
$string['privacy:metadata:local_learningplans_enrl:userenrolmentid'] = 'ID запису user_enrolments.';
$string['privacy:metadata:local_learningplans_enrl:status'] = 'Статус повʼязаного зарахування.';
$string['privacy:metadata:local_learningplans_enrl:timecreated'] = 'Час створення звʼязку.';
$string['privacy:metadata:local_learningplans_enrl:timemodified'] = 'Час останньої зміни звʼязку.';

$string['privacy:metadata:local_learningplans_prog'] = 'Зберігає кешовані знімки прогресу.';
$string['privacy:metadata:local_learningplans_prog:planid'] = 'ID навчального плану.';
$string['privacy:metadata:local_learningplans_prog:userid'] = 'ID користувача.';
$string['privacy:metadata:local_learningplans_prog:totalcourses'] = 'Загальна кількість курсів у плані.';
$string['privacy:metadata:local_learningplans_prog:completedcourses'] = 'Кількість завершених курсів.';
$string['privacy:metadata:local_learningplans_prog:inprogresscourses'] = 'Кількість курсів у процесі.';
$string['privacy:metadata:local_learningplans_prog:notstartedcourses'] = 'Кількість нерозпочатих курсів.';
$string['privacy:metadata:local_learningplans_prog:progresspercent'] = 'Відсоток завершення.';
$string['privacy:metadata:local_learningplans_prog:nextcourseid'] = 'ID наступного обовʼязкового курсу.';
$string['privacy:metadata:local_learningplans_prog:calculatedat'] = 'Час обчислення знімка.';

$string['privacy:metadata:local_learningplans_coh'] = 'Зберігає звʼязки когорт із навчальними планами.';
$string['privacy:metadata:local_learningplans_coh:planid'] = 'ID навчального плану.';
$string['privacy:metadata:local_learningplans_coh:cohortid'] = 'ID повʼязаної когорти.';
$string['privacy:metadata:local_learningplans_coh:createdby'] = 'Користувач, який повʼязав когорту.';
$string['privacy:metadata:local_learningplans_coh:timecreated'] = 'Час створення звʼязку з когортою.';

// Student Lab (ADR-008).
$string['studentlab:title'] = 'Студентська лабораторія';
$string['studentlab:kicker'] = 'Моє навчання';
$string['studentlab:lede'] = 'Ваші активні курси, впорядковані за навчальним планом. Завершуйте кожен курс, щоб відкрити наступний крок вашого шляху.';
$string['studentlab:activeplan'] = 'Активний навчальний план';
$string['studentlab:continue'] = 'Продовжити навчання';
$string['studentlab:courses'] = 'Курси';
$string['studentlab:complete'] = 'Завершено';
$string['studentlab:planprogress'] = 'Прогрес навчального плану';
$string['studentlab:planmeta'] = '{$a->completed} з {$a->total} курсів';
$string['studentlab:coursenumber'] = 'Курс {$a}';
$string['studentlab:percentcomplete'] = 'Завершено {$a}%';
$string['studentlab:lockedhint'] = 'Завершіть попередній курс, щоб відкрити цей';
$string['studentlab:nocourses'] = 'У цьому навчальному плані ще немає курсів.';
$string['studentlab:status:done'] = 'Завершено';
$string['studentlab:status:active'] = 'Активний';
$string['studentlab:status:upnext'] = 'Далі';
$string['studentlab:status:locked'] = 'Заблоковано';
$string['studentlab:action:review'] = 'Переглянути';
$string['studentlab:action:resume'] = 'Продовжити';
$string['studentlab:action:start'] = 'Почати';
$string['privacy:metadata:preference:activeplan'] = 'Навчальний план, який користувач обрав активним.';
$string['course:stagename'] = 'Назва етапу';
$string['course:stagename_help'] = 'Необов’язково. Послідовні курси з однаковою назвою етапу показуються одним етапом на сторінці Студентської лабораторії (напр. "Етап 1 · Основи"). Порожнє поле — етап без назви.';
$string['studentlab:stagedefault'] = 'Етап {$a}';
$string['studentlab:stageoftotal'] = 'Етап {$a->current} з {$a->total}';
$string['studentlab:modulecount'] = 'Модулів: {$a}';
