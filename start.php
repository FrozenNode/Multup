<?php

Autoloader::namespaces(array(
	'Multup' => Bundle::path('multup').'libraries'
));

Autoloader::map(array(
	'Multup' =>  __DIR__.'/multup.php'
));
