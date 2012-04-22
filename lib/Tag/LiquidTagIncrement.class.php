<?php

/**
 * Used to increment a counter into a template
 * 
 * @example 
 * {% increment value %}
 *
 * @author Viorel Dram
 *
 * @package Liquid
 */
class LiquidTagIncrement extends LiquidTag
{
	/**
	 * Name of the variable to increment
	 *
	 * @var string
	 */
	private $_toIncrement;
	private $_incrementWith;

	/**
	 * Constructor
	 *
	 * @param string $markup
	 * @param array $tokens
	 * @param LiquidFileSystem $file_system
	 * @return AssignLiquidTag
	 */
	public function __construct($markup, &$tokens, &$file_system)
	{
		$syntax = new LiquidRegexp("/(".LIQUID_ALLOWED_VARIABLE_CHARS."+)(\s+(with)\s+(".LIQUID_QUOTED_FRAGMENT."+))?/");

		if ($syntax->match($markup))
		{
			if (isset($syntax->matches[4]))
			{
				$this->_toIncrement = $syntax->matches[1];
				$this->_incrementWith = $syntax->matches[4];
			}
			else
			{
				$this->_toIncrement = $syntax->matches[0];
			}
		}
		else
		{
			throw new LiquidException("Syntax Error in 'increment' - Valid syntax: increment [var]");
		}
	}

	/**
	 * Renders the tag
	 *
	 * @param LiquidContext $context
	 */
	public function render(&$context)
	{
		// if the value is not set in the environment check to see if it
		// exists in the context, and if not set it to -1
		if (! isset($context->environments[0][$this->_toIncrement]))
		{
			// check for a context value
			$from_context = $context->get($this->_toIncrement);
			
			// we already have a value in the context
			$context->environments[0][$this->_toIncrement] = (null !== $from_context) ? $from_context : -1;
		}

		$increment_with = 1;

		// if the increment is a number add it to our value
		if (is_numeric($this->_incrementWith))
		{
			$increment_with = $this->_incrementWith;
		}

		// check if the increment is actually a context variable
		if (null !== ($tmp = $context->get($this->_incrementWith)))
		{
			$increment_with = $tmp;
		}

		// increment the value
		$context->environments[0][$this->_toIncrement] += $increment_with;
	}
}