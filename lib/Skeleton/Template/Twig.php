<?php
/**
 * Twig Template class
 *
 * @author Christophe Gosiau <christophe@tigron.be>
 * @author Gerry Demaret <gerry@tigron.be>
 */

namespace Skeleton\Template;

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
				'cache' => TMP_PATH . '/twig/',
				'auto_reload' => true,
				'debug' => true
			]
		);

		if (class_exists('\\Skeleton\\I18n\\Translation') {
			$this->i18n_available = true;
			$this->twig->addExtension(new \Skeleton\I18n\Template\Twig\Extension\Tigron());
		}

		if (Config::$debug === true) {
			$this->twig->addExtension(new \Twig_Extension_Debug());
		}

		$this->twig->addExtension(new \Skeleton\Template\Twig\Extension\Default());
		$this->twig->addExtension(new \Twig_Extension_StringLoader());
		$this->twig->getExtension('core')->setNumberFormat(2, '.', '');
	}

	/**
	 * Set Template dir
	 *
	 * @access public
	 * @param string $directory
	 */
	public function set_template_directory($directory) {
		$loader = new \Twig_Loader_Filesystem($directory);
		$this->twig->setLoader($loader);
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
		$variables = [
			'post' => $_POST,
			'get' => $_GET,
			'cookie' => $_COOKIE,
			'server' => $_SERVER,
			'language' => 	$this->translation->language,
		];

		if ($this->i18n_available === true) {
			$variables['translation'] = $this->translation;
		}

		if (isset($_SESSION)) {
			$variables['session'] = $_SESSION;
			// FIXME: this is going to be broken
			//$variables['session_sticky'] = Web_Session_Sticky::Get();
		}

		$this->twig->addGlobal('env', $variables);
		return $this->twig->render($template, $this->variables);
	}
}
