<?php
/**
 * Custom Field Geometry
 *
 * @package   miya/custom-field-map
 * @author    Takayuki Miyauchi
 * @license   GPL v2
 * @link      https://github.com/miya0001/custom-field-geometry
 */

namespace Miya\WP\Custom_Field;

class Geometry extends \Miya\WP\Custom_Field
{
	/**
	 * Fires at the `admin_enqueue_scripts` hook.
	 *
	 * @param none
	 * @return none
	 */
	public function admin_enqueue_scripts( $hook )
	{
		wp_enqueue_script(
			'riot',
			plugins_url( 'lib/riot/riot+compiler.min.js', dirname( __FILE__ ) ),
			array(),
			false,
			true
		);
		wp_enqueue_script(
			'leaflet',
			plugins_url( 'lib/leaflet/dist/leaflet.js', dirname( __FILE__ ) ),
			array(),
			false,
			true
		);
		wp_enqueue_script(
			'leaflet-draw',
			plugins_url( 'lib/leaflet-draw/dist/leaflet.draw.js', dirname( __FILE__ ) ),
			array( 'leaflet' ),
			false,
			true
		);
		wp_enqueue_script(
			'app',
			plugins_url( 'js/app.js', dirname( __FILE__ ) ),
			array( 'jquery', 'riot', 'leaflet-draw' ),
			false,
			true
		);

		wp_enqueue_style(
			'leaflet',
			plugins_url( 'lib/leaflet/dist/leaflet.css', dirname( __FILE__ ) ),
			array(),
			false
		);
		wp_enqueue_style(
			'leaflet-draw',
			plugins_url( 'lib/leaflet-draw/dist/leaflet.draw.css', dirname( __FILE__ ) ),
			array(),
			false
		);
	}

	/**
	 * Displays the form for the metabox. The nonce will be added automatically.
	 *
	 * @param object $post The object of the post.
	 * @param array $args The argumets passed from `add_meta_box()`.
	 * @return none
	 */
	public function form( $post, $args )
	{
		$tag = plugins_url( 'tags/map.tag', dirname( __FILE__ ) );
		$values = get_post_meta( get_the_ID(), '_' . $this->id, true );
		?>
			<div id="<?php echo esc_attr( $this->id ); ?>" style="width=100%; height:500px; position:relative;"><map></map></div>
			<input class="lat" type="hidden"
				name="<?php echo esc_attr( $this->id ); ?>[lat]"
				value="<?php echo @esc_attr( $values['lat'] ); ?>">
			<input class="lng" type="hidden"
				name="<?php echo esc_attr( $this->id ); ?>[lng]"
				value="<?php echo @esc_attr( $values['lng'] ); ?>">
			<input class="zoom" type="hidden"
				name="<?php echo esc_attr( $this->id ); ?>[zoom]"
				value="<?php echo @esc_attr( $values['zoom'] ); ?>">
			<input class="geojson" type="hidden"
				name="<?php echo esc_attr( $this->id ); ?>[geojson]"
				value="<?php echo @esc_attr( $values['geojson'] ); ?>">

			<script>var custom_field_geometry_id = '<?php echo esc_js( $this->id ); ?>';</script>
			<script src="<?php echo esc_url( $tag ); ?>" type="riot/tag"></script>
		<?php
	}

	/**
	 * Save the metadata from the `form()`. The nonce will be verified automatically.
	 *
	 * @param int $post_id The ID of the post.
	 * @return none
	 */
	public function save( $post_id )
	{
		if ( isset( $_POST[ $this->id ] ) ) {
			update_post_meta( $post_id, '_' . $this->id, $_POST[ $this->id ] );
		}
	}
}
