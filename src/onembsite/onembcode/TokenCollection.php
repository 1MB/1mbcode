<?php
declare(strict_types=1);
namespace onembsite\onembcode;

class TokenCollection {

	/**@var int*/
	protected $current = 0;

	/**@var array*/
	protected $tokens = [];

	public function __construct(array $tokens)
	{
		$this->tokens = $tokens;
	}

	public function next(string $skip = 'T_WHITESPACE', int $add = 0)
	{
		if(isset($this->tokens[$this->current + $add]))
		{
			if(in_array($skip, explode('|', $this->tokens[$this->current + $add]->type)))
			{
				$this->current += $add > 0 ? $add : 1;
				return $this->next();
			}

			$return = $this->tokens[$this->current + $add];
			$this->current += $add > 0 ? $add : 1;
			return $return;
		}

		return null;
	}

	public function findNext(string $type)
	{
		for($i = ++$this->current; $i < count($this->tokens); $i++)
		{
			if(in_array($type, explode('|', $this->tokens[$i]->type)))
			{
				return $this->tokens[$i];
			}
		}

		return null;
	}

	public function currentIndex()
	{
		return $this->current;
	}

	public function setIndex(int $index)
	{
		return $this->current = ($index + 1);
	}
}