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
 * Change password form definition.
 *
 * @package    core
 * @subpackage auth
 * @copyright  2006 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once $CFG->libdir.'/formslib.php';

class login_overwrite_password_form extends moodleform {

    function definition() {
        global $USER, $CFG;

        // Prepare a string showing whether the site wants login password autocompletion to be available to user.
        if (empty($CFG->loginpasswordautocomplete)) {
            $autocomplete = 'autocomplete="on"';
        } else {
            $autocomplete = '';
        }

        $mform = $this->_form;
        $mform->setDisableShortforms(true);
        $mform->addElement('header', 'setpassword', get_string('setpassword'), '');

        // Include the username in the form so browsers will recognise that a password is being set.
 $mform->addElement('text', 'username', '', 'style="display: none;" ' . $autocomplete);
        $mform->setType('username', PARAM_RAW);
        // Token gives authority to change password.
        $mform->addElement('hidden', 'token', '');
        $mform->setType('token', PARAM_ALPHANUM);

        // visible elements
        $mform->addElement('static', 'username2', get_string('username'), $USER->username);

         $policies = array();
        if (!empty($CFG->passwordpolicy)) {
            $policies[] = print_password_policy();
        }
        if (!empty($CFG->passwordreuselimit) and $CFG->passwordreuselimit > 0) {
            $policies[] = get_string('informminpasswordreuselimit', 'auth', $CFG->passwordreuselimit);
        }
        if ($policies) {
            $mform->addElement('static', 'passwordpolicyinfo', '', implode('<br />', $policies));
        }

        $mform->addElement('password', 'newpassword1', get_string('newpassword'), $autocomplete);
        $mform->addRule('newpassword1', get_string('required'), 'required', null, 'client');
        $mform->setType('newpassword1', PARAM_RAW);

        $strpasswordagain = get_string('newpassword') . ' (' . get_string('again') . ')';
        $mform->addElement('password', 'newpassword2', $strpasswordagain, $autocomplete);
        $mform->addRule('newpassword2', get_string('required'), 'required', null, 'client');
        $mform->setType('newpassword2', PARAM_RAW);

        $this->add_action_buttons(true);
    }
/// perform extra password change validation
    function validation($data, $files) {
        global $USER;
        $errors = parent::validation($data, $files);

        if ($data['newpassword1'] <> $data['newpassword2']) {
            $errors['newpassword1'] = get_string('passwordsdiffer');
            $errors['newpassword2'] = get_string('passwordsdiffer');
            return $errors;
        }

        $errmsg = '';//prevents eclipse warnings
        if (!check_password_policy($data['newpassword1'], $errmsg)) {
            $errors['newpassword1'] = $errmsg;
            $errors['newpassword2'] = $errmsg;
            return $errors;
        }

        return $errors;
    }
}
