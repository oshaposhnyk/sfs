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

namespace local_learningplans;

use local_learningplans\infrastructure\moodle\event\moodle_event_dispatcher;

defined('MOODLE_INTERNAL') || die();

/**
 * Event dispatcher tests.
 *
 * @package    local_learningplans
 * @category   test
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class moodle_event_dispatcher_test extends \advanced_testcase {
    /**
     * Event adapter should trigger mapped event.
     */
    public function test_dispatch_user_enrolled_event(): void {
        global $DB;
        $this->resetAfterTest();

        $admin = get_admin();
        $this->setUser($admin);

        $planid = (int)$DB->insert_record('local_learningplans_plan', (object)[
            'name' => 'Plan X',
            'description' => '',
            'enabled' => 1,
            'sequentialmode' => 0,
            'enrolmentmode' => 'immediate',
            'createdby' => (int)$admin->id,
            'timecreated' => time(),
            'timemodified' => time(),
        ]);

        $user = $this->getDataGenerator()->create_user();
        $dispatcher = new moodle_event_dispatcher();

        $sink = $this->redirectEvents();
        $dispatcher->dispatch('user_enrolled', [
            'objectid' => $planid,
            'userid' => (int)$admin->id,
            'relateduserid' => (int)$user->id,
        ]);
        $events = $sink->get_events();
        $sink->close();

        $this->assertCount(1, $events);
        $this->assertInstanceOf(\local_learningplans\event\user_enrolled_into_learning_plan::class, $events[0]);
        $this->assertSame($planid, (int)$events[0]->objectid);
        $this->assertSame((int)$user->id, (int)$events[0]->relateduserid);
    }
}

