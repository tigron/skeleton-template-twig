<?php
/**
 * Config class
 *
 * @author Christophe Gosiau <christophe@tigron.be>
 * @author Gerry Demaret <gerry@tigron.be>
 */

namespace Skeleton\Template\Twig;

class Config {

	/**
	 * Enable debugging
	 *
	 * @access public
	 * @var bool $debug
	 */
	public static $debug = false;

	/**
	 * Cache directory
	 *
	 * This folder will be used to store the cached templates
	 *
	 * @access public
	 * @var string $cache_directory
	 */
	public static $cache_directory = '/tmp';

	/**
	 * Auto_escape
	 *
	 * Indicate if the resulting template should be auto-escaped
	 *
	 * @access public
	 * @var bool $autoescape
	 */
	public static $autoescape = true;
}
