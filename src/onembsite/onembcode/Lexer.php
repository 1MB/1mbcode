<?php
declare(strict_types=1);
namespace onembsite\onembcode;

class Lexer {

	/**@var array*/
	protected $source;

	/**@var array*/
	protected $tokens = [];

	/**@var array*/
	protected $_terminals = [];

	/**
	*	Lexer Constructor
	*	@param array $source
	*	@return void
	*/
	public function __construct(array $source)
	{
		$this->source = $source;
	}

	public function add(string $regex, string $type)
	{
		$this->_terminals[$regex] = $type;
		return $this;
	}

	public function reset()
	{
		$this->_terminals = [];
		return $this;
	}

	/**
	*	Converts script to tokens and returns them
	*	@return array
	*/
	public function run()
	{
		return $this->_tokens();
	}

	/**
	*	Parses script to tokens
	*/
	protected function _tokens()
	{
		$tokens = [];

	    foreach($this->source as $number => $line) {            
	        $offset = 0;
	        while($offset < strlen($line)) {
	            $result = static::_match($line, $number, $offset);
	            if($result === false) {
	                throw new \Exception('Parse error: Unexpected ' . substr($line, $offset) . ' on line ' . ((int)$line + 1));
	            }

	            $result->line = ((int)$line + 1);
	            $result->index = count($tokens) - 1;
	            $tokens[] = $result;
	            $offset += strlen($result->match);
	        }
	    }

	    return $this->tokens = new TokenCollection($tokens);
	}

	/**
	*	Matches lines to terminal patterns
	*
	*	@param string $line
	*	@param int $number
	*	@param int $offset
	*
	*	@return object|bool
	*/
	protected function _match(string $line, int $number, int $offset) {
	    $string = substr($line, $offset);
	    foreach($this->_terminals as $pattern => $name) {
	        if(preg_match($pattern, $string, $matches)) {
	            return (object) [
	                'match' => $matches[1],
	                'type' => $name,
	                'line' => $number + 1
	            ];
	        }
	    }

	    return false;
	}

	/**
	*	Throws an exception
	*
	*	@param string $message
	*	@throws \Exception
	*/
	protected function _exception(string $message)
	{
		throw new \Exception($message);
	}
}