<?php namespace Inkwell\Controller
{
	use Auryn;
	use Closure;
	use Inkwell\Routing;

	class Resolver implements Routing\ResolverInterface
	{
		const CONTROLLER_CLASS = 'Inkwell\Controller\BaseController';

		/**
		 *
		 */
		public function __construct(Auryn\Injector $broker = NULL)
		{
			$this->broker = $broker;
		}

		/**
		 *
		 */
		public function resolve($action, Array $context)
		{
			$controller = NULL;
			$callback   = FALSE;
			$broker     = $this->broker;

			if (is_string($action)) {
				if (strpos($action, '::') !== FALSE) {
					list($class, $action) = explode('::', $action);

					if (!class_exists($class) || !is_callable([$class, $action])) {
						$context['router']->defer();

					} elseif ($broker) {
						$controller = $broker->make($class);

					} else {
						$controller = new $class();
					}

					$callback = [$controller, $action];

				} elseif (function_exists($action)) {
					$callback = $action;
				}

			} elseif ($action instanceof Closure) {
				$controller = $broker->make(self::CONTROLLER_CLASS);
				$callback   = $controller;
				$action     = Closure::bind(function() use ($broker, $action, $controller) {
					$action = $action->bindTo($controller);

					return $broker
						? $broker->execute($action)
						: $action();

				}, $controller);

			}

			if (isset($controller) && is_a($controller, self::CONTROLLER_CLASS)) {
				$controller->__prepare($action, $context);
			}

			return $callback;
		}
	}
}
