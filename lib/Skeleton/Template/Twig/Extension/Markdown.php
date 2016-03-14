<?php
/**
 * Markdown options
 *
 * Add extra options to php-markdown
 *
 * PHP Markdown Options
 * Copyright (c) 2014 Gerry Demaret, Tigron bvba
 * <http://tigron.be/>
 *
 * PHP Markdown
 * Copyright (c) 2004-2013 Michel Fortin
 * <http://michelf.com/projects/php-markdown/>
 *
 * Original Markdown
 * Copyright (c) 2004-2006 John Gruber
 * <http://daringfireball.net/projects/markdown/>
 */

namespace Skeleton\Template\Twig\Extension;

class Markdown extends \Michelf\MarkdownExtra {
	/**
	 * Treat single newlines as line breaks
	 *
	 * @var bool $single_linebreak
	 * @access public
	 */
	public $single_linebreak = false;

	/**
	 * Override method doHardBreaks
	 *
	 * @param string $text
	 */
	protected function doHardBreaks($text) {
		if ($this->single_linebreak == true) {
			$expression = '/ {2,}\n|\n{1}/';
		} else {
			$expression = '/ {2,}\n/';
		}

		return preg_replace_callback($expression,
			array(&$this, '_doHardBreaks_callback'), $text);
	}
}
