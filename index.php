<?php
/* Setup Include Path */
set_include_path(__DIR__.':'.__DIR__ . '/lib:' . get_include_path());

/* Run Autoload Function */
include('lib/tcpro/Autoload.php');

/* Where to place the views */
\tcpro\Fw::$viewDirectory = __DIR__.'/view';

/* Namespace used for controllers */
\tcpro\Fw::$controllerNamespace = '\controller';


/* Run */
\tcpro\Fw::run();

