<?php
session_start();

define("KLARNA_ENV", "playground"); 
define("KLARNA_REGION", "na");

define("KLARNA_USERNAME", "TU_USERNAME_KLARNA");
define("KLARNA_PASSWORD", "TU_PASSWORD_KLARNA");

define("KLARNA_BASE_URL", "https://api-na.playground.klarna.com");

define("KLARNA_SUCCESS_URL", "https://solutionsrentcar.do/WEB/klarna/success.php");
define("KLARNA_CANCEL_URL", "https://solutionsrentcar.do/WEB/klarna/cancel.php");
define("KLARNA_BACK_URL", "https://solutionsrentcar.do/WEB/reservation.php");