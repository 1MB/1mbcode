<?php
declare(strict_types=1);
namespace onembsite\onembcode;

use onembsite\onembcode\Lexer;

class Parser {

	/** @var string*/
	protected $source;

	/**@var Lexer*/
	protected $lexer;

	/**@var array*/
	protected $variables = [];

	/**@var array*/
	protected $functions = [];

	/**
	*	Parser Constructor
	*	@param string $source
	*	@return void
	*/
	public function __construct(string $source, array $functions = [])
	{
		$this->source = $source;
		$this->functions = $functions;
		$this->lexer = new Lexer(explode(PHP_EOL, $source));
		$this->loadDefaultFunctions();
	}

	protected function loadDefaultFunctions()
	{
		$this->functions['print'] = function($mixed) { 
			echo is_array($mixed) ? json_encode($mixed) : (string) $mixed;
		};
		$this->functions['dump'] = function($mixed) { 
			var_dump($mixed);
		};
		$this->functions['get_url'] = function($url) { 
			if(!filter_var($url, FILTER_VALIDATE_URL))
			{
				throw new \Exception('Invalid Type Exception: Expected argument one to function get_url to be valid url.');
			}

			return file_get_contents($url);
		};
	}

	public function parse()
	{
		$this->lexer->add('/^(var)/', 'T_VARIABLE_IDENTIFIER');
		$this->lexer->add('/^( )/', 'T_WHITESPACE');
		$this->lexer->add('/^(?!true)(?!false)(?![\ \;\=\"0-9\(\)]+)(\b(\w+(?![\(\.])))(?!\w)/', 'T_VARIABLE_NAME');
		$this->lexer->add('/^("([^\\"]+|\\.)*")/', 'T_STRING|T_VARIABLE_VALUE');
		$this->lexer->add('/^(?![\ \.]+)([0-9]+)$/', 'T_INTEGER|T_VARIABLE_VALUE');
		$this->lexer->add('/^([0-9]*\.?[0-9]+)/', 'T_FLOAT|T_VARIABLE_VALUE');
		$this->lexer->add('/^(true|false)+/', 'T_BOOLEAN|T_VARIABLE_VALUE');
		$this->lexer->add('/^((\[|\{)[a-z,0-9\ \"\:]+(\]|\}))/', 'T_ARRAY|T_VARIABLE_VALUE');
		$this->lexer->add('/^(((?![\ ])([a-z]+))\(([0-9a-z\,\ ])+\))/', 'T_FUNCTION_RESULT|T_VARIABLE_VALUE');
		$this->lexer->add('/^((?![\ \=\"0-9\(\)]+)(\&)(\b(\w+(?![\(]))([\.]\w+)))/', 'T_NESTED_VARIABLE_REFERENCE|T_VARIABLE_VALUE');
		$this->lexer->add('/^(?![\ \;\=\"0-9\(\)]+)(\&)(\b(\w+(?![\(])))(?!\w)/', 'T_VARIABLE_REFERENCE|T_VARIABLE_VALUE');
		$this->lexer->add('/^(\+|\*|\\|\-)/', 'T_ASSIGNMENT_OPERATOR');
		$this->lexer->add('/^(=)/', 'T_EQUALS');
		$this->lexer->add('/^(;)/', 'T_EOL');

		$tokens = $this->lexer->run();
		while(($token = $tokens->next()) && $token !== null)
		{
			switch($token->type)
			{
				case 'T_VARIABLE_IDENTIFIER':
					$variable_name = $tokens->findNext('T_VARIABLE_NAME');
					$variable_value = $tokens->findNext('T_VARIABLE_VALUE');

					// WE CHECK IF THE NEXT ONE IS A LOGICAL OPERATOR SO IT CAN BE PARSED CORRECTLY
					// THIS CAUSES PERFORMANCE ISSUES, USING A FUNCTION FOR MATH IS FASTER?
					$tokens->setIndex($variable_value->index);
					$operator = $tokens->next('T_WHITESPACE', 1);
					if(in_array('T_ASSIGNMENT_OPERATOR', explode('|', $operator->type)))
					{
						$next = $tokens->next('T_WHITESPACE');
						if($variable_value->type !== 'T_INTEGER|T_VARIABLE_VALUE' && $variable_value->type !== 'T_FLOAT|T_VARIABLE_VALUE')
						{
							throw new \Exception('Parse Error: Unexpected ' . $variable_value->match . ' on line ' . $variable_value->line);
						}
						if($next->type !== 'T_INTEGER|T_VARIABLE_VALUE' && $next->type !== 'T_FLOAT|T_VARIABLE_VALUE')
						{
							throw new \Exception('Parse Error: Unexpected ' . $next->match . ' on line ' . $variable_value->line);
						}

						switch($operator->match)
						{
							case '+':
								$variable_value->match = $variable_value->match + $next->match;
							break;
							case '-':
								$variable_value->match = $variable_value->match - $next->match;
							break;
							case '*':
								$variable_value->match = $variable_value->match * $next->match;
							break;
							case '/':
								$variable_value->match = $variable_value->match / $next->match;
							break;
						}
					}
					else
					{
						$type = (explode('|', $variable_value->type))[0];
						switch($type)
						{
							case 'T_STRING':
								$variable_value->match = strtr($variable_value->match, ['"' => '']);
							break;
							case 'T_INTEGER':
								$variable_value->match = (integer) $variable_value->match;
							break;
							case 'T_FLOAT';
								$variable_value->match = (float) $variable_value->match;
							break;
							case 'T_BOOLEAN':
								$variable_value->match = ($variable_value->match === 'true') ? true : false;
							break;
							case 'T_ARRAY':
								$variable_value->match = json_decode($variable_value->match, true);
								if(!is_array($variable_value->match))
								{
									throw new \Exception('Parse Error: Unable to parse array on line ' . $variable_value->line);
								}
							break;
							case 'T_FUNCTION_RESULT':
								preg_match('/^([a-z]+)/', $variable_value->match, $function_name_matches);
								preg_match('/(\()([0-9a-z\,\ ]+)(\))/', $variable_value->match, $function_param_matches);
								

								$function_name = $function_name_matches[0];
								if(!isset($this->functions[$function_name]))
								{
									throw new \Exception('RuntimeError: Call to undefined function ' . $function_name . ' on line ' . $variable_value->line);
								}

								$function_params = @$function_param_matches[0];
								if($function_params === null)
								{
									throw new \Exception('RuntimeError: Unable to parse function params for function ' . $function_name . ' on line ' . $variable_value->line);
								}

								$base_params = explode(',', str_replace(['(', ')'], '', $function_params));
								$variable_value->match = call_user_func_array($this->functions[$function_name], array_map('trim', $base_params));
							break;
							case 'T_VARIABLE_REFERENCE':
								$variable_ref = strtr($variable_value, ['&' => '']);
								if(!isset($this->variables[$variable_ref]))
								{
									throw new \Exception('RuntimeError: Attempt to access undefined variable ' . $variable_ref . ' on line ' . $variable_value->line);
								}

								$variable_value->match = $this->variables[$variable_ref];
							break;
							case 'T_NESTED_VARIABLE_REFERENCE':
								$variable_ref = strtr($variable_value->match, ['&' => '']);

						        $current_value = $this->variables;
						        $key_path = explode('.', $variable_ref);
						        $value = null;
						        for($i = 0; $i < count($key_path); $i++)
						        {
						        	$current_key = $key_path[$i];
						        	if(!isset($current_value[$current_key]))
						        	{
						        		throw new \Exception('RuntimeError: Attempt to access undefined variable ' . $variable_ref . ' on line ' . $variable_value->line);
						        	}

						            if(!is_array($current_value))
						            {
						                throw new \Exception('RuntimeError: Attempt to access undefined variable ' . $variable_ref . ' on line ' . $variable_value->line);
						            }

						            $current_value = $current_value[$current_key];
						        }

						        if(is_null($current_value))
						        {
						        	throw new \Exception('RuntimeError: Attempt to access undefined variable ' . $variable_ref . ' on line ' . $variable_value->line);
						        }

						        $variable_value->match = $current_value;
							break;
						}
					}

					$this->variables[$variable_name->match] = $variable_value->match;
					$tokens->setIndex($variable_value->index);
				break;
			}
		}
	}

    public static function getVersion()
    {
        return '@git_commit_short@';
    }

    public static function getVersionUrl()
    {
    	return 'https://github.com/1mbsite/1mbcode/commit/@git_commit_short@';
    }
}