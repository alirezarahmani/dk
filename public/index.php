<?php
opcache_reset();
require '/var/www/vendor/autoload.php';

use Boot\Start;
use Boot\Router;
Start::create()->explode();
Router::routes();
