<?php
/**
 * TigronMarkdownEngine
 *
 * Maps Tigron\MarkdownExtra to Aptoma\Twig Markdown Extension
 *
 * @author Gerry Demaret <gerry@tigron.be
 */

namespace Skeleton\Template\Twig\Extension\Markdown;

use Aptoma\Twig\Extension\MarkdownEngineInterface;

class Engine implements MarkdownEngineInterface {
	/**
	 * Array with options to pass to the parser
	 *
	 * @var array $protected
	 * @access protected
	 */
	private $options = [];

	/**
	 * Magic setter, stores everything in $options
	 *
	 * @param string $key
	 * @param mixed $value;
	 * @access public
	 */
	public function __set($key, $value) {
		$this->options[$key] = $value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function transform($content) {
		// http://aaronparecki.com/articles/2012/09/01/1/some-enhancements-to-markdown


		// Parse ![:vimeo]() tags
		$content = preg_replace('|(?<!\\\)!\[:vimeo (\d+)x(\d+)\]\(([^\)]+)\)|',
		  '<object width="$1" height="$2"><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=$3&server=vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=&fullscreen=1" /><embed src="http://vimeo.com/moogaloop.swf?clip_id=$3&server=vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=&fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="$1" height="$2"></embed></object>', $content);

		// Parse ![:youtube]() tags
		$content = preg_replace('|(?<!\\\)!\[:youtube (\d+)x(\d+)\]\(([^\)]+)\)|',
		  '<iframe width="$1" height="$2" src="http://www.youtube.com/embed/$3" frameborder="0" allowfullscreen></iframe>', $content);



		$parser = new \Skeleton\Template\Twig\Extension\Markdown();

		foreach ($this->options as $key => $value) {
			$parser->$key = $value;
		}

		$output = $parser->transform($content);
		return $output;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'Tigron\Markdown';
	}
}
