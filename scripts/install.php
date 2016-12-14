<?php

$whcms_ip = getenv('WHMCS_IP');

if (!$passwd && !$smtp_user && !$ingress) die;

$conf = '/var/opt/persistent/configuration.php';
if (!file_exists($conf)) die;
require_once $conf;

require_once '/var/opt/persistent/modules/addons/KuberDock.php';
$kd_config = KuberDock_config();
$plugin_version = $kd_config['version'];

try {
    $db = new PDO("mysql:host=${db_host};dbname=${db_name}", $db_username, $db_password);

    $sql = "UPDATE tblconfiguration SET VALUE=? WHERE setting='SystemURL'";
    $st = $db->prepare($sql)->execute(array($whcms_ip));

    $sql = "INSERT INTO tbladdonmodules VALUES ('KuberDock', 'version', ?), ('KuberDock', 'access', 1);";
    $st = $db->prepare($sql)->execute(array($plugin_version));

    $sql = "INSERT INTO tblconfiguration(setting, value) VALUES 
              ('ActiveAddonModules', 'KuberDock'), 
              ('AddonModulesPerms', 'a:1:{i:1;a:1:{s:9:\"KuberDock\";s:15:\"KuberDock addon\";}}');";
    $st = $db->prepare($sql)->execute();

    $sql = "INSERT INTO `tblpaymentgateways` VALUES 
              ('mailin','name','Mail In Payment',1),
              ('mailin','type','Invoices',0),
              ('mailin','visible','on',0),
              ('mailin','instructions','Bank Name:\r\nPayee Name:\r\nSort Code:\r\nAccount Number:',0),
              ('mailin','convertto','',0);";
    $st = $db->prepare($sql)->execute();

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}