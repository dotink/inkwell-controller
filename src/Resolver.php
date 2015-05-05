<?php namespace Inkwell\Controller
{
	use Auryn;
	use Closure;
	use Inkwell\Routing;
	use Dotink\Flourish;

	class Resolver implements Routing\ResolverInterface
	{
		const CONTROLLER_CLASS = 'Inkwell\Controller\BaseController';


		/**
		 *
		 */
		private $broker = NULL;


		/**
		 *
		 */
		public function __construct(Auryn\Injector $broker = NULL)
		{
			$this->broker = $broker;
		}


		/**
		 * Execute a resolved action
		 *
		 * @access public
		 * @param mixed $action A callable or callable representation of the action
		 * @return mixed The result of the action being executed
		 */
		public function execute($action)
		{
			return $this->broker
				? $this->broker->execute($action)
				: $action();
		}


		/**
		 *
		 */
		public function resolve($action, Array $context)
		{
			$controller = NULL;
			$callable   = FALSE;
			$router     = $context['router'];

			if (is_string($action)) {
				if (strpos($action, '::') !== FALSE) {

					//
					// The string appears to be a Class::method callback, so we'll try to
					// instantiate the class and return a more formal callable.
					//

					list($class, $action) = explode('::', $action);

					if (!is_callable([$class, $action])) {
						$router->defer();

					} elseif ($this->broker) {
						$controller = $this->broker->make($class);

					} else {
						$controller = new $class();
					}

					$callable = [$controller, $action];

				} elseif (function_exists($action)) {

					//
					// The string appears to be a function so we'll use it as our callable
					//

					$callable = $action;

				} else {

					//
					// The default response is false, which indicates that no action should
					// attempt to be executed.  This will leave whatever the string was as the
					// response body.
					//

				}

			} elseif ($action instanceof Closure) {

				//
				// If the route target is a closure, we're going to instantiate a base controller
				// and make use of prepare/__invoke to execute it.
				//

				$controller = $callable = $this->broker->make(self::CONTROLLER_CLASS);
				$action     = function() use ($action, $controller) {
					return $this->execute($action->bindTo($controller));
				};

			} else {
				throw new Flourish\ProgrammerException(
					'Invalid router target %s', $action
				);
			}

			if (is_a($controller, self::CONTROLLER_CLASS)) {
				if (is_string($action) && method_exists(self::CONTROLLER_CLASS, $action)) {
					$router->defer();
				}

				$controller->prepare($action, $context);
			}

			return $callable;
		}
	}
}
