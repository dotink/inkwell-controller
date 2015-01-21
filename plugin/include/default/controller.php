<?php

	return Affinity\Action::create(['core', 'routing'], function($app, $container) {
		$app['resolver'] = $container->make('Inkwell\Routing\ResolverInterface');
	});
