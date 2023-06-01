<?php

class WPTHEMENAME_Updater {

	/**
	 * The theme basename
	 *
	 * @var string
	 */
	private $theme_basename;

	/**
	 * The theme congiguration
	 *
	 * @var array
	 */
	private $theme_config;
	

	/**
	 * The GitHub repository info
	 *
	 * @var array
	 */
	private $github_response;
	

	/**
	 * The theme data
	 *
	 * @var array
	 */
	private $theme_data;

	
	/**
	 * The class construct
	 *
	 * @param  string $theme_basename The theme name
	 * @param  array  $theme_config The GitHub config.
	 * @return void
	 */
	public function __construct( $theme_basename, $theme_config ) {
		$this->theme_basename	 = $theme_basename;
		$this->theme_config    = $theme_config;
				
	}

	/**
	 * The check for updates init function.
	 *
	 * @return void
	 */
	public function check_update() {
		add_filter( 'pre_set_site_transient_update_themes', array( $this, 'pre_set_site_transient_update_themes' ), 10, 1 );
		add_filter( 'http_request_args', array( $this, 'set_header_token' ), 10, 2 );
		add_filter( 'upgrader_source_selection', array( $this, 'upgrader_source_selection'), 10, 4 );

		// add_filter( 'upgrader_post_install', array( $this, 'upgrader_post_install' ), 10, 3, 99 );

	}
	/**
	 * The check for updates transient function.
	 *
	 * @param  object $transient The transient object.
	 * @return object            The transient object.
	 */

	function pre_set_site_transient_update_themes( $transient ) {
		
		if ( !property_exists( $transient, 'checked' ) ) {
			return $transient;
		}
					
		if (	isset($transient->checked[ $this->theme_basename ] ) 
			&& ($transient->checked[ $this->theme_basename ] ) ) {
			// Query premium/private repo for updates.
			$update = $this->themename_check_for_updates( $this->theme_basename, $transient->checked[ $this->theme_basename ] );

		} else {
			$update = false;

		}
		

		if ( $update ) {
				// Update is available.
				// $update should be an array containing all of the fields in $item below.
				$transient->response[$this->theme_basename] = $update;

		} else {
				// No update is available.				
				$current_theme_version = false;
				if (	isset($this->theme_data ) 
				&& ($this->theme_data ) ) {
					$current_theme_version = $this->theme_data->get('Version');
				};

				$item = array(
						'theme'        => $this->theme_basename,
						'new_version'  => $current_theme_version,
						'url'          => '',
						'package'      => '',
						'requires'     => '',
						'requires_php' => '',
				);
				// Adding the "mock" item to the `no_update` property is required
				// for the enable/disable auto-updates links to correctly appear in UI.
				$transient->no_update[$this->theme_basename] = $item;

		}
	
		return $transient;

	}		


	function themename_check_for_updates( $theme_basename, $current_version ){

		// Recupero la infomración del repositorio,
		// en concreto la útitla release
		$this->github_repository_info();
				
		$this->get_theme_data();
		
							
		// Compare versions.
		if ( version_compare( $this->github_response['tag_name'], $current_version, 'gt' ) ) {
			// New version available.
			$theme = array(
				'theme'       => $this->theme_data->get_stylesheet(),
				'new_version' => $this->github_response['tag_name'],
				'url' 				=> $this->github_response['html_url'] ,
				'package'     => $this->github_response['zipball_url'],				
			);

		} else {
			$theme = false;

		}

		return  $theme;

	}

