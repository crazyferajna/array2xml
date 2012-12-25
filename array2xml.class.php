<?php

/* 
 * Array to XML class for PHP
 * 
 * version 1.0
 * 
 * by K13 aka crazyferajna
 * 
 * Copyright (c) 2012
 * 
 */

class Array2XML{
  	
  	// wyjsciowy kod XML
	protected $_output = '';
	
	public function __construct($root_node, $array)
	{
		// dodanie naglowka i parsowanie tablicy
		$this->_output = '<?xml version="1.0" encoding="utf-8"?>'.$this->parse_array($root_node, $array);
	}
	
	private function parse_array($name, $obj)
	{
		// lista atrybutow elementu
		$attributes = array();
		
		// zawartosc elementu
		$content = '';
		
		// jesli nazwa tagu jest nieprawidlowa
		if(! self::is_valid_tag($name))
		{
			// wyrzuc wyjatek
			throw new Exception('Invalid tagname \''.$name.'\'');
		}
		
		
		if(is_array($obj))
		{
			foreach($obj as $node => $value)
			{
				switch(strtolower($node))
				{
				case '@attributes':
					
					$attributes = $value;
					break;
				case '@value':
					$content = $value;
					break;
				case '@cdata':
					$content = '<![CDATA['.$value.']]>';
					break;
				default:
				
					if($this->is_list($value))
					{
						foreach($value as $item)
						{
							$content .= $this->parse_array($node, $item);
						}
					}
					else
					{
						$content .= $this->parse_array($node, $value);
					}
				}
			}
			
			if(strlen($content) == 0)
			{
				return '<'.$name.self::attributes($attributes, true).' />';
			}
			else
			{
				return '<'.$name.self::attributes($attributes, true).'>'.$content.'</'.$name.'>';
			}
			
		}
		elseif(self::is_bool($obj))
		{
			return $this->parse_array($name, self::bool_to_string($obj));
		}
		else
		{
			if(strlen($obj) == 0)
			{
				return '<'.$name.' />';
			}
			else
			{
				return '<'.$name.'>'.$obj.'</'.$name.'>';
			}
		}
	}
	
	public static function attributes($array, $space = false)
	{
		$output = array();
		
		foreach($array as $name => $value)
		{
			if(self::is_bool($value))
			{
				$value = self::bool_to_string($value);
			}
				
			if(empty($value))
			{
				$output[] = $name;
			}
			else
			{
				$output[] = $name.'="'.$value.'"';
			}
		}
		
		$output = implode(' ', $output);
		
		if(strlen($output) > 0 && $space)
		{
			return ' '.$output;
		}
		else
		{
			return $output;
		}
	}
	
	public function __toString()
	{
		return $this->_output;
	}
	
	private static function is_array($obj)
	{
		return is_array($obj);
	}
	
	private static function is_integer($obj)
	{
		return is_int($obj);
	}
	
	private static function is_bool($obj)
	{
		return is_bool($obj);
	}
	
	private static function bool_to_string($obj)
	{
		return $obj ? 'true' : 'false';
	}
		
	public function is_list($obj)
	{
		if(! is_array($obj))
		{
			return false;
		}
		
		foreach($obj as $index => $element)
		{
			if(! self::is_integer($index) || ! self::is_array($element))
			{
				return false;
			}
		}
		return true;
	}
	
	private static function is_valid_tag($tag)
	{
        $pattern = '/^[a-z_]+[a-z0-9\:\-\.\_]*[^:]*$/i';
        return preg_match($pattern, $tag, $matches) && $matches[0] == $tag;
    }
}
