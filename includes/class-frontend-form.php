<?php
namespace MECFSTracker;

class Frontend_Form {

    public function register() {
        add_action( 'wp_enqueue_scripts', [ $this, 'assets' ] );
        add_action( 'wp_ajax_mecfs_save_entry', [ $this, 'save_entry' ] );
        add_action( 'wp_ajax_nopriv_mecfs_save_entry', '__return_false' );
    }

    public function assets() {
        wp_enqueue_script(
            'mecfs-tracker',
            plugins_url( 'assets/form.js', MECFS_TRACKER_PLUGIN_FILE ),
            [ 'jquery' ],
            MECFS_TRACKER_VERSION,
            true
        );
        wp_localize_script( 'mecfs-tracker', 'MECFSTracker', [
            'ajax'  => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'mecfs_entry' ),
        ] );
    }

    public function render() {
        if ( ! is_user_logged_in() ) {
            return '<p>' . esc_html__( 'Bitte anmelden.', 'mecfs-tracker' ) . '</p>';
        }
        global $wpdb;
        $symptom_table = $wpdb->prefix . 'mecfs_symptoms';
        $symptoms      = $wpdb->get_results( "SELECT id, label FROM $symptom_table ORDER BY label" );
        ob_start();
        ?>
        <form id="mecfs-tracker-form">
            <input type="date" name="entry_date" value="<?php echo esc_attr( current_time( 'Y-m-d' ) ); ?>" />
            <?php
            $questions = [
                [
                    'question' => __( 'Wie viel Zeit hast du heute insgesamt im Liegen verbracht (außer Schlaf)?', 'mecfs-tracker' ),
                    'options'  => [
                        [ 'label' => __( 'Fast den ganzen Tag (22–24 h)', 'mecfs-tracker' ), 'value' => 0 ],
                        [ 'label' => __( 'Mehr als 18 h', 'mecfs-tracker' ), 'value' => 10 ],
                        [ 'label' => __( '12–18 h', 'mecfs-tracker' ), 'value' => 20 ],
                        [ 'label' => __( 'Unter 12 h', 'mecfs-tracker' ), 'value' => 30 ],
                    ],
                ],
                [
                    'question' => __( 'Was war heute deine körperlich anstrengendste Aktivität?', 'mecfs-tracker' ),
                    'options'  => [
                        [ 'label' => __( 'Nur Toilette / Zähneputzen', 'mecfs-tracker' ), 'value' => 0 ],
                        [ 'label' => __( 'Körperpflege & Anziehen', 'mecfs-tracker' ), 'value' => 20 ],
                        [ 'label' => __( 'Kleine Mahlzeit zubereiten, kurze Wege', 'mecfs-tracker' ), 'value' => 30 ],
                        [ 'label' => __( 'Spaziergang, Haushalt, > 30 Min aufrecht', 'mecfs-tracker' ), 'value' => 40 ],
                    ],
                ],
                [
                    'question' => __( 'Wie stark haben sich deine Symptome heute nach Aktivität verschlechtert?', 'mecfs-tracker' ),
                    'options'  => [
                        [ 'label' => __( 'Sehr stark, schon nach wenig Aktivität', 'mecfs-tracker' ), 'value' => 0 ],
                        [ 'label' => __( 'Deutlich spürbar', 'mecfs-tracker' ), 'value' => 20 ],
                        [ 'label' => __( 'Leicht', 'mecfs-tracker' ), 'value' => 30 ],
                        [ 'label' => __( 'Keine Verschlechterung', 'mecfs-tracker' ), 'value' => 40 ],
                    ],
                ],
                [
                    'question' => __( 'Wie lange konntest du dich heute am Stück konzentrieren (z. B. lesen, zuhören)?', 'mecfs-tracker' ),
                    'options'  => [
                        [ 'label' => __( 'Unter 5 Min', 'mecfs-tracker' ), 'value' => 10 ],
                        [ 'label' => __( '5–15 Min', 'mecfs-tracker' ), 'value' => 20 ],
                        [ 'label' => __( '15–30 Min', 'mecfs-tracker' ), 'value' => 30 ],
                        [ 'label' => __( 'Über 30 Min', 'mecfs-tracker' ), 'value' => 40 ],
                    ],
                ],
            ];
            foreach ( $questions as $index => $data ) :
                ?>
                <fieldset class="bell-question">
                    <legend><?php echo esc_html( $data['question'] ); ?></legend>
                    <?php foreach ( $data['options'] as $option ) : ?>
                        <label><input type="radio" name="bell_q<?php echo $index + 1; ?>" value="<?php echo esc_attr( $option['value'] ); ?>" required /> <?php echo esc_html( $option['label'] ); ?> &rarr; <?php echo intval( $option['value'] ); ?> <?php esc_html_e( 'Punkte', 'mecfs-tracker' ); ?></label>
                    <?php endforeach; ?>
                </fieldset>
                <?php
            endforeach;
            ?>
            <input type="hidden" name="bell_score" value="0" />
            <label><?php esc_html_e( 'Emotionaler Zustand', 'mecfs-tracker' ); ?></label>
            <input type="range" name="emotion" min="0" max="100" />
            <div id="symptom-list">
                <h4><?php esc_html_e( 'Symptome', 'mecfs-tracker' ); ?></h4>
                <?php foreach ( $symptoms as $symptom ) : ?>
                    <div class="symptom-field">
                        <span><?php echo esc_html( $symptom->label ); ?></span>
                        <input type="range" name="symptoms[<?php echo intval( $symptom->id ); ?>]" min="0" max="100" />
                    </div>
                <?php endforeach; ?>
                <div id="custom-symptoms"></div>
                <button type="button" id="add-symptom"><?php esc_html_e( 'Symptom hinzufügen', 'mecfs-tracker' ); ?></button>
            </div>
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
        $entry_date = sanitize_text_field( $_POST['entry_date'] ?? '' );
        $wpdb->replace(
            $table,
            [
                'user_id'    => get_current_user_id(),
                'entry_date' => $entry_date,
                'bell_score' => intval( $_POST['bell_score'] ?? 0 ),
                'emotion'    => intval( $_POST['emotion'] ?? 0 ),
                'notes'      => wp_kses_post( $_POST['notes'] ?? '' ),
                'positives'  => wp_kses_post( $_POST['positives'] ?? '' ),
                'negatives'  => wp_kses_post( $_POST['negatives'] ?? '' ),
            ]
        );

        $entry_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $table WHERE user_id = %d AND entry_date = %s", get_current_user_id(), $entry_date ) );

        $symptom_user_table = $wpdb->prefix . 'mecfs_user_symptoms';
        $wpdb->delete( $symptom_user_table, [ 'entry_id' => $entry_id ] );

        if ( ! empty( $_POST['symptoms'] ) && is_array( $_POST['symptoms'] ) ) {
            foreach ( $_POST['symptoms'] as $symptom_id => $severity ) {
                $wpdb->insert(
                    $symptom_user_table,
                    [
                        'user_id'    => get_current_user_id(),
                        'symptom_id' => intval( $symptom_id ),
                        'severity'   => intval( $severity ),
                        'entry_id'   => $entry_id,
                    ]
                );
            }
        }

        if ( ! empty( $_POST['new_symptoms'] ) && is_array( $_POST['new_symptoms'] ) ) {
            $symptom_table = $wpdb->prefix . 'mecfs_symptoms';
            foreach ( $_POST['new_symptoms'] as $symptom ) {
                $label = sanitize_text_field( $symptom['label'] ?? '' );
                if ( '' === $label ) {
                    continue;
                }
                $slug       = sanitize_title( $label );
                $symptom_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $symptom_table WHERE slug = %s", $slug ) );
                if ( ! $symptom_id ) {
                    $wpdb->insert( $symptom_table, [ 'slug' => $slug, 'label' => $label ] );
                    $symptom_id = $wpdb->insert_id;
                }
                $wpdb->insert(
                    $symptom_user_table,
                    [
                        'user_id'    => get_current_user_id(),
                        'symptom_id' => $symptom_id,
                        'severity'   => intval( $symptom['severity'] ?? 0 ),
                        'entry_id'   => $entry_id,
                    ]
                );
            }
        }
        wp_send_json_success();
    }
}
