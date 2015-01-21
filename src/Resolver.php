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
		public function __construct(Auryn\Provider $container)
		{
			$this->container = $container;
		}

		/**
		 *
		 */
		public function resolve($action, Array $context)
		{
			if ($action instanceof Closure) {
				$class      = 'Inkwell\Controller\BaseController';
				$controller = $this->container->make($class);
				$action     = $action->bindTo($controller, $controller);
				$reference  = [$controller, '{closure}'];

			} elseif (is_array($action)) {
				$class      = $action[0];
				$action     = $action[1];
				$controller = $this->container->make($class);
				$reference  = [$controller, $action];

			} else {
				$reference  = [$action];
			}

			if (isset($controller)) {
				$controller->__prepare($action, $context);
			}

			return $reference;
		}
	}
}
