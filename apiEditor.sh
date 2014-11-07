#!/bin/bash
php public/index.php development enable
php -S 0.0.0.0:8888 -t public public/index.php
php public/index.php development disable
