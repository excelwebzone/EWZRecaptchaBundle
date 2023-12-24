#!/usr/bin/env php
<?php

$version = "v1.6.1";
$message = "ready for symfony 7 - fixed naming";


echo("Running tests:\n");
system("vendor/bin/phpunit --testdox", $res);
if ($res>0) {
  echo("\nError during execution test scripts. Releasing cannceled.\n");
  return 1;
}

$res = shell_exec('git add .');
$res = shell_exec('git commit -m "' . $message . '"');
$res = shell_exec('git push');

$res = shell_exec('git tag -a ' . $version . ' -m "' . $message . '"');
$res = shell_exec('git push origin ' . $version);

?>
