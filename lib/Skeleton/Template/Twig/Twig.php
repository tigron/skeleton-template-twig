<?php
/**
 * Twig Template class
 *
 * @author Christophe Gosiau <christophe@tigron.be>
 * @author Gerry Demaret <gerry@tigron.be>
 * @author David Vandemaele <david@tigron.be>
 */

namespace Skeleton\Template\Twig;

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
		$chain_loader = new \Twig\Loader\ChainLoader([
			new \Twig\Loader\FilesystemLoader()
		]);

		if (!isset(Config::$cache_path) and isset(Config::$cache_directory)) {
			Config::$cache_path = Config::$cache_directory;
		}

		$this->twig = new \Twig\Environment(
			$chain_loader,
			[
				'cache' => Config::$cache_path,
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
			$this->twig->addExtension(new \Twig\Extension\DebugExtension());
		}

		$this->twig->addExtension(new \Skeleton\Template\Twig\Extension\Common());
		$this->twig->addExtension(new \Twig\Extension\StringLoaderExtension());
		$this->twig->addExtension(new \Twig\Extra\String\StringExtension());
		$this->twig->addExtension(new \Twig\Extra\Markdown\MarkdownExtension());
		$this->twig->addExtension(new \Twig\Extra\Cache\CacheExtension());

		$extensions = Config::get_extensions();
		foreach ($extensions as $extension) {
			$this->twig->addExtension(new $extension());
		}

		$this->twig->getExtension('\Twig\Extension\CoreExtension')->setNumberFormat(2, '.', '');

		$this->twig->addRuntimeLoader(new class implements \Twig\RuntimeLoader\RuntimeLoaderInterface {
			public function load($class) {
				if (\Twig\Extra\Markdown\MarkdownRuntime::class === $class) {
					return new \Twig\Extra\Markdown\MarkdownRuntime(new \Skeleton\Template\Twig\Extension\Markdown\Engine());
				}
			}
		});

		$this->twig->addRuntimeLoader(new class implements \Twig\RuntimeLoader\RuntimeLoaderInterface {
			public function load($class) {
				if (\Twig\Extra\Cache\CacheRuntime::class === $class) {
					return new \Twig\Extra\Cache\CacheRuntime(new \Symfony\Component\Cache\Adapter\TagAwareAdapter(new \Symfony\Component\Cache\Adapter\FilesystemAdapter()));
				}
			}
		});
	}

	/**
	 * Set Template dir
	 *
	 * @access public
	 * @param string $directory
	 */
	public function add_template_directory($directory, $namespace = null) {
		/**
		 * @Deprecated: for backwards compatibility
		 */
		$this->add_template_path($directory, $namespace);
	}

	/**
	 * Add Template path
	 *
	 * @access public
	 * @param string $path
	 */
	public function add_template_path($path, $namespace = null) {
		if ($this->filesystem_loader === null) {
			$this->filesystem_loader = new \Twig\Loader\FilesystemLoader($path);
		}

		if ($namespace === null) {
			$this->filesystem_loader->addPath($path);
		} else {
			$this->filesystem_loader->addPath($path, $namespace);
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
