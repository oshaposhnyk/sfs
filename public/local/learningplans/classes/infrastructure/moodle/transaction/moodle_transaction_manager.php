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

namespace local_learningplans\infrastructure\moodle\transaction;

use local_learningplans\application\port\transaction_manager_interface;

defined('MOODLE_INTERNAL') || die();

/**
 * Moodle transaction adapter.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class moodle_transaction_manager implements transaction_manager_interface {
    /**
     * @inheritDoc
     */
    public function run(callable $callback) {
        global $DB;
        $transaction = $DB->start_delegated_transaction();
        try {
            $result = $callback();
            $transaction->allow_commit();
            return $result;
        } catch (\Throwable $exception) {
            $transaction->rollback($exception);
        }
    }
}

