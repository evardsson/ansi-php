<?php
namespace evardsson\ansi;
/**
 * ANSI character color shortcuts for PHP
 * 
 * @license MIT
 * The MIT License (MIT)
 * 
 * Copyright (c) 2013 Sjan Evardsson
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author Sjan Evardsson
 * @version 0.8
 */

/**
 * ANSI character color shortcuts for PHP
 *
 * This class provides simple colorizing shortcuts for ANSI text on colored terms in
 * PHP
 * USAGE:
 * $a = new ANSI(ANSI::WHITE, ANSI::GREEN, array( ANSI::BOLD, ANSI::UNDERLINE, ANSI::BLINK));
 * $a->pline("This should be green background, white text, bright, underlined, and blinking");
 */
class ANSI 
{
	/*
    0   Regular
    1   Bright (increased intensity) works pretty much everywhere 	
    2   Faint (decreased intensity) not widely supported
    3   Italic: not widely supported. Sometimes treated as inverse. Ignored on iTerm,
            not displayed in Term.app or Gentoo Bash term - 
    4   Underline: Single works pretty much everywhere
    5   Blink: Slow less than 150 per minute works pretty much everywhere
    6   Blink: Rapid MS-DOS ANSI.SYS; 150 per minute or more; not widely supported
            only works in pre-XP MS-DOS
    7   Image: Negative inverse or reverse; swap foreground and background
    */
	const BLACK  = 0;
	const RED    = 1;
	const GREEN  = 2;
	const YELLOW = 3;
	const BLUE   = 4;
	const PURPLE = 5;
	const CYAN   = 6;
	const WHITE  = 7;
	
	const NORMAL     = 0;
	const BOLD       = 1;
	const BRIGHT     = 1;
	const DULL       = 2;
	const FAINT      = 2;
	const ITALIC     = 3;
	const UNDERLINE  = 4;
	const BLINK      = 5;
	const BLINK_SLOW = 5;
	const BLINK_FAST = 6;
	const BLINK_DOS  = 6;
	const INVERSE    = 7;
	
	private $reset;
	private $format;
	private $foreground;
	private $background;
	private $style;
	private $switches;
	
	/**
	 * Constructor
	 * @param mixed $foreground color either an int value from the constants, or
	 *		a color name (case is not important, but spelling is - it must be in the 
	 * 		color list below)
	 *		OPTION: if your terminal supports 256 colors, you can use any int value
	 *		from 0 - 255 here. If you are unsure whether your terminal can display
	 *		256 colors or are looking for the right color to use, you can call the
	 *		static function showForegroundColors()
	 * @param mixed $background color either an int value from the constants, or
	 *		a color name (case is not important, but spelling is - it must be in the 
	 * 		color list below)
	 *		OPTION: if your terminal supports 256 colors, you can use any int value
	 *		from 0 - 255 here. If you are unsure whether your terminal can display
	 *		256 colors or are looking for the right color to use, you can call the
	 *		static function showBackgroundColors()
	 * @param mixed $style int value from the list below, or array of ints, or
	 *		a value name or array of names (case is not important, but spelling is -
	 *		it must be in the style list below)
	 *		NOTE: Styles do not get applied for extended colors. They are only applied
	 *		to the default system colors. The only exception is INVERSE which this
	 *		script emulates in extended color sets by swapping foreground and 
	 *		background colors.
	 * Colors for foreground/background must be one of:
	 *		ints:
	 *		ANSI::BLACK
	 * 		ANSI::RED
	 * 		ANSI::GREEN
	 * 		ANSI::YELLOW
	 * 		ANSI::BLUE
	 * 		ANSI::PURPLE
	 * 		ANSI::CYAN
	 * 		ANSI::WHITE
	 *		strings (case insensitive):
	 *		'black','red','green','yellow','blue','purple','cyan','white'
	 * Styles for style must be one of or array of:
	 *		ints:
	 * 		ANSI::NORMAL - normal (default term) style
	 * 		ANSI::BOLD - same as BRIGHT, uses brighter value of color
	 * 		ANSI::BRIGHT
	 * 		ANSI::DULL - same as FAINT, uses fainter value of color, not widely supported
	 * 		ANSI::FAINT
	 * 		ANSI::ITALIC - not widely supported
	 * 		ANSI::UNDERLINE - underlines text
	 * 		ANSI::BLINK - same as BLINK_SLOW (< 150 / min)
	 * 		ANSI::BLINK_SLOW
	 * 		ANSI::BLINK_FAST - same as BLINK_DOS, only supported in MS-DOS pre WinXP
	 * 		ANSI::BLINK_DOS
	 * 		ANSI::INVERSE - switches foreground/background colors
	 *		strings (case insensitive):
	 *		'normal','bold','bright','dull','faint','italic','underline','blink',
	 *		'blink_slow','blink_fast','blink_dos','inverse'
	 */
	public function __construct($foreground, $background=false, $style=false) {
		$this->foreground = $foreground;
		$this->background = $background;
		$this->style = $style;
		$this->reset = chr(27).'[0m';
		$this->parseStyle();
	}
	
