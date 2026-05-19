<?php
/**
 * Configuración local — NO se sube a Git.
 *
 * Cómo usarlo en Hostinger:
 *  1. hPanel → Administrador de archivos → /public_html/
 *  2. Crea un archivo nuevo llamado: config.local.php
 *  3. Pega el contenido de abajo y cambia los valores reales.
 *  4. Guarda. El sitio empieza a usar estos valores inmediatamente.
 *
 * Si no creas este archivo, el sitio usa los valores por defecto del código.
 */

// Base de datos MySQL
$_ENV['DB_HOST'] = 'srv500.hstgr.io';
$_ENV['DB_PORT'] = '3306';
$_ENV['DB_USER'] = 'u144787244_solutionsrent';
$_ENV['DB_PASS'] = 'PON_AQUI_EL_PASSWORD_NUEVO';
$_ENV['DB_NAME'] = 'u144787244_solutionsrent';

// JWT — genera uno con:  php -r "echo bin2hex(random_bytes(48));"
$_ENV['JWT_SECRET'] = 'PON_AQUI_UN_SECRET_LARGO_ALEATORIO';
