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
                [
                    'text'    => __( 'Wie viel Zeit hast du heute insgesamt im Liegen verbracht?', 'mecfs-tracker' ),
                    'options' => [
                        0  => __( 'Fast den ganzen Tag (22–24 h)', 'mecfs-tracker' ),
                        10 => __( 'Mehr als 18 Stunden', 'mecfs-tracker' ),
                        20 => __( 'Zwischen 12 und 18 Stunden', 'mecfs-tracker' ),
                        30 => __( 'Weniger als 12 Stunden', 'mecfs-tracker' ),
                    ],
                ],
                [
                    'text'    => __( 'Was war heute deine körperlich anspruchsvollste Aktivität?', 'mecfs-tracker' ),
                    'options' => [
                        0  => __( 'Nur Toilette / Zähneputzen o. ä.', 'mecfs-tracker' ),
                        20 => __( 'Körperpflege & Anziehen', 'mecfs-tracker' ),
                        30 => __( 'Kleine Mahlzeit zubereiten, 1–2 kurze Wege in der Wohnung', 'mecfs-tracker' ),
                        40 => __( 'Spaziergang, Haushalt, Einkauf oder > 30 Min aufrecht', 'mecfs-tracker' ),
                    ],
                ],
                [
                    'text'    => __( 'Wie stark haben sich heute deine Symptome nach körperlicher oder geistiger Aktivität verschlechtert?', 'mecfs-tracker' ),
                    'options' => [
                        0  => __( 'Stark – bereits nach kleinster Aktivität', 'mecfs-tracker' ),
                        20 => __( 'Deutlich – auch bei einfacher Aktivität', 'mecfs-tracker' ),
                        30 => __( 'Leicht – nach moderater Aktivität', 'mecfs-tracker' ),
                        40 => __( 'Keine spürbare Verschlechterung', 'mecfs-tracker' ),
                    ],
                ],
                [
                    'text'    => __( 'Wie lange konntest du dich heute am Stück geistig konzentrieren (z. B. lesen, zuhören, schreiben)?', 'mecfs-tracker' ),
                    'options' => [
                        10 => __( 'Unter 5 Minuten', 'mecfs-tracker' ),
                        20 => __( '5–15 Minuten', 'mecfs-tracker' ),
                        30 => __( '15–30 Minuten', 'mecfs-tracker' ),
                        40 => __( 'Über 30 Minuten', 'mecfs-tracker' ),
                    ],
                ],
            ];
            foreach ( $questions as $index => $question ) :
                ?>
                <fieldset class="bell-question">
                    <legend><?php echo esc_html( $question['text'] ); ?></legend>
                    <?php foreach ( $question['options'] as $value => $label ) : ?>
                        <label><input type="radio" name="bell_q<?php echo $index + 1; ?>" value="<?php echo esc_attr( $value ); ?>" required /><?php echo esc_html( $label ); ?></label>
                    <?php endforeach; ?>
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
