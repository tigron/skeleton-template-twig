<?php
/**
 * Additional functions and filters for Twig
 *
 * @author Christophe Gosiau <christophe@tigron.be>
 * @author Gerry Demaret <gerry@tigron.be>
 */

namespace Skeleton\Template\Twig\Extension;

class Common extends \Twig_Extension {

	private $environment;

	/**
	 * Init runtime
	 *
	 * @access public
	 */
	public function initRuntime(\Twig_Environment $environment) {
		parent::initRuntime($environment);
		$this->environment = $environment;
	}

	/**
	 * Returns a list of globals
	 *
	 * @return array
	 */
	public function getGlobals() {
		$templates = [
			'base' => '_default/macro.base.twig',
			'form' => '_default/form.base.twig',
		];

		$globals = [];
		foreach ($templates as $key => $template) {
			try {
				$loaded_template = $this->environment->loadTemplate($template);
				$globals[$key] = $loaded_template;
			} catch (\Twig_Error_Loader $e) { }
		}

		return $globals;
	}

	/**
	 * Returns a list of filters
	 *
	 * @return array
	 */
	public function getFilters() {
		return [
			new \Twig_SimpleFilter('print_r', [$this, 'print_r_filter'], ['is_safe' => ['html']]),
			new \Twig_SimpleFilter('json_decode', [$this, 'json_decode_filter'], ['is_safe' => ['html']]),
			new \Twig_SimpleFilter('serialize', [$this, 'serialize_filter'], ['is_safe' => ['html']]),
			new \Twig_SimpleFilter('round', [$this, 'round_filter'], ['is_safe' => ['html']]),
			new \Twig_SimpleFilter('date', [$this, 'date_filter'], ['needs_environment' => true, 'is_safe' => ['html']]),
			new \Twig_SimpleFilter('datetime', [$this, 'datetime_filter'], ['needs_environment' => true, 'is_safe' => ['html']]),
			new \Twig_SimpleFilter('filesize', [$this, 'filesize_filter'], ['needs_environment' => true, 'is_safe' => ['html']]),
			new \Twig_SimpleFilter('rewrite', [$this, 'rewrite_filter'], ['needs_environment' => true, 'is_safe' => ['html']]),
			new \Twig_SimpleFilter('object_sort', [$this, 'object_sort_filter'], ['needs_environment' => true, 'is_safe' => ['html']]),
			new \Twig_SimpleFilter('get_class', [$this, 'get_class_filter'], ['is_safe' => ['html']]),
			new \Twig_SimpleFilter('reverse_rewrite', [$this, 'reverse_rewrite_filter'], ['is_safe' => ['html']]),
			new \Twig_SimpleFilter('transliterate', [$this, 'transliterate_filter'], ['is_safe' => ['html']]),
		];
	}

	/**
	 * Returns a list of functions
	 *
	 * @return array
	 */
	public function getFunctions() {
		return [
			new \Twig_SimpleFunction('strpos', [$this, 'strpos_function'], ['is_safe' => ['html']]),
		];
	}

	/**
	 * Get class filter
	 *
	 * @param mixed $value
	 * @param bool $raw
	 * @return string $classname
	 */
	public function get_class_filter($value, $raw = true) {
		return get_class($value);
	}

	/**
	 * Reverse rewrite filter
	 *
	 * @param mixed $value
	 * @param bool $raw
	 * @return string $reverse_rewrite
	 */
	public function reverse_rewrite_filter($value, $raw = true) {
		return \Skeleton\Core\Util::rewrite_reverse($value);
	}

	/**
	 * Filter print_r
	 *
	 * @param mixed $value
	 * @param bool $raw
	 * @return string $output
	 */
	public function print_r_filter($value, $raw = true) {
		$output = '';
		if ($raw === false) {
			$output = '<pre>';
		}

		$output .= print_r($value, true);

		if ($raw === false) {
			$output .= '</pre>';
		}

		return $output;
	}

	/**
	 * Filter transliterate
	 *
	 * @param mixed $value
	 * @param mixed $transliterator
	 * @return string $output
	 */
	public function transliterate_filter($value, $transliterator = 'Any-Latin;') {
		return transliterator_transliterate($transliterator, $value);
	}

	/**
	 * Filter serialize
	 *
	 * @param mixed $value
	 * @return string $output
	 */
	public function serialize_filter($value) {
		return serialize($value);
	}

