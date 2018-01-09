<?php
/**
 * Values in this file are modifiable, don't touch any other php file,
 * except if you know what you are doing.
 */
// General settings
define('DEBUG', False); /* Show debug messages */

// DB settings
define('DB_HOSTNAME', 'mysql');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_DATABASE', 'odk');

// Menu
define('MENU_BRAND', 'Βοηθός Κατανομής Κενών');
define('MENU_INSTITUTIONS', 'Σχολεία και κενά');
define('MENU_APPLICANTS', 'Αιτήσεις υποψηφίων');
define('MENU_RESOLVE', 'Προτεινόμενη κατανομή κενών');
define('MENU_RESET_JOBS', 'Διαγραφή κατανομής');
define('MENU_RESET_ALL', 'Διαγραφή όλων');

// common translations
define('EXPORT_CSV', 'Αποθήκευση σε CSV (Excel)');
define('CHOICE', 'Προτίμηση');

// head labels
define('HEAD_INSTITUTIONS', 'Σχολεία');
define('HEAD_POSITIONS', 'Κενά');
define('HEAD_APPLICANTS', 'Αιτούντες');
define('HEAD_POINTS', 'Μόρια');
define('HEAD_ACTIONS', ' ');

// applicants.php
/** Number of Choices per applicant */
define('APPL_NUMBER_OF_CHOICES', 5);

// index.php
define('INDEX_HELP', 'Αυτή η εφαρμογή δημιουργεί προτάσεις για την κατανομή κενών σε υποψηφίους.<br/><br/> <u>Πως λειτουργεί</u><br/>α. Εισάγετε τα σχολεία με τα κενά τους<br/>β. Εισάγετε τις αιτήσεις των υποψηφίων με τα μόρια και τις προτιμήσεις τους<br/>γ. Βλέπετε την προτεινόμενη κατανομή κενών<br/><br/>-->Κάθε φορά που κάνετε αλλαγές στις αιτήσεις υποψηφίων, αλλάζει αυτόματα και προτεινόμενη κατανομή.<br/>--> Αν θέλετε να ξεκινήσετε από την αρχή, επιλέξτε «Διαγραφή όλων».');
?>