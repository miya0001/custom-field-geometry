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
	public function __construct( $id, $title, $options = array() )
	{
		parent::__construct( $id, $title, $options );

		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ), 9 );
	}

	public function add( $post_type )
	{
		parent::add( $post_type );
		$meta_id = $this->id;
		add_action( 'rest_api_init', function() use ( $post_type, $meta_id ) {
			register_rest_field( $post_type, 'geojson', array(
					'get_callback' => function( $object ) use ( $meta_id ) {
						$meta = get_post_meta( $object['id'], $meta_id, true );
						return $meta['geojson'];
					},
					'schema' => null,
				)
			);
		} );
	}

	public function register_scripts()
	{
		wp_register_script(
			'riot',
			plugins_url( 'lib/riot/riot+compiler.min.js', dirname( __FILE__ ) ),
			array(),
			false,
			true
		);

		wp_register_script(
			'leaflet',
			plugins_url( 'lib/leaflet/dist/leaflet.js', dirname( __FILE__ ) ),
			array(),
			false,
			true
		);

		wp_register_style(
			'leaflet',
			plugins_url( 'lib/leaflet/dist/leaflet.css', dirname( __FILE__ ) ),
			array(),
			false
		);
	}

	/**
	 * Fires at the `admin_enqueue_scripts` hook.
	 *
	 * @param none
	 * @return none
	 */
	public function admin_enqueue_scripts( $hook )
	{
		wp_enqueue_script(
			'leaflet-draw',
			plugins_url( 'lib/leaflet-draw/dist/leaflet.draw.js', dirname( __FILE__ ) ),
			array( 'leaflet' ),
			false,
			true
		);

		wp_enqueue_script(
			'custom-field-geometry-admin',
			plugins_url( 'js/admin.js', dirname( __FILE__ ) ),
			array( 'riot', 'leaflet-draw' ),
			false,
			true
		);

		wp_enqueue_style(
			'leaflet-draw',
			plugins_url( 'lib/leaflet-draw/dist/leaflet.draw.css', dirname( __FILE__ ) ),
			array( 'leaflet' ),
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
		$tag = plugins_url( 'tags/geometry-admin.tag', dirname( __FILE__ ) );
		$values = get_post_meta( get_the_ID(), $this->id, true );
		?>
			<div id="<?php echo esc_attr( $this->id ); ?>" style="width=100%; height:500px; position:relative;"><geometry-admin></geometry-admin></div>
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

			<script>
				var custom_field_geometry_id = '<?php echo esc_js( $this->id ); ?>';
				var custom_field_geometry_options = <?php echo json_encode( $this->options ); ?>;
			</script>
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
			update_post_meta( $post_id, $this->id, $_POST[ $this->id ] );
		}
	}
}
