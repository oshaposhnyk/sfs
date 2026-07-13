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
 * Repeatable block admin setting for Future Food configuration.
 *
 * @package    local_sfsgame
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_sfsgame\admin_setting;

defined('MOODLE_INTERNAL') || die();

/**
 * Admin setting that edits repeatable UI blocks and stores them as JSON.
 */
final class repeat_blocks extends \admin_setting {
    public const TYPE_MISSIONS = 'missions';
    public const TYPE_DECISIONS = 'decisions';

    /** @var string Block type. */
    private string $type;

    /**
     * Constructor.
     *
     * @param string $name Setting name.
     * @param string $visiblename Visible setting name.
     * @param string $description Setting help text.
     * @param string $defaultsetting Default JSON value.
     * @param string $type Block type.
     */
    public function __construct(
        string $name,
        string $visiblename,
        string $description,
        string $defaultsetting,
        string $type
    ) {
        $this->type = $type;
        $this->customcontrol = true;
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * Return the raw saved JSON.
     *
     * @return string|null
     */
    public function get_setting() {
        return $this->config_read($this->name);
    }

    /**
     * Store blocks as normalized JSON.
     *
     * @param mixed $data Submitted setting data.
     * @return string Empty string on success, error string otherwise.
     */
    public function write_setting($data): string {
        if (!is_array($data)) {
            return get_string('validateerror', 'admin');
        }

        $result = $this->type === self::TYPE_MISSIONS
            ? $this->normalise_missions($data)
            : $this->normalise_decisions($data);

        if (is_string($result)) {
            return $result;
        }

        $json = $result === []
            ? ''
            : json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        if ($json === false) {
            return get_string('validateerror', 'admin');
        }

        return $this->config_write($this->name, $json) ? '' : get_string('errorsetting', 'admin');
    }

    /**
     * Return setting HTML.
     *
     * @param mixed $data Current data or submitted data on validation failure.
     * @param string $query Admin search query.
     * @return string
     */
    public function output_html($data, $query = ''): string {
        global $PAGE;

        $PAGE->requires->js_call_amd('local_sfsgame/repeat_blocks', 'init', [$this->get_id()]);

        $items = $this->items_for_output($data);
        $items[] = $this->empty_item();

        $element = \html_writer::start_tag('div', [
            'id' => $this->get_id(),
            'class' => 'sfs-game-repeat',
            'data-next-index' => count($items),
        ]);
        $element .= \html_writer::empty_tag('input', [
            'type' => 'hidden',
            'name' => $this->get_full_name() . '[_submitted]',
            'value' => '1',
        ]);
        $element .= \html_writer::start_tag('div', ['data-repeat-list' => '1']);
        foreach ($items as $index => $item) {
            $element .= $this->block_html((string)$index, $item);
        }
        $element .= \html_writer::end_tag('div');
        $element .= \html_writer::tag('button', $this->add_label(), [
            'type' => 'button',
            'class' => 'btn btn-secondary',
            'data-repeat-add' => '1',
        ]);
        $element .= \html_writer::tag('template', $this->block_html('__INDEX__', $this->empty_item()), [
            'data-repeat-template' => '1',
        ]);
        $element .= \html_writer::end_tag('div');

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', '', $query);
    }

    /**
     * Convert saved/submitted data to editable rows.
     *
     * @param mixed $data Current data.
     * @return array[]
     */
    private function items_for_output($data): array {
        if (is_array($data)) {
            return $this->type === self::TYPE_MISSIONS
                ? $this->mission_rows_from_array($data)
                : $this->decision_rows_from_array($data);
        }

        return $this->type === self::TYPE_MISSIONS
            ? $this->mission_rows_from_json((string)$data)
            : $this->decision_rows_from_json((string)$data);
    }

    /**
     * Render one editable block.
     *
     * @param string $index Row index or template placeholder.
     * @param array $item Row values.
     * @return string
     */
    private function block_html(string $index, array $item): string {
        $out = \html_writer::start_tag('fieldset', [
            'class' => 'card mb-3 sfs-game-repeat__item',
            'data-repeat-item' => '1',
        ]);
        $out .= \html_writer::tag('legend', $this->block_label($index), ['class' => 'card-header h6 mb-0']);
        $out .= \html_writer::start_tag('div', ['class' => 'card-body']);
        foreach ($this->field_definitions() as $field => $definition) {
            $out .= $this->field_html($index, $field, $definition, (string)($item[$field] ?? ''));
        }
        $out .= \html_writer::tag('button', $this->delete_label(), [
            'type' => 'button',
            'class' => 'btn btn-outline-danger',
            'data-repeat-delete' => '1',
        ]);
        $out .= \html_writer::end_tag('div');
        $out .= \html_writer::end_tag('fieldset');

        return $out;
    }

    /**
     * Render one field.
     *
     * @param string $index Row index or template placeholder.
     * @param string $field Field key.
     * @param array $definition Field definition.
     * @param string $value Current value.
     * @return string
     */
    private function field_html(string $index, string $field, array $definition, string $value): string {
        $id = $this->get_id() . '_' . $field . '_' . $index;
        $name = $this->get_full_name() . '[' . $field . '][' . $index . ']';
        $attrs = [
            'id' => $id,
            'name' => $name,
            'class' => 'form-control',
        ];
        $out = \html_writer::start_tag('div', ['class' => 'form-group']);
        $out .= \html_writer::label($definition['label'], $id);
        if (($definition['type'] ?? 'text') === 'textarea') {
            $attrs['rows'] = (string)($definition['rows'] ?? 3);
            $out .= \html_writer::tag('textarea', s($value), $attrs);
        } else {
            $attrs['type'] = 'text';
            $attrs['value'] = $value;
            $out .= \html_writer::empty_tag('input', $attrs);
        }
        if (!empty($definition['help'])) {
            $out .= \html_writer::div($definition['help'], 'form-text text-muted');
        }
        $out .= \html_writer::end_tag('div');

        return $out;
    }

    /**
     * Fields for current block type.
     *
     * @return array
     */
    private function field_definitions(): array {
        if ($this->type === self::TYPE_MISSIONS) {
            return [
                'badge' => ['label' => get_string('settings:missionbadge', 'local_sfsgame')],
                'tags' => [
                    'label' => get_string('settings:missiontags', 'local_sfsgame'),
                    'help' => get_string('settings:missiontags_help', 'local_sfsgame'),
                ],
                'title' => ['label' => get_string('settings:missiontitle', 'local_sfsgame')],
                'text' => [
                    'label' => get_string('settings:missiontext', 'local_sfsgame'),
                    'type' => 'textarea',
                    'rows' => 3,
                ],
                'duration' => ['label' => get_string('settings:missionduration', 'local_sfsgame')],
                'xp' => ['label' => get_string('settings:missionxp', 'local_sfsgame')],
                'reward' => [
                    'label' => get_string('settings:missionreward', 'local_sfsgame'),
                    'help' => get_string('settings:missionreward_help', 'local_sfsgame'),
                ],
                'url' => [
                    'label' => get_string('settings:missionurl', 'local_sfsgame'),
                    'help' => get_string('settings:missionurl_help', 'local_sfsgame'),
                ],
            ];
        }

        return [
            'label' => ['label' => get_string('settings:choicelabel', 'local_sfsgame')],
            'icon' => ['label' => get_string('settings:choiceicon', 'local_sfsgame')],
            'note' => [
                'label' => get_string('settings:choicenote', 'local_sfsgame'),
                'type' => 'textarea',
                'rows' => 2,
            ],
            'url' => [
                'label' => get_string('settings:choiceurl', 'local_sfsgame'),
                'help' => get_string('settings:url_help', 'local_sfsgame'),
            ],
        ];
    }

    /**
     * Normalise mission rows.
     *
     * @param array $data Submitted rows.
     * @return array|string
     */
    private function normalise_missions(array $data) {
        $rows = [];
        foreach ($this->indexes($data, ['badge', 'tags', 'title', 'text', 'reward', 'duration', 'xp', 'url']) as $index) {
            $badge = clean_param($this->value_at($data, 'badge', $index), PARAM_TEXT);
            $tags = clean_param($this->value_at($data, 'tags', $index), PARAM_TEXT);
            $title = clean_param($this->value_at($data, 'title', $index), PARAM_TEXT);
            $text = clean_param($this->value_at($data, 'text', $index), PARAM_TEXT);
            $reward = clean_param($this->value_at($data, 'reward', $index), PARAM_TEXT);
            $duration = clean_param($this->value_at($data, 'duration', $index), PARAM_TEXT);
            $xp = $this->value_at($data, 'xp', $index);
            $url = clean_param($this->value_at($data, 'url', $index), PARAM_TEXT);

            if (!$this->has_any_value([$badge, $tags, $title, $text, $reward, $duration, $xp, $url])) {
                continue;
            }
            if ($title === '') {
                return get_string('settings:requiredtitle', 'local_sfsgame');
            }
            if ($url !== '' && !$this->is_supported_url($url)) {
                return get_string('settings:invalidurl', 'local_sfsgame');
            }
            if ($xp !== '' && !preg_match('/^-?\d+$/', $xp)) {
                return get_string('settings:invalidxp', 'local_sfsgame');
            }

            $rows[] = [
                'badge' => $badge,
                'tags' => $this->tags_from_string($tags),
                'title' => $title,
                'text' => $text,
                'reward' => $reward,
                'duration' => $duration,
                'xp' => max(0, (int)$xp),
                'url' => $url,
            ];
        }

        return $rows;
    }

    /**
     * Normalise decision rows.
     *
     * @param array $data Submitted rows.
     * @return array|string
     */
    private function normalise_decisions(array $data) {
        $rows = [];
        foreach ($this->indexes($data, ['label', 'icon', 'note', 'url']) as $index) {
            $label = clean_param($this->value_at($data, 'label', $index), PARAM_TEXT);
            $icon = clean_param($this->value_at($data, 'icon', $index), PARAM_TEXT);
            $note = clean_param($this->value_at($data, 'note', $index), PARAM_TEXT);
            $url = clean_param($this->value_at($data, 'url', $index), PARAM_TEXT);

            if (!$this->has_any_value([$label, $icon, $note, $url])) {
                continue;
            }
            if ($label === '') {
                return get_string('settings:requiredlabel', 'local_sfsgame');
            }
            if ($url === '') {
                return get_string('settings:requiredurl', 'local_sfsgame');
            }
            if (!$this->is_supported_url($url)) {
                return get_string('settings:invalidurl', 'local_sfsgame');
            }

            $rows[] = [
                'label' => $label,
                'icon' => $icon,
                'note' => $note,
                'url' => $url,
            ];
        }

        return $rows;
    }

    /**
     * Convert missions JSON to editable rows.
     *
     * @param string $json Saved JSON.
     * @return array[]
     */
    private function mission_rows_from_json(string $json): array {
        $rows = [];
        foreach (\local_sfsgame\missions::parse($json) as $mission) {
            $rows[] = [
                'badge' => $mission['badge'],
                'tags' => implode(', ', array_map(static fn($tag) => $tag['name'], $mission['tags'])),
                'title' => $mission['title'],
                'text' => $mission['text'],
                'reward' => $mission['reward'],
                'duration' => $mission['duration'],
                'xp' => (string)$mission['xp'],
                'url' => $mission['url'],
            ];
        }

        return $rows;
    }

    /**
     * Convert submitted mission data to editable rows.
     *
     * @param array $data Submitted data.
     * @return array[]
     */
    private function mission_rows_from_array(array $data): array {
        $rows = [];
        foreach ($this->indexes($data, ['badge', 'tags', 'title', 'text', 'reward', 'duration', 'xp', 'url']) as $index) {
            $rows[] = [
                'badge' => $this->value_at($data, 'badge', $index),
                'tags' => $this->value_at($data, 'tags', $index),
                'title' => $this->value_at($data, 'title', $index),
                'text' => $this->value_at($data, 'text', $index),
                'reward' => $this->value_at($data, 'reward', $index),
                'duration' => $this->value_at($data, 'duration', $index),
                'xp' => $this->value_at($data, 'xp', $index),
                'url' => $this->value_at($data, 'url', $index),
            ];
        }

        return $rows;
    }

    /**
     * Convert decisions JSON to editable rows.
     *
     * @param string $json Saved JSON.
     * @return array[]
     */
    private function decision_rows_from_json(string $json): array {
        $rows = [];
        foreach (\local_sfsgame\decision::parse($json) as $choice) {
            $rows[] = [
                'label' => $choice['label'],
                'icon' => $choice['icon'],
                'note' => $choice['note'],
                'url' => $choice['url'],
            ];
        }

        return $rows;
    }

    /**
     * Convert submitted decision data to editable rows.
     *
     * @param array $data Submitted data.
     * @return array[]
     */
    private function decision_rows_from_array(array $data): array {
        $rows = [];
        foreach ($this->indexes($data, ['label', 'icon', 'note', 'url']) as $index) {
            $rows[] = [
                'label' => $this->value_at($data, 'label', $index),
                'icon' => $this->value_at($data, 'icon', $index),
                'note' => $this->value_at($data, 'note', $index),
                'url' => $this->value_at($data, 'url', $index),
            ];
        }

        return $rows;
    }

    /**
     * Find row indexes in nested form data.
     *
     * @param array $data Submitted rows.
     * @param string[] $fields Field keys.
     * @return int[]
     */
    private function indexes(array $data, array $fields): array {
        $indexes = [];
        foreach ($fields as $field) {
            foreach (array_keys((array)($data[$field] ?? [])) as $index) {
                if ($index !== '__INDEX__') {
                    $indexes[(int)$index] = (int)$index;
                }
            }
        }
        ksort($indexes);

        return array_values($indexes);
    }

    /**
     * Read one nested value.
     *
     * @param array $data Submitted rows.
     * @param string $field Field key.
     * @param int $index Row index.
     * @return string
     */
    private function value_at(array $data, string $field, int $index): string {
        $values = (array)($data[$field] ?? []);

        return trim((string)($values[$index] ?? ''));
    }

    /**
     * Is at least one value non-empty?
     *
     * @param string[] $values Values.
     * @return bool
     */
    private function has_any_value(array $values): bool {
        foreach ($values as $value) {
            if (trim((string)$value) !== '') {
                return true;
            }
        }

        return false;
    }

    /**
     * Parse comma-separated tags.
     *
     * @param string $tags Tags string.
     * @return string[]
     */
    private function tags_from_string(string $tags): array {
        $out = [];
        foreach (explode(',', $tags) as $tag) {
            $tag = trim($tag);
            if ($tag !== '') {
                $out[] = $tag;
            }
        }

        return $out;
    }

    /**
     * Check whether URL is safe for Future Food links.
     *
     * @param string $url Raw URL.
     * @return bool
     */
    private function is_supported_url(string $url): bool {
        $url = trim($url);
        if ($url === '' || str_starts_with($url, '//')
                || (!str_starts_with($url, '/') && !preg_match('~^https?://~i', $url))) {
            return false;
        }

        try {
            new \moodle_url($url);
        } catch (\Throwable $exception) {
            return false;
        }

        return true;
    }

    /**
     * Empty item for rendering a blank block.
     *
     * @return array
     */
    private function empty_item(): array {
        return $this->type === self::TYPE_MISSIONS
            ? ['badge' => '', 'tags' => '', 'title' => '', 'text' => '',
                'reward' => '', 'duration' => '', 'xp' => '', 'url' => '']
            : ['label' => '', 'icon' => '', 'note' => '', 'url' => ''];
    }

    /**
     * Add-button label.
     *
     * @return string
     */
    private function add_label(): string {
        return $this->type === self::TYPE_MISSIONS
            ? get_string('settings:addmission', 'local_sfsgame')
            : get_string('settings:addchoice', 'local_sfsgame');
    }

    /**
     * Delete-button label.
     *
     * @return string
     */
    private function delete_label(): string {
        return $this->type === self::TYPE_MISSIONS
            ? get_string('settings:deletemission', 'local_sfsgame')
            : get_string('settings:deletechoice', 'local_sfsgame');
    }

    /**
     * Block heading label.
     *
     * @param string $index Row index.
     * @return string
     */
    private function block_label(string $index): string {
        if ($index === '__INDEX__') {
            return $this->type === self::TYPE_MISSIONS
                ? get_string('settings:missionblocknew', 'local_sfsgame')
                : get_string('settings:choiceblocknew', 'local_sfsgame');
        }

        $number = (int)$index + 1;

        return $this->type === self::TYPE_MISSIONS
            ? get_string('settings:missionblock', 'local_sfsgame', $number)
            : get_string('settings:choiceblock', 'local_sfsgame', $number);
    }
}
