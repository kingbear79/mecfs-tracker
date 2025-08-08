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
        <form id="mecfs-tracker-form" class="d-flex flex-column gap-3">
            <input type="date" class="form-control" name="entry_date" value="<?php echo esc_attr( current_time( 'Y-m-d' ) ); ?>" />
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
                <div class="card bell-question">
                    <div class="card-body">
                        <label for="bell_q<?php echo $index + 1; ?>" class="form-label"><?php echo esc_html( $data['question'] ); ?></label>
                        <select class="form-select" id="bell_q<?php echo $index + 1; ?>" name="bell_q<?php echo $index + 1; ?>" required>
                            <option value="" selected disabled><?php esc_html_e( 'Bitte auswählen', 'mecfs-tracker' ); ?></option>
                            <?php foreach ( $data['options'] as $option ) : ?>
                                <option value="<?php echo esc_attr( $option['value'] ); ?>">
                                    <?php echo esc_html( $option['label'] ); ?> &rarr; <?php echo intval( $option['value'] ); ?> <?php esc_html_e( 'Punkte', 'mecfs-tracker' ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <?php
            endforeach;
            ?>
            <input type="hidden" name="bell_score" value="0" />
            <div id="emotion-questions" class="d-flex flex-column gap-3">
                <h4><?php esc_html_e( 'Emotionaler Zustand', 'mecfs-tracker' ); ?></h4>
                <?php
                $emotion_questions = [
                    [
                        'id'     => 'aengste',
                        'text'   => __( 'Wie stark fühlst Du Dich heute von Ängsten oder Sorgen belastet?', 'mecfs-tracker' ),
                        'scale'  => __( '1 = überhaupt nicht, 10 = sehr stark', 'mecfs-tracker' ),
                        'invert' => true,
                    ],
                    [
                        'id'     => 'stimmung',
                        'text'   => __( 'Wie ist Deine allgemeine Stimmung heute?', 'mecfs-tracker' ),
                        'scale'  => __( '1 = sehr schlecht, 10 = sehr gut', 'mecfs-tracker' ),
                        'invert' => false,
                    ],
                    [
                        'id'     => 'antrieb',
                        'text'   => __( 'Wie stark ist Dein innerer Antrieb heute?', 'mecfs-tracker' ),
                        'scale'  => __( '1 = keinerlei Antrieb, 10 = sehr starker Antrieb', 'mecfs-tracker' ),
                        'invert' => false,
                    ],
                    [
                        'id'     => 'depressivität',
                        'text'   => __( 'Wie stark fühlst Du Dich heute von depressiven Gedanken oder Gefühlen belastet?', 'mecfs-tracker' ),
                        'scale'  => __( '1 = überhaupt nicht, 10 = sehr stark', 'mecfs-tracker' ),
                        'invert' => true,
                    ],
                ];
                foreach ( $emotion_questions as $q ) :
                    ?>
                    <div class="emotion-question card">
                        <div class="card-body">
                            <label for="emotion-<?php echo esc_attr( $q['id'] ); ?>" class="form-label"><?php echo esc_html( $q['text'] ); ?></label>
                            <input type="range" class="form-range" name="emotion[<?php echo esc_attr( $q['id'] ); ?>]" id="emotion-<?php echo esc_attr( $q['id'] ); ?>" min="1" max="10" value="5" />
                            <span class="range-value">5</span>
                            <div class="slider-scale"><span>1</span><span>10</span></div>
                            <small class="text-muted"><?php echo esc_html( $q['scale'] ); ?></small>
                        </div>
                    </div>
                    <?php
                endforeach;
                ?>
            </div>
            <input type="hidden" name="emotion" value="0" />
            <div id="symptom-list" class="card">
                <div class="card-body">
                    <h4><?php esc_html_e( 'Symptome', 'mecfs-tracker' ); ?></h4>
                    <table class="symptom-table table">
                        <tbody id="symptom-table-body">
                            <?php foreach ( $symptoms as $symptom ) : ?>
                                <tr class="symptom-field">
                                    <td><?php echo esc_html( $symptom->label ); ?></td>
                                    <td>
                                        <input type="range" class="form-range" name="symptoms[<?php echo intval( $symptom->id ); ?>]" min="0" max="100" value="0" />
                                        <span class="range-value">0</span>
                                        <div class="slider-scale"><span>0</span><span>100</span></div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <button type="button" id="add-symptom" class="btn btn-outline-primary"><?php esc_html_e( 'Symptom hinzufügen', 'mecfs-tracker' ); ?></button>
                </div>
            </div>
            <textarea class="form-control" name="notes" placeholder="<?php esc_attr_e( 'Besonderheiten', 'mecfs-tracker' ); ?>"></textarea>
            <textarea class="form-control" name="positives" placeholder="<?php esc_attr_e( 'Was hat gutgetan?', 'mecfs-tracker' ); ?>"></textarea>
            <textarea class="form-control" name="negatives" placeholder="<?php esc_attr_e( 'Was hat belastet?', 'mecfs-tracker' ); ?>"></textarea>
            <button type="submit" class="btn btn-primary"><?php esc_html_e( 'Speichern', 'mecfs-tracker' ); ?></button>
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
