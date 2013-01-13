<?php

/* 
 * Array to XML class for PHP
 * 
 * version 1.0
 * 
 * by K13 aka crazyferajna
 * 
 * Copyright (c) 2012-2013
 * 
 */

class Array2XML{
	protected $_output = '';
	
	/*
	 * Konstruktor klasy
	 * 
	 * @param	string	$root_node	nazwa głównego tagu generowanego dokumentu
	 * @param	array	$array		tablica do konwersji
	 * @return	string
	 */
	public function __construct($root_node, $array)
	{
		$this->_output = '<?xml version="1.0" encoding="utf-8"?>'.$this->parse_array($root_node, $array);
	}
	
	/*
	 * Metoda magiczna rzutowania do stringa
	 * 
	 * @return	string
	 */
	public function __toString()
	{
		return $this->_output;
	}
	
	/*
	 * Główna funkcja parsująca obiekt do kodu XML
	 * 
	 * @param	string	$root_node	nazwa głównego tagu generowanego dokumentu
	 * @param	mixed	$obj		tablica do konwersji
	 * @return	string
	 */
	private function parse_array($root_node, $obj)
	{
		$attributes = array();
		
		$content = '';
		
		if(! self::is_valid_tag($root_node))
		{
			throw new Exception('Invalid tagname \''.$root_node.'\'');
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
				return '<'.$root_node.self::attributes($attributes, true).' />';
			}
			else
			{
				return '<'.$root_node.self::attributes($attributes, true).'>'.$content.'</'.$root_node.'>';
			}
			
		}
		elseif(is_bool($obj))
		{
			return $this->parse_array($root_node, self::bool_to_string($obj));
		}
		else
		{
			if(strlen($obj) == 0)
			{
				return '<'.$root_node.' />';
			}
			else
			{
				return '<'.$root_node.'>'.$obj.'</'.$root_node.'>';
			}
		}
	}
	
	/*
	 * Funkcja parsująca tablicę parametrów do postaci stringa
	 * 
	 * @param	array	$attibutes	lista atrybutów
	 * @param	bool	$space		czy dodać spację na początku wygenerowanego stringa?
	 * @return	string
	 */
	public static function attributes($attibutes, $space = false)
	{
		$output = array();
		
		foreach($attibutes as $name => $value)
		{
			if(is_bool($value))
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
	
	
	/*
	 * Zamienia typ boolean na string
	 * 
	 * @param	boolean	$bool
	 * @return	string
	 */
	private static function bool_to_string($bool)
	{
		return $bool ? 'true' : 'false';
	}
	
	/*
	 * Sprawdza czy obiekt jest tablicą węzłów dokumentu
	 * 
	 * @param	mixed	$obj
	 * @return	bool
	 */
	public function is_list($obj)
	{
		if(! is_array($obj))
		{
			return false;
		}
		
		foreach($obj as $index => $element)
		{
			if(! is_int($index) || ! is_array($element))
			{
				return false;
			}
		}
		return true;
	}
	
	/*
	 * Sprawdza czy string jest dozwoloną nazwą tagu
	 * 
	 * @param	string	$tag
	 * @return	bool
	 */
	private static function is_valid_tag($tag)
	{
        $pattern = '/^[a-z_]+[a-z0-9\:\-\.\_]*[^:]*$/i';
        return preg_match($pattern, $tag, $matches) && $matches[0] == $tag;
    }
}
