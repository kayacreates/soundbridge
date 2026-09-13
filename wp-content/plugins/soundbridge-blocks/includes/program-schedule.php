<?php
/** Structured Program schedule helpers and editor. */

defined('ABSPATH') || exit;

function soundbridge_program_schedule_days(): array
{
    return array(
        'monday' => __('Monday', 'soundbridge-blocks'),
        'tuesday' => __('Tuesday', 'soundbridge-blocks'),
        'wednesday' => __('Wednesday', 'soundbridge-blocks'),
        'thursday' => __('Thursday', 'soundbridge-blocks'),
        'friday' => __('Friday', 'soundbridge-blocks'),
        'saturday' => __('Saturday', 'soundbridge-blocks'),
        'sunday' => __('Sunday', 'soundbridge-blocks'),
    );
}

function soundbridge_program_schedule_types(): array
{
    return array(
        'orientation' => __('Orientation', 'soundbridge-blocks'),
        'coaching' => __('Coaching / rehearsal', 'soundbridge-blocks'),
        'break' => __('Break', 'soundbridge-blocks'),
        'workshop' => __('Workshop', 'soundbridge-blocks'),
        'theory' => __('Theory', 'soundbridge-blocks'),
        'performance' => __('Performance', 'soundbridge-blocks'),
        'other' => __('Other', 'soundbridge-blocks'),
    );
}

function soundbridge_sanitize_program_schedule_items($value): string
{
    $decoded = is_array($value) ? $value : json_decode((string) $value, true);
    if (!is_array($decoded)) return '';

    $allowed_days = array_keys(soundbridge_program_schedule_days());
    $allowed_types = array_keys(soundbridge_program_schedule_types());
    $items = array();

    foreach ($decoded as $item) {
        if (!is_array($item)) continue;
        $start = sanitize_text_field($item['start'] ?? '');
        $end = sanitize_text_field($item['end'] ?? '');
        $title = sanitize_text_field($item['title'] ?? '');
        $days = array_values(array_intersect($allowed_days, array_map('sanitize_key', (array) ($item['days'] ?? array()))));
        $type = sanitize_key($item['type'] ?? 'other');
        if (!$title || !$start || !$end || !$days || !preg_match('/^\d{2}:\d{2}$/', $start) || !preg_match('/^\d{2}:\d{2}$/', $end) || $end <= $start) continue;

        $items[] = array(
            'title' => $title,
            'detail' => sanitize_text_field($item['detail'] ?? ''),
            'start' => $start,
            'end' => $end,
            'days' => $days,
            'type' => in_array($type, $allowed_types, true) ? $type : 'other',
        );
    }

    return $items ? wp_json_encode($items) : '';
}

function soundbridge_get_program_schedule_items($post_id): array
{
    $items = json_decode((string) get_post_meta($post_id, 'sb_schedule_items', true), true);
    return is_array($items) ? $items : array();
}

function soundbridge_format_schedule_time($time): string
{
    $timestamp = strtotime((string) $time);
    return false === $timestamp ? (string) $time : wp_date('g:i A', $timestamp);
}

/** Convert schedule items into rows and calculated table row spans. */
function soundbridge_build_program_schedule_matrix(array $items): array
{
    $all_days = soundbridge_program_schedule_days();
    $used_days = array();
    $boundaries = array();

    foreach ($items as $item) {
        foreach ((array) ($item['days'] ?? array()) as $day) {
            if (isset($all_days[$day])) $used_days[$day] = true;
        }
        if (!empty($item['start'])) $boundaries[] = $item['start'];
        if (!empty($item['end'])) $boundaries[] = $item['end'];
    }

    $days = array_intersect_key($all_days, $used_days);
    $boundaries = array_values(array_unique($boundaries));
    sort($boundaries, SORT_STRING);
    $rows = array();

    for ($index = 0, $last = count($boundaries) - 1; $index < $last; $index++) {
        $start = $boundaries[$index];
        $end = $boundaries[$index + 1];
        $cells = array();

        foreach ($days as $day => $label) {
            $cell = null;
            foreach ($items as $item) {
                if (!in_array($day, (array) ($item['days'] ?? array()), true)) continue;
                if (($item['start'] ?? '') === $start) {
                    $end_index = array_search($item['end'] ?? '', $boundaries, true);
                    $cell = array('item' => $item, 'rowspan' => max(1, (int) $end_index - $index));
                    break;
                }
                if (($item['start'] ?? '') < $start && ($item['end'] ?? '') > $start) {
                    $cell = array('skip' => true);
                    break;
                }
            }
            $cells[$day] = $cell;
        }

        $rows[] = array('start' => $start, 'end' => $end, 'cells' => $cells);
    }

    return array('days' => $days, 'rows' => $rows);
}

