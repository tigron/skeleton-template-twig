<?php
/**
 * Additional functions and filters for Twig
 *
 * @author Christophe Gosiau <christophe@tigron.be>
 * @author Gerry Demaret <gerry@tigron.be>
 */

namespace Skeleton\Template\Twig\Extension;

class Common extends \Twig\Extension\AbstractExtension {

	/**
	 * Returns a list of filters
	 *
	 * @return array
	 */
	public function getFilters() {
		return [
			new \Twig\TwigFilter('print_r', [$this, 'print_r_filter'], ['is_safe' => ['html']]),
			new \Twig\TwigFilter('json_decode', [$this, 'json_decode_filter'], ['is_safe' => ['html']]),
			new \Twig\TwigFilter('serialize', [$this, 'serialize_filter'], ['is_safe' => ['html']]),
			new \Twig\TwigFilter('round', [$this, 'round_filter'], ['is_safe' => ['html']]),
			new \Twig\TwigFilter('date', [$this, 'date_filter'], ['needs_environment' => true, 'is_safe' => ['html']]),
			new \Twig\TwigFilter('datetime', [$this, 'datetime_filter'], ['needs_environment' => true, 'is_safe' => ['html']]),
			new \Twig\TwigFilter('filesize', [$this, 'filesize_filter'], ['needs_environment' => true, 'is_safe' => ['html']]),
			new \Twig\TwigFilter('rewrite', [$this, 'rewrite_filter'], ['needs_environment' => true, 'is_safe' => ['html']]),
			new \Twig\TwigFilter('object_sort', [$this, 'object_sort_filter'], ['needs_environment' => true, 'is_safe' => ['html']]),
			new \Twig\TwigFilter('get_class', [$this, 'get_class_filter'], ['is_safe' => ['html']]),
			new \Twig\TwigFilter('reverse_rewrite', [$this, 'reverse_rewrite_filter'], ['is_safe' => ['html']]),
			new \Twig\TwigFilter('transliterate', [$this, 'transliterate_filter'], ['is_safe' => ['html']]),
			new \Twig\TwigFilter('byte_format', [$this, 'byte_format_filter'], ['is_safe' => ['html']]),
			new \Twig\TwigFilter('truncate', [$this, 'truncate_filter'], ['needs_environment' => true]),
		];
	}

	/**
	 * Returns a list of functions
	 *
	 * @return array
	 */
	public function getFunctions() {
		return [
			new \Twig\TwigFunction('strpos', [$this, 'strpos_function'], ['is_safe' => ['html']]),
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
	public function date_filter(\Twig\Environment $env, $date, $format = 'd/m/Y') {
		return twig_date_format_filter($env, $date, $format);
	}

	/**
	 * Filter datetime
	 *
	 * @param string $datetime
	 * @param string $format
	 * @return string $output
	 */
	public function datetime_filter(\Twig\Environment $env, $datetime, $format = 'd/m/Y H:i:s') {
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
	public function filesize_filter(\Twig\Environment $env, $filesize, $precision = 2, $decimal_mark = '.', $system = 'iec') {
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
	public function rewrite_filter(\Twig\Environment $env, $url) {
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
	public function object_sort_filter(\Twig\Environment $env, $objects, $property, $direction = 'asc', $type = 'auto') {
		usort($objects, function($a, $b) use ($property, $direction, $type) {
			if (!is_object($property) && isset($a->$property)) {
				$property1 = $a->$property;
				$property2 = $b->$property;
			} elseif (is_callable([$a, $property])) {
				$property1 = call_user_func_array([$a, $property], []);
				$property2 = call_user_func_array([$b, $property], []);
			} elseif (is_callable($property)) {
				$property1 = $property($a);
				$property2 = $property($b);
			}

			if (is_numeric($property1) && is_numeric($property2) && $type == 'auto') {
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
	 * Function byte_format_filter
	 *
	 * @param mixed $bytes
	 * @param string $si Use SI units (or not)
	 * @return mixed $group_thousands
	 */
	public function byte_format_filter($bytes, $si = false, $group_thousands = false) {
		if ($si === true) {
			$unit = 1000;
			$prefix = 'kMGTPE';
		} else {
			$unit = 1024;
			$prefix = 'KMGTPE';
		}

		if ($bytes == "") {
			$bytes = 0;
		}

		if ($bytes <= $unit) {
			if ($group_thousands === true) {
				return number_format($bytes) . ' B';
			}

			return $bytes . ' B';
		}

		$exponent = intval((log($bytes) / log($unit)));
		$prefix = $prefix[$exponent - 1] . ($si ? "" : "i");
		$number = sprintf("%.1f", $bytes / pow($unit, $exponent));

		if ($group_thousands === true) {
			$number = number_format($number);
		}

		return sprintf("%.1f %sB", $number, $prefix);
	}

	/**
	 * Function twig_truncate_filter
	 *
	 * @access public
	 * @param Environment $environment
	 * @param string $value
	 * @param int $length
	 * @param boolean $preserve
	 * @param string $separator
	 */
	public function truncate_filter(\Twig\Environment $env, $value, $length = 30, $preserve = false, $separator = '...') {
		if (mb_strlen($value, $env->getCharset()) > $length) {
			if ($preserve) {
				// If breakpoint is on the last word, return the value without separator.
				if (false === ($breakpoint = mb_strpos($value, ' ', $length, $env->getCharset()))) {
					return $value;
				}
				$length = $breakpoint;
			}
			return rtrim(mb_substr($value, 0, $length, $env->getCharset())).$separator;
		}
		return $value;
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
