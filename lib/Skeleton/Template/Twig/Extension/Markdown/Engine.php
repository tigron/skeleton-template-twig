<?php
/**
 * TigronMarkdownEngine
 *
 * Maps Tigron\MarkdownExtra to Aptoma\Twig Markdown Extension
 *
 * @author Gerry Demaret <gerry@tigron.be
 * @author David Vandemaele <david@tigron.be>
 */

namespace Skeleton\Template\Twig\Extension\Markdown;

class Engine implements \Twig\Extra\Markdown\MarkdownInterface {

	/**
	 * {@inheritdoc}
	 */
	public function convert(string $body): string {
		// http://aaronparecki.com/articles/2012/09/01/1/some-enhancements-to-markdown

		// Parse ![:vimeo]() tags
		$body = preg_replace('|(?<!\\\)!\[:vimeo (\d+)x(\d+)\]\(([^\)]+)\)|',
		  '<object width="$1" height="$2"><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=$3&server=vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=&fullscreen=1" /><embed src="http://vimeo.com/moogaloop.swf?clip_id=$3&server=vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=&fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="$1" height="$2"></embed></object>', $body);

		// Parse ![:youtube]() tags
		$body = preg_replace('|(?<!\\\)!\[:youtube (\d+)x(\d+)\]\(([^\)]+)\)|',
		  '<iframe width="$1" height="$2" src="http://www.youtube.com/embed/$3" frameborder="0" allowfullscreen></iframe>', $body);

		$convertor = new \Twig\Extra\Markdown\MarkdownRuntime(new \Twig\Extra\Markdown\DefaultMarkdown());
		return $convertor->convert($body);
	}

}