function soundbridge_render_program_schedule_builder($post_id): void
{
    $items = soundbridge_get_program_schedule_items($post_id);
    ?>
    <div class="sb-program-field sb-program-field--wide sb-schedule-builder">
        <strong><?php esc_html_e('Detailed schedule', 'soundbridge-blocks'); ?></strong>
        <span class="description"><?php esc_html_e('Add an activity, select its days, and provide start and end times. Multi-day activities only need to be entered once.', 'soundbridge-blocks'); ?></span>
        <input type="hidden" name="sb_schedule_items" id="sb-schedule-items" value="<?php echo esc_attr(wp_json_encode($items)); ?>">
        <div id="sb-schedule-builder-rows"></div>
        <button type="button" class="button button-secondary" id="sb-schedule-add"><?php esc_html_e('Add schedule item', 'soundbridge-blocks'); ?></button>
        <details class="sb-schedule-legacy">
            <summary><?php esc_html_e('Legacy schedule fallback', 'soundbridge-blocks'); ?></summary>
            <span class="description"><?php esc_html_e('Existing pipe-separated schedule content remains available as a fallback when no structured items exist.', 'soundbridge-blocks'); ?></span>
            <textarea name="sb_schedule_details" rows="5" placeholder="Day | Time | Description"><?php echo esc_textarea(get_post_meta($post_id, 'sb_schedule_details', true)); ?></textarea>
        </details>
    </div>
    <style>
        .sb-schedule-builder { gap: 10px; }
        .sb-schedule-builder__row { position: relative; display: grid; grid-template-columns: minmax(180px, 1.5fr) repeat(2, minmax(110px, .65fr)) minmax(150px, 1fr); gap: 12px; padding: 18px; background: #f6f7f7; border: 1px solid #dcdcde; border-radius: 6px; }
        .sb-schedule-builder__row + .sb-schedule-builder__row { margin-top: 12px; }
        .sb-schedule-builder__row label { display: flex; flex-direction: column; gap: 4px; }
        .sb-schedule-builder__row label > span, .sb-schedule-builder__days > span { font-size: 12px; font-weight: 600; }
        .sb-schedule-builder__detail, .sb-schedule-builder__days, .sb-schedule-builder__actions { grid-column: 1 / -1; }
        .sb-schedule-builder__days { display: flex; flex-wrap: wrap; gap: 8px 14px; }
        .sb-schedule-builder__days > span { width: 100%; }
        .sb-schedule-builder__days label { display: inline-flex; flex-direction: row; align-items: center; }
        .sb-schedule-builder__actions { display: flex; gap: 6px; justify-content: flex-end; }
        .sb-schedule-legacy { padding-top: 8px; }
        .sb-schedule-legacy summary { margin-bottom: 8px; cursor: pointer; font-weight: 600; }
        .sb-schedule-legacy textarea { margin-top: 8px; }
        @media (max-width: 1000px) { .sb-schedule-builder__row { grid-template-columns: 1fr 1fr; } }
    </style>
    <script>
        (() => {
            const input = document.getElementById('sb-schedule-items');
            const rows = document.getElementById('sb-schedule-builder-rows');
            const addButton = document.getElementById('sb-schedule-add');
            if (!input || !rows || !addButton) return;

            const dayOptions = <?php echo wp_json_encode(soundbridge_program_schedule_days()); ?>;
            const typeOptions = <?php echo wp_json_encode(soundbridge_program_schedule_types()); ?>;
            let items;
            try { items = JSON.parse(input.value || '[]'); } catch (error) { items = []; }

            const sync = () => { input.value = JSON.stringify(items); };
            const render = () => {
                rows.innerHTML = '';
                items.forEach((item, index) => {
                    const row = document.createElement('div');
                    row.className = 'sb-schedule-builder__row';
                    row.innerHTML = `
                        <label><span>Activity</span><input type="text" data-key="title"></label>
                        <label><span>Start time</span><input type="time" data-key="start"></label>
                        <label><span>End time</span><input type="time" data-key="end"></label>
                        <label><span>Activity type</span><select data-key="type"></select></label>
                        <label class="sb-schedule-builder__detail"><span>Secondary detail (optional)</span><input type="text" data-key="detail" placeholder="12:00 PM recital"></label>
                        <div class="sb-schedule-builder__days"><span>Days</span></div>
                        <div class="sb-schedule-builder__actions"><button type="button" class="button" data-action="up">↑</button><button type="button" class="button" data-action="down">↓</button><button type="button" class="button-link-delete" data-action="remove">Remove</button></div>`;

                    row.querySelectorAll('[data-key]').forEach((field) => {
                        const key = field.dataset.key;
                        if (key === 'type') {
                            Object.entries(typeOptions).forEach(([value, label]) => field.add(new Option(label, value)));
                        }
                        field.value = item[key] || (key === 'type' ? 'other' : '');
                        field.addEventListener('input', () => { items[index][key] = field.value; sync(); });
                    });

                    const days = row.querySelector('.sb-schedule-builder__days');
                    Object.entries(dayOptions).forEach(([value, label]) => {
                        const day = document.createElement('label');
                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.checked = (item.days || []).includes(value);
                        checkbox.addEventListener('change', () => {
                            item.days = Object.keys(dayOptions).filter((key) => key === value ? checkbox.checked : (item.days || []).includes(key));
                            sync();
                        });
                        day.append(checkbox, document.createTextNode(label));
                        days.append(day);
                    });

                    row.querySelector('[data-action="up"]').disabled = index === 0;
                    row.querySelector('[data-action="down"]').disabled = index === items.length - 1;
                    row.addEventListener('click', (event) => {
                        const action = event.target.dataset.action;
                        if (!action) return;
                        if (action === 'remove') items.splice(index, 1);
                        if (action === 'up' && index > 0) [items[index - 1], items[index]] = [items[index], items[index - 1]];
                        if (action === 'down' && index < items.length - 1) [items[index + 1], items[index]] = [items[index], items[index + 1]];
                        sync();
                        render();
                    });
                    rows.append(row);
                });
            };

            addButton.addEventListener('click', () => {
                items.push({ title: '', detail: '', start: '09:00', end: '10:00', days: [], type: 'other' });
                sync();
                render();
            });
            render();
        })();
    </script>
    <?php
}