	/**
	 * Magic getter. Overrides default -> operator
	 * @return mixed
	 */
	public function __get($key) 
	{
		if (isset($this->shortnames[$key])) 
		{
			return chr(27).$this->shortnames[$key];
		}
		if (isset($this->longnames[$key])) 
		{
			return chr(27).$this->longnames[$key];
		}
		if (isset($this->$key) && is_int($this->$key))
		{
			return $this->$key;
		}
		return null;
	}
	
	/**
	 * Print a formatted string
	 * @param string $string - the string to print
	 * @param boolean $reset - defaults to true, set to false to leave term in
	 *		colored/styled state after printing.
	 * NOTE: if you choose to leave the term styled, remember to reset to default
	 * by calling reset() before exiting, or the term will be styled until manually
	 * changed by the user or restarted!
	 */
	public function p($string)
	{
		print $this->format.$string.$this->reset;
	}
	
	/**
	 * Print a formatted string to a single line
	 * @param string $string - the string to print
	 * @param boolean $reset - defaults to true, set to false to leave term in
	 *		colored/styled state after printing.
	 * NOTE: if you choose to leave the term styled, remember to reset to default
	 * by calling reset() before exiting, or the term will be styled until manually
	 * changed by the user or restarted!
	 */
	public function pline($string)
	{
		print $this->format.$string.$this->reset."\n";
	}
	
	/**
	 * Change the foreground color. See __construct() for color values
	 */
	public function setForeground($colorInt)
	{
		$this->foreground = $colorInt;
		$this->parseStyle();
	}
	
	/**
	 * Change the background color. See __construct() for color values
	 */
	public function setBackground($colorInt)
	{
		$this->background = $colorInt;
		$this->parseStyle();
	}
	
	/**
	 * Change the bold state
	 * @param boolean $bool - true to turn bold on, false to turn it off
	 */
	public function setBold($bool)
	{ // 1
		$this->switches[ANSI::BOLD] = $bool;
		$this->parseStyle();
	}
	
	/**
	 * Change the underline state
	 * @param boolean $bool - true to turn underline on, false to turn it off
	 */
	public function setUnderline($bool)
	{ // 4
		$this->switches[ANSI::UNDERLINE] = $bool;
		$this->parseStyle();
	}
	
	/**
	 * Change the blink state
	 * @param boolean $bool - true to turn blink on, false to turn it off
	 */
	public function setBlink($bool)
	{ // 5
		$this->switches[ANSI::BLINK] = $bool;
		$this->parseStyle();
	}
	
	/**
	 * Change the inverse state
	 * @param boolean $bool - true to turn inverse on, false to turn it off
	 */
	public function setInverse($bool)
	{ // 7
		if ($this->switches[ANSI::INVERSE] != $bool && ($this->foreground > 7 || $this->background > 7)) 
		{
			$tmp = $this->foreground;
			$this->foreground = $this->background;
			$this->background = $tmp;
		}
		$this->switches[ANSI::INVERSE] = $bool;
		$this->parseStyle();
	}
	
	/**
	 * Reset term to defaults
	 */
	public function reset()
	{ // 0
		print $this->reset;
	}
	
