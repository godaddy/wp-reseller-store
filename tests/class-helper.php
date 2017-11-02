<?php
/**
 * Class Helper.
 *
 * Helper class to create and test reseller_product post easily.
 */

namespace Reseller_Store\Tests;

final class Helper {

	/**
	 * Product fixture.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	public static  $fixture = '{
    "id": "wordpress-basic",
    "categories": [
      {
        "Hosting": [
          "WordPress"
        ]
      },
      "Websites"
    ],
    "tags": [
      "hosting",
      "WordPress",
      "websites"
    ],
    "title": "WordPress Basic",
    "content": "<p>Think basic sites and blogs and startups.</p>\n<ul>\n<li>1 website</li>\n<li>10GB SSD storage</li>\n<li>25,000 monthly visitors</li>\n<li>SFTP</li>\n</ul>\n",
    "term": "month",
    "image": "https://img1.wsimg.com/rcc/products/banner/46.png",
    "imageId": "46",
    "listPrice": "$7.99",
    "salePrice": false
  }';


	/**
	 * Create a product post.
	 *
	 * @since 1.0.0
	 *
	 * @param string $post_title     The title of the custom post.
	 *
	 * @return array                 The post that was just created.
	 */
	public static function create_product( $post_title = 'WordPress Hosting' ) {

		$post_id = wp_insert_post(
			[
				'post_title'  => $post_title,
				'post_name'   => 'wordpress-hosting',
				'post_type'   => 'reseller_product',
				'post_status' => 'publish',
			]
		);

		$meta = [
			'rstore_id'         => 'wordpress-basic',
			'rstore_categories' => [],
			'rstore_image'      => 'http://image',
			'rstore_term'       => 'year',
			'rstore_listPrice'  => '$70.00',
			'rstore_title'      => 'Wordpres Hosting',
			'rstore_content'    => 'blah blah',
			'rstore_salePrice'  => '$50.00',
		];

		foreach ( $meta as $key => $value ) {

			update_post_meta( $post_id, $key, $value );

		}

		return get_post( $post_id );

	}

}
