<?php
/**
 * GoDaddy Reseller Store Product embed tests
 */

namespace Reseller_Store;

final class TestEmbed extends TestCase {

	/**
	 * @testdox Test Product widgets exist.
	 */
	function test_basics() {

		$this->assertTrue(
			class_exists( __NAMESPACE__ . '\Embed' ),
			'Class \Widgets\Embed is not found'
		);

	}

	/**
	 * @testdox Given a valid post the head should render
	 */
	function test_excerpt_head() {

		global $post;

		$embed = new Embed();

		$post = Tests\Helper::create_product();

		$embed->head();

		$this->expectOutputRegex( '/<style type="text\/css">/' );

	}

	/**
	 * @testdox Given an invalid post the head should not render
	 */
	function test_excerpt_head_invalid_post() {

		global $post;

		$embed = new Embed();

		$post_id = $this->factory->post->create(
			[
				'post_title' => 'test',
			]
		);

		$post = get_post( $post_id );

		$embed->head();

		$this->expectOutputString( '' );

	}

	/**
	 * @testdox Given a valid post the excerpt should return
	 */
	function test_excerpt() {

		global $post;

		$embed = new Embed();

		$post = Tests\Helper::create_product( 'test product' );

		$excerpt = $embed->excerpt( 'content' );

		echo $excerpt;

		$this->expectOutputRegex( '/<p>this is a product<\/p>/' );

	}

	/**
	 * @testdox Given a valid post in `the_content` filter the excerpt should return
	 */
	function test_excerpt_in_content_filter() {

		global $post;

		$post = Tests\Helper::create_product( 'test product' );

		add_filter(
			'the_content',
			function() {

				$embed = new Embed();

				return $embed->excerpt( 'content' );

			}
		);

		echo apply_filters( 'the_content', 'test' );

		$this->expectOutputRegex( '/<p>this is a product<\/p>/' );

	}


	/**
	 * @testdox Given an invalid post type the excerpt should not return
	 */
	function test_excerpt_invalid_post() {

		global $post;

		$embed = new Embed();

		$post_id = $this->factory->post->create(
			[
				'post_title' => 'test',
			]
		);

		$post = get_post( $post_id );

		$excerpt = $embed->excerpt( 'content' );

		$this->assertEquals( 'content', $excerpt );

	}


	/**
	 * @testdox It should flush the cache
	 */
	function test_flush_cache() {

		$cache_entries = Embed::flush_cache();

		$this->assertEquals( 0, $cache_entries );

	}

	/**
	 * @testdox It should search and replace post content
	 */
	function test_search_replace_post_content() {

		Tests\Helper::create_product();

		$posts = Embed::search_replace_post_content( 'product', 'subscription' );

		$this->assertEquals( 1, $posts );

	}

}
