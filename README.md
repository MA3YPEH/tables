# collaborative tables #

## About ##

A plugin for Moodle that allows you to work with spreadsheets in real time using a web socket.

## Features ##

- Allows you to add a spreadsheet to a Moodle course as an activity.
- Allows you to administer access to the cells in the spreadsheet.
- Allows you to restrict viewing the contents of the table cells.
- Allows you to grade student work in the cells of the spreadsheet.
- Allows you to upload quiz completion data to the spreadsheet.

## Authors and Contributors ##

Advisor and minor contributor: Vladimir A. Parkhomenko Senior Lecturer at SPbPU ICSC

Main Contributor: Egor N. Mazur Student at SPbPU ICSC

## Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/mod/tables

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.

## License ##

2023 Mazur Egor <mazur.eh@edu.spbstu.ru>

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.
