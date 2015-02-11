<?php namespace Inkwell\Controller
{
	interface NegotiatorConsumerInterface
	{
		public function setLanguageNegotiator($negotiator);
		public function setMimeTypeNegotiator($negotiator);
	}
}
