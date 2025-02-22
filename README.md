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

This project is licensed under the MIT License.
