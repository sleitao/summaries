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
 * Defines the version of summaries
 *
 * This code fragment is called by moodle_needs_upgrading() and
 * /admin/index.php
 *
 * @package    mod
 * @subpackage summaries
 * @copyright  2012 Nuno Barbosa, Susana Leitão
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

//$plugin->version   = 0;               // If version == 0 then module will not be installed
$plugin->version   = 2017061900;      // The current module version (Date: YYYYMMDDXX)
$plugin->requires  = 2010031900;      // Requires this Moodle version
$plugin->cron      = 0;               // Period for cron to check this module (secs)
$plugin->component = 'mod_summaries'; // To check on upgrade, that module sits in correct place
