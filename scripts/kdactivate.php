<?php

require 'init.php';
require 'modules/addons/KuberDock/KuberDock.php';

KuberDock_activate();
\models\billing\Config::addAllowedApiIP('node', '192.168.120.31');