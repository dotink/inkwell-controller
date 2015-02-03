<?php namespace Inkwell\Controller
{
	use Auryn;
	use Closure;
	use Inkwell\Routing;

	class Resolver implements Routing\ResolverInterface
	{
		/**
		 *
		 */
		public function __construct(Auryn\Provider $broker)
		{
			$this->broker = $broker;
		}

		/**
		 *
		 */
		public function resolve($action, Array $context)
		{
			$reference = FALSE;

			if (is_string($action)) {
				if (strpos($action, '::') !== FALSE) {
					list($class, $action) = explode('::', $action);

					if (!class_exists($class) || !is_callable([$class, $action])) {
						$context['router']->defer();
					}

					$controller           = $this->broker->make($class);
					$reference            = [$controller, $action];

				} elseif (function_exists($action)) {
					$reference = $action;
				}

			} elseif ($action instanceof Closure) {
				$controller           = $this->broker->make('Inkwell\Controller\BaseController');
				$action               = $action->bindTo($controller, $controller);
				$reference            = $controller;

			}

			if (isset($controller)) {
				$controller->__prepare($action, $context);
			}

			return $reference;
		}
	}
}
