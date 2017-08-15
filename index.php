<?php
// use this because some configuration problems on this server
$post = file_get_contents("php://input");

require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use MessageBird\Command\RunCommand;
$obj = new RunCommand($post);
echo $obj->execute();
