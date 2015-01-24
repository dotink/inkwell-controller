<?php namespace Inkwell\Controller
{
	interface NegotiatorInterface
	{
		public function setLanguageNegotiator($negotiator);
		public function setMimeTypeNegotiator($negotiator);
	}
}