	/**
	 * Show all available foreground colors
	 * It will also show if your term is 256 color capable
	 */
	public static function showForegroundColors()
	{
		$x = chr(27);
		$r = $x.'[0m';
		print <<<EOL

SYSTEM COLORS:
  Name   | Normal Sample            | Bright Sample
---------+--------------------------+------------------------
  BLACK  |{$x}[30m Sample Text {$x}[47m Sample Text $r|{$x}[1;30m Sample Text {$x}[47m Sample Text$r
  RED    |{$x}[31m Sample Text {$x}[47m Sample Text $r|{$x}[1;31m Sample Text {$x}[47m Sample Text$r
  GREEN  |{$x}[32m Sample Text {$x}[47m Sample Text $r|{$x}[1;32m Sample Text {$x}[47m Sample Text$r
  YELLOW |{$x}[33m Sample Text {$x}[47m Sample Text $r|{$x}[1;33m Sample Text {$x}[47m Sample Text$r
  BLUE   |{$x}[34m Sample Text {$x}[47m Sample Text $r|{$x}[1;34m Sample Text {$x}[47m Sample Text$r
  PURPLE |{$x}[35m Sample Text {$x}[47m Sample Text $r|{$x}[1;35m Sample Text {$x}[47m Sample Text$r
  CYAN   |{$x}[36m Sample Text {$x}[47m Sample Text $r|{$x}[1;36m Sample Text {$x}[47m Sample Text$r
  WHITE  |{$x}[37m Sample Text {$x}[47m Sample Text $r|{$x}[1;37m Sample Text {$x}[47m Sample Text$r


EOL;
		
		print "EXTENDED COLORS:\n";
		print "###  Sample          ###  Sample          ###  Sample          ###  Sample          ###  Sample          ###  Sample\n";
		$count = 1;
		for ($i = 16; $i < 256; $i++)
		{
			$pd = ($i < 100)?' ':'';
			print "$pd$i {$x}[38;5;{$i}mSample {$x}[47mSample $r   ";
			if ($count % 6 == 0) print "\n";
			$count++;
		}
	}
	
	/**
	 * Show all available background colors
	 * It will also show if your term is 256 color capable
	 */
	public static function showBackgroundColors()
	{
		$x = chr(27);
		$r = $x.'[0m';
		print <<<EOL

SYSTEM COLORS:
  Name   | Sample
---------+-------------------------
  BLACK  |{$x}[40m Sample Text {$x}[30m Sample Text $r
  RED    |{$x}[41m Sample Text {$x}[30m Sample Text $r
  GREEN  |{$x}[42m Sample Text {$x}[30m Sample Text $r
  YELLOW |{$x}[43m Sample Text {$x}[30m Sample Text $r
  BLUE   |{$x}[44m Sample Text {$x}[30m Sample Text $r
  PURPLE |{$x}[45m Sample Text {$x}[30m Sample Text $r
  CYAN   |{$x}[46m Sample Text {$x}[30m Sample Text $r
  WHITE  |{$x}[47m Sample Text {$x}[30m Sample Text $r


EOL;
		
		print "EXTENDED COLORS:\n";
		print "###  Sample          ###  Sample          ###  Sample          ###  Sample          ###  Sample          ###  Sample\n";
		$count = 1;
		for ($i = 16; $i < 256; $i++)
		{
			$pd = ($i < 100)?' ':'';
			print "$pd$i {$x}[48;5;{$i}mSample {$x}[30mSample $r   ";
			if ($count % 6 == 0) print "\n";
			$count++;
		}
	}
	
	/**
	 * Get the proper extended term number for the nearest color to an rgb color space
	 * @param int $r red 0 - 255
	 * @param int $g green 0 - 255
	 * @param int $b blue 0 - 255
	 * @return int closest ANSI 256 color value
	 */
	public static function rgb($r, $g, $b)
	{
		$ret = 0;
		// first look for grayscale
		if ($r == $g && $r == $b)
		{
			if ($r % 51 <= $r % 10.625)
			{ // this is usually closer in the main color area
				$x = round($r / 51);
				$ret = 16 + ($x * 36) + ($x * 6) + $x;
			}
			else
			{ // this is closer in the grayscale area
				$x = round($r / 10.625);
				$ret = 232 + $x;
			}
		}
		else
		{ // look in the main color area
			$x = round($r / 51);
			$y = round($g / 51);
			$z = round($b / 51);
			$ret = 16 + ($x * 36) + ($y * 6) + $z;
		}
		return $ret;
	}
	
	/**
	 * Internal function to set up format string
	 */
	private function parseStyle() {
		// styles can be chained: ~[1;4;6;31m = bright, underlined, blinking red
		if (!is_array($this->switches)) 
		{
			$this->setSwitches($this->style);
		}
		$s = '';
		foreach ($this->switches as $k=>$v)
		{
			if ($this->foreground <= 7 && $this->background <= 7) 
			{
				if ($v) $s .= $k.';';
			}
		}
		$b = '';
		if ($this->background !== false) {
			if (is_numeric($this->background) && $this->background >= 0) 
			{
				if ($this->background <= 7)
				{
					$bx = $this->background + 40;
					$b = chr(27)."[{$bx}m";
				}
				else
				{
					$b = chr(27).'[48;5;'.$this->background.'m';
				}
			}
			else if (is_string($this->background))
			{
				$bs = strtoupper($this->background);
				if (isset(self::$bs))
				{
					$bx = self::$bs + 40;
					$b = chr(27)."[{$bx}m";
				}
			}
		}
		$f = 30;
		if (is_numeric($this->foreground) && $this->foreground >= 0) 
		{
			if ($this->foreground <= 7)
				$f += $this->foreground;
			else
				$f = chr(27).'[38;5;'.$this->foreground;
		}
		else if (is_string($this->foreground))
		{
			$fs = strtoupper($this->foreground);
			if (isset(self::$fs))
			{
				$f += self::$fs;
			}
		}
		$this->format = chr(27).'['.$s.$f.'m'.$b;
	}
	
	/**
	 * Internal function to set up style switches used for creating format string
	 */
	private function setSwitches($style) {
		$this->switches = array (
			0 => false,
			1 => false,
			2 => false,
			3 => false,
			4 => false,
			5 => false,
			6 => false,
			7 => false
			);
		if (is_array($this->style)) foreach ($this->style as $st)
		{
			$this->switches[$st] = true;
		}
		else if (is_int($this->style))
		{
			$this->switches[$this->style] = true;
		}
	}
	
}

