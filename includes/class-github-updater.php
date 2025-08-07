<?php
namespace MECFSTracker;

class GitHub_Updater {
    private $owner = 'OWNER'; // TODO: Replace with actual GitHub username/organization
    private $repo = 'mecfs-tracker';

    public function register() {
        add_filter( 'pre_set_site_transient_update_plugins', [ $this, 'check_for_update' ] );
    }

    public function check_for_update( $transient ) {
        if ( empty( $transient->checked ) ) {
            return $transient;
        }

        $plugin = plugin_basename( MECFS_TRACKER_PLUGIN_FILE );

        if ( ! isset( $transient->checked[ $plugin ] ) ) {
            return $transient;
        }

        $current_version = $transient->checked[ $plugin ];

        $response = wp_remote_get( sprintf( 'https://api.github.com/repos/%s/%s/releases/latest', $this->owner, $this->repo ) );
        if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
            return $transient;
        }

        $release = json_decode( wp_remote_retrieve_body( $response ) );
        if ( ! $release || empty( $release->tag_name ) ) {
            return $transient;
        }

        $remote_version = ltrim( $release->tag_name, 'v' );
        if ( version_compare( $remote_version, $current_version, '<=' ) ) {
            return $transient;
        }

        $package = '';
        if ( ! empty( $release->assets ) ) {
            foreach ( $release->assets as $asset ) {
                if ( $asset->name === $this->repo . '.zip' ) {
                    $package = $asset->browser_download_url;
                    break;
                }
            }
        }

        if ( empty( $package ) ) {
            $package = $release->zipball_url;
        }

        $transient->response[ $plugin ] = (object) [
            'slug'        => $this->repo,
            'plugin'      => $plugin,
            'new_version' => $remote_version,
            'package'     => $package,
            'url'         => $release->html_url,
        ];

        return $transient;
    }
}
