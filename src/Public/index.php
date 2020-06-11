<?php
require getenv('LOCAL_DEV_DIR') . '/vendor/autoload.php';

use Boot\Start;
use Boot\Router;

Start::create()->explode();
Router::routes();
