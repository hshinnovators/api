<?php
/**
 * @author: James Dryden <james.dryden@kentprojects.com>
 * @license: Copyright KentProjects
 * @link: http://kentprojects.com
 *
 * Class InputEmail
 * This represents an input that accepts email input.
 */
class InputEmail extends Input
{
	public function __construct($name, array $attributes = array())
	{
		parent::__construct($name, array_merge($attributes, array(
			"type" => "email"
		)));
	}

	/**
	 * Validate a particular value against this field.
	 *
	 * @param mixed $value
	 * @throws FormException
	 * @return bool
	 */
	public function validate($value)
	{
		return Validate::Email($value);
	}
}