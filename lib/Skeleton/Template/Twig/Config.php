<?php
/**
 * Config class
 *
 * @author Christophe Gosiau <christophe@tigron.be>
 * @author Gerry Demaret <gerry@tigron.be>
 * @author David Vandemaele <david@tigron.be>
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
	public static $cache_path = '/tmp';
	public static $cache_directory = null;	 // @Deprecated

	/**
	 * Auto_escape
	 *
	 * Indicate if the resulting template should be auto-escaped
	 *
	 * @access public
	 * @var bool $autoescape
	 */
	public static $autoescape = 'name';

	/**
	 * Extensions
	 *
	 * @access private
	 * @var array $extensions
	 */
	private static $extensions = [];

	/**
	 * Add Extension
	 *
	 * Add an extension for custom functions and filters
	 *
	 * @access public
	 * @param string $classname
	 */
	public static function add_extension($classname) {
		self::$extensions[] = $classname;
	}

	/**
	 * Get extensions
	 *
	 * @access public
	 * @return array $extensions
	 */
	public static function get_extensions() {
		return self::$extensions;
	}
}
