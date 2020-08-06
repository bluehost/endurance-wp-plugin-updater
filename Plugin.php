<?php

namespace Endurance_WP_Plugin_Updater;

/**
 * Class Plugin
 *
 * Get information about the current plugin context.
 *
 * @method author
 * @method author_uri
 * @method description
 * @method domain_path
 * @method license
 * @method license_uri
 * @method name
 * @method requires_wp_version
 * @method requires_php_version
 * @method text_domain
 * @method uri
 * @method version
 */
class Plugin {

	/**
	 * A collection of valid WordPress plugin file headers.
	 *
	 * @var array
	 */
	const HEADERS = array(
		'author'               => 'Author',
		'author_uri'           => 'AuthorURI',
		'description'          => 'Description',
		'domain_path'          => 'DomainPath',
		'license'              => 'License',
		'license_uri'          => 'LicenseURI',
		'name'                 => 'Name',
		'requires_wp_version'  => 'RequiresAtLeast',
		'requires_php_version' => 'RequiresPHP',
		'text_domain'          => 'TextDomain',
		'uri'                  => 'PluginURI',
		'version'              => 'Version',
	);

	/**
	 * The absolute path to the plugin file.
	 *
	 * @var string
	 */
	protected $file;

	/**
	 * Plugin constructor.
	 *
	 * @param string $file The absolute path to the plugin file.
	 */
	public function __construct( $file = __FILE__ ) {
		$this->file = $file;
	}

	/**
	 * Get the plugin basename.
	 *
	 * @return string
	 */
	public function basename() {
		return plugin_basename( $this->file );
	}

	/**
	 * Get the plugin slug.
	 *
	 * @return string
	 */
	public function slug() {
		return basename( plugin_dir_path( $this->file ) );
	}

	/**
	 * Get a specific plugin file header.
	 *
	 * @param string $name The plugin file header name.
	 *
	 * @return string
	 */
	protected function get_file_header( $name ) {
		$file_headers = $this->get_file_headers();

		return (string) isset( $file_headers[ $name ] ) ? $file_headers[ $name ] : '';
	}

	/**
	 * Get all plugin file headers.
	 *
	 * @return array
	 */
	protected function get_file_headers() {

		static $file_headers = array();

		if ( empty( $file_headers ) ) {

			if ( ! function_exists( 'get_plugin_data' ) ) {
				require wp_normalize_path( ABSPATH . '/wp-admin/includes/plugin.php' );
			}

			$file_headers = get_plugin_data( $this->file );
		}

		return $file_headers;
	}

	/**
	 * Magic method for fetching data from plugin file headers.
	 *
	 * @param string $name The method name.
	 * @param array  $args The method parameters.
	 *
	 * @return string
	 */
	public function __call( $name, $args ) {
		$value = '';
		if ( array_key_exists( $name, $this::HEADERS ) ) {
			$value = $this->get_file_header( $this::HEADERS[ $name ] );
		}

		return $value;
	}

}
