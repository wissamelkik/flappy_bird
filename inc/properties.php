<?php
// Session
if(session_id() == '') session_start();

// Security
define("ENCRYPTION_SALT", "FlaPpY BiRd !s aWeS0me");

// String Separation in AJAX call
define("STRING_SEPARATOR_IN_AJAX_CALL", ";;;;;");

// Timezone
date_default_timezone_set("Asia/Beirut");

// MySQL Database Connection
define("DB_HOST", "localhost");
define("DB_USER", "");
define("DB_PASSWORD", "");
define("DB_NAME", "flappy_bird");
?>