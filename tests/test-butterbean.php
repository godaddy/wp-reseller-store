<?php
/**
 * GoDaddy Reseller Store ButterBean class tests
 */

namespace Reseller_Store;

final class TestButterBean extends TestCase {

	/**
	 * Load post action name.
	 *
	 * @since NEXT
	 *
	 * @var string
	 */
	private $load_post_php = 'load-post.php';

	/**
	 * @testdox Test that ButterBean class exists.
	 */
	public function test_basics() {

		$this->assertTrue( class_exists( __NAMESPACE__ . '\ButterBean' ) );

	}


	/**
	 * @testdox Given Product Post_Type ButterBean should render.
	 */
	public function test_butterbean_renders() {

		global $current_screen;

		$user_id = $this->factory->user->create(
			array(
				'role' => 'administrator',
			)
		);
		wp_set_current_user( $user_id );

		set_current_screen( 'edit-post' );
		$current_screen->post_type = Post_Type::SLUG;

		$butter_bean = new ButterBean();
		$butter_bean->load();

		butterbean_loader_100();

		do_action( $this->load_post_php );
		do_action( 'admin_footer' );

		$this->expectOutputRegex( '/<input type="submit" class="button button-primary" id="republish_product" name="republish_product" value="Reset">/' );

	}

	/**
	 * @testdox Given a Post_Type that is not valid ButterBean should not render.
	 */
	public function test_butterbean_does_not_render() {

		global $current_screen;

		$user_id = $this->factory->user->create(
			array(
				'role' => 'administrator',
			)
		);
		wp_set_current_user( $user_id );

		set_current_screen( 'edit-post' );
		$current_screen->post_type = 'Post';

		$butter_bean = new ButterBean();
		$butter_bean->load();

		butterbean_loader_100();

		do_action( $this->load_post_php );
		do_action( 'admin_footer' );

		$this->expectOutputString( '' );

	}

}
