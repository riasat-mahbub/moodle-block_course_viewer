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
 * Version details
 *
 * @package    block_course_viewer
 * @copyright  2022 Riasat Mahbub (riasat.mahbub@brainstation-23.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_course_viewer extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_course_viewer');
    }

    function get_content() {
        global $DB, $USER, $OUTPUT;

        $content = [];
        $courses = $DB->get_records('course');


        foreach ($courses as $course) {

            $context =  context_course::instance($course->id);
            if (is_siteadmin($USER->id) || is_enrolled($context, $USER->id, 'mod/assign:grade', true)) {
                $course_link = new moodle_url("/course/view.php?id=".$course->id);

                // students cannot create resources
                $resource_viewers = get_enrolled_users($context, 'mod/resource:view');
                $resource_creators = get_enrolled_users($context, 'mod/resource:addinstance');

                $number_enrolled = count($resource_viewers)-count($resource_creators);

                array_push($content, array("course_link"=>$course_link,"fullname"=>$course->fullname, "number_enrolled"=>$number_enrolled));
            }
        }


        $templatecontext = (object)[
            'content' => $content
        ];

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = $OUTPUT->render_from_template('block_course_viewer/course_viewer', $templatecontext);

        return $this->content;
    }
}
