<?php
  class Env{
    static $database = [
        'DB_HOST' => 'localhost',
        'DB_USERNAME' => 'root',
        'DB_PASSWORD' => '',
        'DB_NAME' => 'macehub-url-shortner',
        'DB_PORT' => '3306',
    ];
    // 
    static $credentials = [
      // 'your email' => '<hashed password_hash("<yourpassword>", PASSWORD_DEFAULT)>"
      // Example
      'email@domain.com' => '$2y$10$x7wn1bPDjnPprWnaiX2Pm.nDv2ndtPQgfWkXHOKpIHYZdvIYQTzLy'
    ];
  }
?>