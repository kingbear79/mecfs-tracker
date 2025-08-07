<?php
namespace MECFSTracker;

class Frontend_Form {

    public function register() {
        add_action( 'wp_enqueue_scripts', [ $this, 'assets' ] );
        add_shortcode( 'mecfs_tracker_form', [ $this, 'render' ] );
        add_action( 'wp_ajax_mecfs_save_entry', [ $this, 'save_entry' ] );
        add_action( 'wp_ajax_nopriv_mecfs_save_entry', '__return_false' );
    }

    public function assets() {
        wp_enqueue_style( 'mecfs-tracker', plugins_url( 'assets/form.css', dirname( __FILE__ ) ), [], '0.1.0' );
        wp_enqueue_script( 'mecfs-tracker', plugins_url( 'assets/form.js', dirname( __FILE__ ) ), [ 'jquery' ], '0.1.0', true );
        wp_localize_script( 'mecfs-tracker', 'MECFSTracker', [
            'ajax'  => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'mecfs_entry' ),
        ] );
    }

    public function render() {
        if ( ! is_user_logged_in() ) {
            return '<p>' . esc_html__( 'Bitte anmelden.', 'mecfs-tracker' ) . '</p>';
        }
        ob_start();
        ?>
        <form id="mecfs-tracker-form">
            <input type="date" name="entry_date" value="<?php echo esc_attr( current_time( 'Y-m-d' ) ); ?>" />
            <?php
            $questions = [
                __( 'Wie belastbar fühlen Sie sich heute körperlich?', 'mecfs-tracker' ),
                __( 'Wie gut können Sie heute mentale Aufgaben bewältigen?', 'mecfs-tracker' ),
                __( 'Wie oft müssen Sie sich heute ausruhen?', 'mecfs-tracker' ),
                __( 'Wie weit können Sie sich heute außer Haus bewegen?', 'mecfs-tracker' ),
                __( 'Wie gut sind Ihre Symptome heute kontrollierbar?', 'mecfs-tracker' ),
            ];
            foreach ( $questions as $index => $question ) :
                ?>
                <fieldset class="bell-question">
                    <legend><?php echo esc_html( $question ); ?></legend>
                    <?php for ( $i = 0; $i <= 4; $i++ ) : ?>
                        <label><input type="radio" name="bell_q<?php echo $index + 1; ?>" value="<?php echo $i; ?>" required /> <?php echo $i; ?></label>
                    <?php endfor; ?>
                </fieldset>
                <?php
            endforeach;
            ?>
            <input type="hidden" name="bell_score" value="0" />
            <label><?php esc_html_e( 'Emotionaler Zustand', 'mecfs-tracker' ); ?></label>
            <input type="range" name="emotion" min="0" max="100" />
            <!-- TODO: Dynamische Symptome -->
            <textarea name="notes" placeholder="<?php esc_attr_e( 'Besonderheiten', 'mecfs-tracker' ); ?>"></textarea>
            <textarea name="positives" placeholder="<?php esc_attr_e( 'Was hat gutgetan?', 'mecfs-tracker' ); ?>"></textarea>
            <textarea name="negatives" placeholder="<?php esc_attr_e( 'Was hat belastet?', 'mecfs-tracker' ); ?>"></textarea>
            <button type="submit"><?php esc_html_e( 'Speichern', 'mecfs-tracker' ); ?></button>
        </form>
        <?php
        return ob_get_clean();
    }

    public function save_entry() {
        check_ajax_referer( 'mecfs_entry', 'nonce' );
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( 'not_logged_in' );
        }
        global $wpdb;
        $table = $wpdb->prefix . 'mecfs_entries';
        $wpdb->replace(
            $table,
            [
                'user_id'    => get_current_user_id(),
                'entry_date' => sanitize_text_field( $_POST['entry_date'] ?? '' ),
                'bell_score' => intval( $_POST['bell_score'] ?? 0 ),
                'emotion'    => intval( $_POST['emotion'] ?? 0 ),
                'notes'      => wp_kses_post( $_POST['notes'] ?? '' ),
                'positives'  => wp_kses_post( $_POST['positives'] ?? '' ),
                'negatives'  => wp_kses_post( $_POST['negatives'] ?? '' ),
            ]
        );
        wp_send_json_success();
    }
}
