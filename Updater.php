<?php

namespace Endurance_WP_Plugin_Updater;

use Pimple\Container;

/**
 * Class Updater
 *
 * @package Endurance_WP_Plugin_Updater
 */
class Updater {

	public function __construct( $vendor, $package, $plugin_basename ) {

		$container = new Container( [
			'vendor'           => $vendor,
			'package'          => $package,
			'plugin_basename'  => $plugin_basename,
			'plugin'           => function () use ( $plugin_basename ) {
				return new Plugin( WP_PLUGIN_DIR . '/' . $plugin_basename );
			},
			'get_release_data' => function ( Container $c ) {
				$cache_key = str_replace( '-', '_', $c['plugin']->slug() ) . '_github_api_latest_release';
				$payload = get_transient( $cache_key );
				if ( ! $payload ) {
					$payload = (object) [];
					$query_string = '?' . http_build_query( [
							'vendorName'     => $c['vendor'],
							'packageName'    => $c['package'],
							'pluginBasename' => $c['plugin_basename']
						], null, '&' );
					$response = wp_remote_get( 'https://bluehost-wp-release.com/v1/' . $query_string );
					if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
						$body = wp_remote_retrieve_body( $response );
						if ( $body ) {
							$data = json_decode( $body );
							if ( $data ) {
								$payload = $data;
								set_transient( $cache_key, $payload, HOUR_IN_SECONDS * 6 );
							}
						}
					}
				}

				return $payload;
			},
		] );

		add_filter( 'site_transient_update_plugins', function ( $transient ) use ( $container ) {

			$plugin = $container['plugin'];
			$release = $container['get_release_data'];

			if ( isset( $release->new_version ) && version_compare( $release->new_version, $plugin->version(), '>' ) ) {
				$transient->response[ $plugin->basename() ] = $release;
			}

			return $transient;
		} );

		add_action( 'plugins_api', function ( $response, $action, $args ) use ( $container ) {
			$plugin = $container['plugin'];
			if ( isset( $args->slug ) && $args->slug === $plugin->slug() ) {
				$release = $container['get_release_data'];
				$response = (object) [
					'author'       => $plugin->author(),
					'homepage'     => $plugin->uri(),
					'last_updated' => $release->last_updated,
					'name'         => $plugin->name(),
					'plugin_name'  => $plugin->name(),
					'sections'     => [
						'Description' => $plugin->description(),
					],
					'slug'         => $plugin->slug(),
					'version'      => $release->new_version,
				];
			}

			return $response;
		}, 20, 3 );

	}

}
