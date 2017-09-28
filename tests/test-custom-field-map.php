<?php

class Custom_Field_Map_Tests extends WP_UnitTestCase
{
	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public
	function test_enqueue_fires_on_post()
	{
		$map = new Miya\WP\Custom_Field\Geometry( 'map', 'Map' );
		$map->add( 'post' );

		convert_to_screen( 'post' );
		set_current_screen( 'post-new.php' );
		do_action( 'admin_enqueue_scripts', 'post-new.php' );
		$this->assertTrue( wp_style_is( 'leaflet' ) );
		$this->assertTrue( wp_script_is( 'riot' ) );
		$this->assertTrue( wp_script_is( 'leaflet' ) );
		$this->assertTrue( wp_script_is( 'app' ) );
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	function test_enqueue_fires_on_page()
	{
		register_post_type( 'my-post-type' );

		$map = new Miya\WP\Custom_Field\Geometry( 'map', 'Map' );
		$map->add( array( 'page', 'my-post-type' ) );

		$GLOBALS['current_screen'] = convert_to_screen( 'my-post-type' );
		do_action( 'admin_enqueue_scripts', 'post-new.php' );
		$this->assertTrue( wp_style_is( 'leaflet' ) );
		$this->assertTrue( wp_script_is( 'riot' ) );
		$this->assertTrue( wp_script_is( 'leaflet' ) );
		$this->assertTrue( wp_script_is( 'app' ) );
	}

	/**
	 * A test `form()` should be called.
	 */
	function test_form_should_be_called_with_empty_meta()
	{
		global $wp_meta_boxes;

		$test = new Miya\WP\Custom_Field\Geometry( 'hello', 'Hello' );
		$test->add( 'my-post-type' );

		do_action( 'add_meta_boxes' );

		$post_id = $this->factory->post->create( array( 'post_title' => 'Test Post' ) );

		$GLOBALS['post'] = get_post( $post_id );

		ob_start();
		$metaboxes = $wp_meta_boxes['my-post-type']['advanced']['default'];
		call_user_func(
			array( $metaboxes['hello']['callback'][0], $metaboxes['hello']['callback'][1] ),
			get_post( $post_id ),
			array()
		);
		$res = ob_get_contents();
		ob_end_clean();

		$this->assertRegExp( '#id="hello"#', $res );
		$this->assertRegExp( '#class="lat"#', $res );
		$this->assertRegExp( '#class="lng"#', $res );
		$this->assertRegExp( '#value="">#', $res );
		$this->assertRegExp( '#value="">#', $res );
	}
}