	/**
	 * Get the GitHub repository info.
	 *
	 * @return array
	 */
	public function github_repository_info() {		

		if ( null !== $this->github_response ) {
			return;
		}

		// REST API args.
		$args = array(
			'method'      => 'GET',
			'timeout'     => 5,
			'redirection' => 5,
			'httpversion' => '1.0',			
			'headers'     => array(),
			'sslverify'   => false,
		);

		if ( $this->theme_config['token'] ) {
			$args['headers'] = array( 'Authorization' => 'token ' . $this->theme_config['token'] );

		}

		// Get the response.
		$request = wp_remote_get( $this->theme_config['github_uri'], $args );

		// Check for error.
		if ( is_wp_error( $request ) ) {
      error_log(print_r($request, true));
			return;
		}
		// Decode the response.
		$response = json_decode( wp_remote_retrieve_body( $request ), true );

		if ( is_array( $response ) ) {						
			// Tomo la última versión publicada
			$response = current( $response );

		}

			
		// Is there access token?
		if ( $this->theme_config['token'] ) {
			// Update the zipball_url with the token.
			$response['zipball_url'] = add_query_arg( 'access_token', $this->theme_config['token'], $response['zipball_url'] );
		}
		
		$this->github_response = $response;				

	}



	/**
	 * Get the theme data.
	 *
	 * @return array
	 */
	public function get_theme_data() {
		if ( null !== $this->theme_data ) {
			return;
		}
		$this->theme_data = wp_get_theme( $this->theme_basename );

	}


	/**
	 * Change the args used on downloading the zipball.
	 *
	 * @param  array  $args HTTP request arguments.
	 * @param  string $url The downloading url.
	 * @return array
	 */
	public function set_header_token( $args, $url ) {
	
		$parse_url = wp_parse_url( $url );

		if ( 'api.github.com' === $parse_url['host'] && isset( $parse_url['query'] ) ) {

			parse_str( $parse_url['query'], $query );
			if ( isset( $query['access_token'] ) && $query['access_token'] ) {
				$args['headers']['Authorization'] = 'token ' . $query['access_token'];

			}

		}

		return $args;

	}


	/**
	 * Used for renaming of sources to ensure correct directory name.
	 *
	 * @since WordPress 4.4.0 The $hook_extra parameter became available.
	 *
	 * @param string                           $source        File path of $source.
	 * @param string                           $remote_source File path of $remote_source.
	 * @param \Plugin_Upgrader|\Theme_Upgrader $upgrader      An Upgrader object.
	 * @param array                            $hook_extra    Array of hook data.
	 *
	 * @return string|\WP_Error
	 */
	public function upgrader_source_selection( $source, $remote_source, $upgrader, $hook_extra = null ) {

		global $wp_filesystem;

		$slug            = null;
		$new_source      = null;
		$remote_source   = $wp_filesystem->wp_content_dir() . 'upgrade/';

		/*
		 * Rename themes.
		 */
		$slug       = $this->theme_basename;
		$new_source = trailingslashit( $remote_source ) . $slug;


		if ( trailingslashit( strtolower( $source ) ) !== trailingslashit( strtolower( $new_source ) ) ) {
			$result = move_dir( $source, $new_source, true );
			if ( is_wp_error( $result ) ) {
				return $result;
			}
		}


		// Clean up $new_source directory.
		add_action( 'upgrader_install_package_result', array( $this, 'delete_upgrade_source' ), 10, 2 );

		return trailingslashit( $new_source );
	}


	/**
	 * Deletes temporary upgrade directory.
	 *
	 * @since 10.10.0
	 * @uses `upgrader_install_package_result` filter
	 *
	 * @global WP_Filesystem_Base $wp_filesystem WordPress filesystem subclass.
	 *
	 * @param array|WP_Error $result     Result from WP_Upgrader::install_package().
	 * @param array          $hook_extra Extra arguments passed to hooked filters.
	 * @return bool
	 */
	public function delete_upgrade_source( $result, $hook_extra ) {
		global $wp_filesystem;

		if ( ! is_wp_error( $result ) && ! empty( $result['destination_name'] ) ) {
			$wp_filesystem->delete(
				$wp_filesystem->wp_content_dir() . "upgrade/{$result['destination_name']}",
				true
			);
		}

		return $result;
	}

}

