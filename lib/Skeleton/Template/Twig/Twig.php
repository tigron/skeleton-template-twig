<?php
/**
 * Twig Template class
 *
 * @author Christophe Gosiau <christophe@tigron.be>
 * @author Gerry Demaret <gerry@tigron.be>
 */

namespace Skeleton\Template\Twig;

use Aptoma\Twig\Extension\MarkdownExtension;
use Aptoma\Twig\Extension\MarkdownEngine;

class Twig {
	/**
	 * Local Twig instance
	 *
	 * @var \Twig_Environment $twig
	 */
	private $twig = null;

	/**
	 * Variables
	 *
	 * @access private
	 * @var array $variables
	 */
	private $variables = [];

	/**
	 * Variables to add to the environment
	 *
	 * @access private
	 * @var array $environment
	 */
	private $environment = [];

	/**
	 * Translation
	 *
	 * @access private
	 * @var Translation $translation
	 */
	private $translation = null;

	/**
	 * Is i18n available
	 *
	 * @access private
	 * @var bool $i18n_available
	 */
	private $i18n_available = false;

	/**
	 * filesystem_loader
	 *
	 * @access private
	 * @var Twig_Loader_Filesystem $filesystem
	 */
	private $filesystem_loader = null;

	/**
	 * Constructor
	 *
	 * @param string $name
	 * @param Language $language
	 */
	public function __construct() {
		\Twig_Autoloader::register();
		$chain_loader = new \Twig_Loader_Chain([
			new \Twig_Loader_Filesystem(),
			new \Twig_Loader_String()
		]);

		$this->twig = new \Twig_Environment(
			$chain_loader,
			[
				'cache' => Config::$cache_directory,
				'auto_reload' => true,
				'debug' => Config::$debug,
				'autoescape' => Config::$autoescape
			]
		);

		if (class_exists('\\Skeleton\\I18n\\Translation')) {
			$this->i18n_available = true;
			$this->twig->addExtension(new \Skeleton\I18n\Template\Twig\Extension\Tigron());
		}

		if (Config::$debug === true) {
			$this->twig->addExtension(new \Twig_Extension_Debug());
		}

		$this->twig->addExtension(new \Skeleton\Template\Twig\Extension\Common());
		$this->twig->addExtension(new \Twig_Extension_StringLoader());
		$this->twig->addExtension(new \Twig_Extensions_Extension_Text());

		$parser = new \Skeleton\Template\Twig\Extension\Markdown\Engine();
		$parser->single_linebreak = true;
		$this->twig->addExtension(new MarkdownExtension(
			$parser
		));

		$extensions = Config::get_extensions();
		foreach ($extensions as $extension) {
			$this->twig->addExtension($extension);
		}

		$this->twig->getExtension('core')->setNumberFormat(2, '.', '');
	}

	/**
	 * Set Template dir
	 *
	 * @access public
	 * @param string $directory
	 */
	public function add_template_directory($directory, $namespace = null) {
		if ($this->filesystem_loader === null) {
			$this->filesystem_loader = new \Twig_Loader_Filesystem($directory);
		}

		if ($namespace === null) {
			$this->filesystem_loader->addPath($directory);
		} else {
			$this->filesystem_loader->addPath($directory, $namespace);
		}
	}

	/**
	 * Add an environment variable
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function add_environment($key, $value) {
		$this->environment[$key] = $value;
	}

	/**
	 * Assign variables to the template
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function assign($key, $value) {
		$this->variables[$key] = $value;
	}

	/**
	 * Set translation
	 *
	 * @access public
	 * @param Translation $translation
	 */
	public function set_translation(\Skeleton\I18n\Translation $translation) {
		if ($this->i18n_available === true) {
			$this->translation = $translation;
		} else {
			throw new \Exception('Translation is not available, class not found');
		}
	}

	/**
	 * Render
	 *
	 * @access public
	 * @param string $template
	 * @return string $html
	 */
	public function render($template) {
		$environment = [
			'post' => $_POST,
			'get' => $_GET,
			'cookie' => $_COOKIE,
			'server' => $_SERVER,
		];

		$environment = array_merge($environment, $this->environment);

		if ($this->i18n_available === true and $this->translation !== null) {
			$environment['translation'] = $this->translation;
			$environment['language'] = $this->translation->language;
		}

		if (isset($_SESSION)) {
			$environment['session'] = $_SESSION;
		}
		$this->twig->setLoader($this->filesystem_loader);
		$this->twig->addGlobal('env', $environment);
		return $this->twig->render($template, $this->variables);
	}
}
