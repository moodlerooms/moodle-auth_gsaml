<?php
/**
 * Copyright (C) 2010  Moodlerooms Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://opensource.org/licenses/gpl-3.0.html.
 *
 * @copyright  Copyright (c) 2009 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://opensource.org/licenses/gpl-3.0.html     GNU Public License
 */

/**
 * Default auth_saml controller
 *
 * @author Chris Stones
 * @package auth_gsaml
 */
defined('MOODLE_INTERNAL') or die('Direct access to this script is forbidden.');

class auth_gsaml_controller_default extends mr_controller_block {

    /**
     * Require capability for viewing this controller
     */
    public function require_capability() {
        // Require admin for our admin action
        switch ($this->action) {
            case 'admin':
                require_capability('moodle/site:config', $this->get_context());
                break;
            default:
                require_capability('moodle/site:config', $this->get_context());
        }
    }

    /**
     * Define tabs for all controllers
     */
    public static function add_tabs($controller, &$tabs) {
        $tabs->toptab('status',   array('controller' => 'default','action' => 'view'))
             ->toptab('logs',     array('controller' => 'default','action' => 'logs'));
             //->toptab('ssotests', array('controller' => 'default','action' => 'ssotests'))
             //->toptab('docs',     array('controller' => 'default','action' => 'docs'));


    }

    /**
     * Default view is the status
     */
    public function view_action() {
        global $CFG, $COURSE, $OUTPUT, $PAGE, $USER;

        // Default view is just the status information
        $this->tabs->set('status');
        $this->print_header();

        print $OUTPUT->box_start('generalbox boxaligncenter');

        // Let's make sure we can see our config settings when checking
        // GSaml Authentication SSO Settings
        if(!$gsaml_conf = get_config('auth/gsaml')) {
            print $OUTPUT->notification(get_string('gsamlconfignotset','auth_gsaml'));
        } else {
            $this->print_config_table(get_string('googlesamlconfigdata','auth_gsaml'),$gsaml_conf);
        }

        print $OUTPUT->box_end();
        $this->print_footer();       
    }

    public function logs_action() {
        global $OUTPUT,$PAGE,$CFG;
        $this->tabs->set('logs');
        
        require_once($CFG->dirroot.'/auth/gsaml/report/gsamllogs.php');
        $report = new auth_gsaml_report_gsamllogs($this->url);//, $COURSE->id);
        
        $this->print_header();
        print $this->mroutput->render($report);
        $this->print_footer();
    }

    public function ssotests_action() {
        $this->tabs->set('ssotests');
        $this->print_header();
        print "tests to run to check authentication with google via the saml protocoal";
        $this->print_footer();
    }

    /**
     * View this PHPDoc Generated Content.
     *
     * @global object $CFG
     * @global object $COURSE
     * @global object $OUTPUT
     */
    public function docs_action() {
        global $CFG,$COURSE,$OUTPUT;
        $this->tabs->set('docs');
        $this->print_header();

        // include graphics

        // Also output phpdoc generated docs
        print $this->output->heading("Gapps Documentation");
        print $OUTPUT->box_start('generalbox boxaligncenter');
        //$str = '<iframe src="'.$CFG->wwwroot.'/blocks/gapps/docs/index.html'.'" width="100%" height="600" align="center"> </iframe>';
        //print $str;
        print $OUTPUT->box_end();


        $this->print_footer();
    }


    public function print_config_table($heading,$table_obj) {
        global $OUTPUT;

        print $OUTPUT->heading($heading);//, $size, $class, $id);
        $conf_table = new html_table();
        $conf_table->head  = array('Setting','Value');
        $conf_table->align = array('left','left');
        $conf_table->data  = array();

        foreach( $table_obj as $setting => $value ) {
            $conf_table->data[] = array($setting,$value);
        }
        print html_writer::table($conf_table);
    }
}