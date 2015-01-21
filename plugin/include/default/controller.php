<?php

	return Affinity\Action::create(['core', 'routing'], function($app, $broker) {
		$app['resolver'] = $broker->make('Inkwell\Routing\ResolverInterface');
	});