	/**
	 * Filter round
	 *
	 * @param float $value
	 * @param int $decimal
	 * @return int $output
	 */
	public function round_filter($value, $decimal=0) {
		return round($value, $decimal);
	}

	/**
	 * JSON decode
	 *
	 * @param string $json
	 * @return array $array
	 */
	public function json_decode_filter($json) {
		return json_decode($json);
	}

	/**
	 * Filter date
	 *
	 * @param string $date
	 * @param string $format
	 * @return string $output
	 */
	public function date_filter(\Twig_Environment $env, $date, $format = 'd/m/Y') {
		return twig_date_format_filter($env, $date, $format);
	}

	/**
	 * Filter datetime
	 *
	 * @param string $datetime
	 * @param string $format
	 * @return string $output
	 */
	public function datetime_filter(\Twig_Environment $env, $datetime, $format = 'd/m/Y H:i:s') {
		return twig_date_format_filter($env, $datetime, $format);
	}

	/**
	 * Filesize filter
	 *
	 * @param int $filesize File size, in bytes
	 * @param int $precision Round the output to $precision
	 * @param string $decimal_mark Decimal mark to use
	 * @param string $system System to use, can be "iec" (default) or "metric"
	 * @return string $output
	 */
	public function filesize_filter(\Twig_Environment $env, $filesize, $precision = 2, $decimal_mark = '.', $system = 'iec') {
		$iec_suffixes = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];
		$metric_suffixes = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

		if ($system == 'iec') {
			$unit = 1024;
			$suffixes = $iec_suffixes;
		} elseif ($system == 'metric') {
			$unit = 1000;
			$suffixes = $metric_suffixes;
		} else {
			throw new \Exception('System not supported');
		}

		$current_filesize = $filesize;
		foreach ($suffixes as $suffix) {
			$new_filesize = $current_filesize / $unit;

			if ($new_filesize < 1) {
				return number_format($current_filesize, $precision, $decimal_mark, ' ') . ' ' . $suffix;
			}

			$current_filesize = $new_filesize;
		}

		return $filesize;
	}

	/**
	 * Filter rewrite
	 *
	 * @param string $url
	 * @return string $output
	 */
	public function rewrite_filter(\Twig_Environment $env, $url) {
		if (class_exists('\Skeleton\Core\Util')) {
			return \Skeleton\Core\Util::rewrite_reverse($url);
		}

		return $url;
	}

	/**
	 * Filter object_sort
	 *
	 * @param array $objects Array containing the objects to be supported
	 * @param string $property Property on which to sort the objects
	 * @param string $direction Direction in which to sort the objects
	 * @param string $type Type of sorting to apply, can be "auto" (default), "string" or "date"
	 * @return string $output
	 */
	public function object_sort(\Twig_Environment $env, $objects, $property, $direction = 'asc', $type = 'auto') {
		usort($objects, function($a, $b) use ($property, $direction, $type) {
			if (!is_object($property) AND isset($a->$property)) {
				$property1 = $a->$property;
				$property2 = $b->$property;
			} elseif (is_callable([$a, $property])) {
				$property1 = call_user_func_array([$a, $property], []);
				$property2 = call_user_func_array([$a, $property], []);
			} elseif (is_callable($property)) {
				$property1 = $property($a);
				$property2 = $property($b);
			}

			if (is_numeric($property1) AND is_numeric($property2) AND $type == 'auto') {
				$type = 'int';
			}

			if ($type == 'string') {
				$cmp = strcasecmp($property1, $property2);
			} elseif ($type == 'date') {
				if (strtotime($property1) > strtotime($property2)) {
					$cmp = 1;
				} else {
					$cmp = -1;
				}
			} else {
				$cmp = $property1 > $property2 ? 1 : -1;
			}

			if ($direction == 'desc') {
				return -1*$cmp;
			} else {
				return $cmp;
			}
		});

		return $objects;
	}

	/**
	 * Function strpos
	 *
	 * @param mixed $value
	 * @param string $to_search
	 * @return mixed $output
	 */
	public function strpos_function($value, $to_search) {
		return strpos($value, $to_search);
	}

	/**
	 * Name of this extension
	 *
	 * @return string
	 */
	public function getName() {
		return 'Common';
	}
}
