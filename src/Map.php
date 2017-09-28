<?php
/**
 * Custom Field Map
 *
 * @package   miya/custom-field-map
 * @author    Takayuki Miyauchi
 * @license   GPL v2
 * @link      https://github.com/miya0001/custom-field-map
 */

namespace Miya\WP\Custom_Field;

class Map extends \Miya\WP\Custom_Field
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
			'https://cdn.jsdelivr.net/npm/riot@3.6/riot+compiler.min.js',
			array(),
			false,
			true
		);
		wp_enqueue_script(
			'leaflet',
			'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.0.3/leaflet.js',
			array(),
			false,
			true
		);
		wp_enqueue_script(
			'leaflet-draw',
			'https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/0.4.12/leaflet.draw.js',
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
			'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.0.3/leaflet.css',
			array(),
			false
		);
		wp_enqueue_style(
			'leaflet-draw',
			'https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/0.4.12/leaflet.draw.css',
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
