<div class="postbox">
    <h3 class="hndle"><?php _e( 'Spintax', 'page-generator-pro' ); ?></h3>

    <div class="wpzinc-option">
        <p class="description">
            <?php
            echo sprintf(
                /* translators: %1$s: Link to Documentation, already translated, %2$s: Link to Documentation, already translated */
                __( 'Specifies how to generate spintax from non-spun content when using the %1$s and %2$s functionality.', 'page-generator-pro' ),
                '<a href="' . $this->base->plugin->documentation_url . '/generate-using-spintax/#automatically-generate-spintax"  target="_blank" rel="noopener">' . __( 'Generate Spintax from Selected Content', 'page-generator-pro' ) . '</a>',
                '<a href="' . $this->base->plugin->documentation_url . '/generate-content/#fields--generation"  target="_blank" rel="noopener">' . __( 'Spin Content', 'page-generator-pro' ) . '</a>'
            );
            ?>
        </p>
    </div>

    <div class="wpzinc-option">
        <div class="left">
            <label for="frontend"><?php _e( 'Process on Frontend', 'page-generator-pro' ); ?></label>
        </div>
        <div class="right">
            <select name="<?php echo $this->base->plugin->name; ?>-spintax[frontend]" id="frontend" size="1">
                <option value=""<?php selected( $settings['frontend'], '' ); ?>><?php _e( 'No', 'page-generator-pro' ); ?></option>
                <option value="1"<?php selected( $settings['frontend'], '1' ); ?>><?php _e( 'Yes', 'page-generator-pro' ); ?></option>
            </select>

            <p class="description">
                <?php _e( 'If enabled, <strong>any</strong> Block Spintax and/or Spintax detected in <strong>any</strong> Post Content will be dynamically processed each time it is viewed.', 'page-generator-pro' ); ?>
                <br />
                <?php _e( 'Block Spintax and Spintax in any Content Group is <strong>always</strong> processed, <strong>regardless</strong> of this setting.', 'page-generator-pro' ); ?>
                
            </p>
        </div>
    </div>

    <div class="wpzinc-option">
        <div class="left">
            <label for="provider"><?php _e( 'Service', 'page-generator-pro' ); ?></label>
        </div>
        <div class="right">
            <select name="<?php echo $this->base->plugin->name; ?>-spintax[provider]" id="provider" size="1">
                <?php
                foreach ( $providers as $provider => $label ) {
                    ?>
                    <option value="<?php echo $provider; ?>"<?php selected( $settings['provider'], $provider ); ?>><?php echo $label; ?></option>
                    <?php
                }
                ?>
            </select>
            <p class="description">
                <?php _e( 'Optionally use a third party service to generate spintax.', 'page-generator-pro' ); ?>
            </p>
        </div>
    </div>

    <div id="skip-capitalized-words" class="wpzinc-option">
        <div class="left">
            <label for="skip_capitalized_words"><?php _e( 'Skip Capitalized Words', 'page-generator-pro' ); ?></label>
        </div>
        <div class="right">
            <select name="<?php echo $this->base->plugin->name; ?>-spintax[skip_capitalized_words]" id="skip_capitalized_words" size="1">
                <option value=""<?php selected( $settings['skip_capitalized_words'], '' ); ?>><?php _e( 'No', 'page-generator-pro' ); ?></option>
                <option value="1"<?php selected( $settings['skip_capitalized_words'], '1' ); ?>><?php _e( 'Yes', 'page-generator-pro' ); ?></option>
            </select>

            <p class="description">
                <?php _e( 'If enabled, capitalized words will NOT have spintax applied to them.  This is useful for branded terms.', 'page-generator-pro' ); ?>
            </p>
        </div>
    </div>

    <div id="skip-words" class="wpzinc-option">
        <div class="left">
            <label for="protected_words"><?php _e( 'Skip Words', 'page-generator-pro' ); ?></label>
        </div>
        <div class="right">
            <textarea name="<?php echo $this->base->plugin->name; ?>-spintax[protected_words]" id="protected_words" class="widefat" rows="10"><?php echo $settings['protected_words']; ?></textarea>
            <p class="description">
                <?php _e( 'Words defined here will NOT have spintax applied to them. Keywords and Shortcodes are never spun. Enter one word per line.', 'page-generator-pro' ); ?>
            </p>
        </div>
    </div>

    <!-- ChimpRewriter -->
    <div id="chimprewriter">
        <div class="wpzinc-option">
            <div class="left">
                <label for="chimprewriter_email_address"><?php _e( 'Email Address', 'page-generator-pro' ); ?></label>
            </div>
            <div class="right">
                <input type="text" name="<?php echo $this->base->plugin->name; ?>-spintax[chimprewriter_email_address]" id="chimprewriter_email_address" value="<?php echo $settings['chimprewriter_email_address']; ?>" class="widefat" />
                <p class="description">
                    <?php 
                    echo sprintf( 
                        /* translators: ChimpRewriter Registration Link */
                        __( 'The email address you use when logging into ChimpRewriter. %s if you don\'t have one.', 'page-generator-pro' ),
                        '<a href="' . $this->base->get_class( 'chimprewriter' )->get_registration_url() . '" target="_blank" rel="noopener">' . __( 'Register an account', 'page-generator-pro' ) . '</a>'
                    );
                    ?>
                </p>
            </div>
        </div>

        <div class="wpzinc-option">
            <div class="left">
                <label for="chimprewriter_api_key"><?php _e( 'API Key', 'page-generator-pro' ); ?></label>
            </div>
            <div class="right">
                <input type="text" name="<?php echo $this->base->plugin->name; ?>-spintax[chimprewriter_api_key]" id="chimprewriter_api_key" value="<?php echo $settings['chimprewriter_api_key']; ?>" class="widefat" />
                <p class="description">
                    <?php 
                    echo sprintf( 
                        /* translators: %1$s: ChimpRewriter Account Link, %2$s: ChimpRewriter Registration Link */
                        __( 'Enter your ChimpRewriter API key, %1$s. Don\'t have an account? %2$s.', 'page-generator-pro' ),
                        '<a href="' . $this->base->get_class( 'chimprewriter' )->get_account_url() . '" target="_blank" rel="noopener">' . __( 'which can be found here', 'page-generator-pro' ) . '</a>',
                    	'<a href="' . $this->base->get_class( 'chimprewriter' )->get_registration_url() . '" target="_blank" rel="noopener">' . __( 'Register an account', 'page-generator-pro' ) . '</a>'
                    );
                    ?>
                </p>
            </div>
        </div>

        <div class="wpzinc-option">
            <div class="left">
                <label for="chimprewriter_confidence_level"><?php _e( 'Confidence Level', 'page-generator-pro' ); ?></label>
            </div>
            <div class="right">
                <select name="<?php echo $this->base->plugin->name; ?>-spintax[chimprewriter_confidence_level]" id="chimprewriter_confidence_level" size="1">
                    <?php
                    foreach ( $confidence_levels['chimprewriter'] as $level => $label ) {
                        ?>
                        <option value="<?php echo $level; ?>"<?php selected( $settings['chimprewriter_confidence_level'], $level ); ?>>
                            <?php echo $label; ?>
                        </option>
                        <?php
                    }
                    ?>
                </select>

                <p class="description">
                    <?php _e( 'The higher the confidence level, the more readable the text and the less number of spins and variations produced.', 'page-generator-pro' ); ?>
                </p>
            </div>
        </div>

        <div class="wpzinc-option">
            <div class="left">
                <label for="chimprewriter_part_of_speech_level"><?php _e( 'Part of Speech Level', 'page-generator-pro' ); ?></label>
            </div>
            <div class="right">
                <select name="<?php echo $this->base->plugin->name; ?>-spintax[chimprewriter_part_of_speech_level]" id="chimprewriter_part_of_speech_level" size="1">
                    <?php
                    foreach ( $part_of_speech_levels['chimprewriter'] as $level => $label ) {
                        ?>
                        <option value="<?php echo $level; ?>"<?php selected( $settings['chimprewriter_part_of_speech_level'], $level ); ?>>
                            <?php echo $label; ?>
                        </option>
                        <?php
                    }
                    ?>
                </select>

                <p class="description">
                    <?php _e( 'The higher the Part of Speech level, the more readable the text and the less number of spins and variations produced.', 'page-generator-pro' ); ?>
                </p>
            </div>
        </div>

        <div class="wpzinc-option">
            <div class="left">
                <label for="chimprewriter_verify_grammar"><?php _e( 'Verify Grammar', 'page-generator-pro' ); ?></label>
            </div>
            <div class="right">
                <select name="<?php echo $this->base->plugin->name; ?>-spintax[chimprewriter_verify_grammar]" id="chimprewriter_verify_grammar" size="1">
                    <option value="0"<?php selected( $settings['chimprewriter_verify_grammar'], 0 ); ?>><?php _e( 'No', 'page-generator-pro' ); ?></option>
                    <option value="1"<?php selected( $settings['chimprewriter_verify_grammar'], 1 ); ?>><?php _e( 'Yes', 'page-generator-pro' ); ?></option>
                </select>

                <p class="description">
                    <?php _e( 'If enabled, grammar is verified on the resulting text to produce a very high quality spin.', 'page-generator-pro' ); ?>
                </p>
            </div>
        </div>

        <div class="wpzinc-option">
            <div class="left">
                <label for="chimprewriter_nested_spintax"><?php _e( 'Apply Nested Spintax', 'page-generator-pro' ); ?></label>
            </div>
            <div class="right">
                <select name="<?php echo $this->base->plugin->name; ?>-spintax[chimprewriter_nested_spintax]" id="chimprewriter_nested_spintax" size="1">
                    <option value=""<?php selected( $settings['chimprewriter_nested_spintax'], '' ); ?>><?php _e( 'No', 'page-generator-pro' ); ?></option>
                    <option value="1"<?php selected( $settings['chimprewriter_nested_spintax'], '1' ); ?>><?php _e( 'Yes', 'page-generator-pro' ); ?></option>
                </select>

                <p class="description">
                    <?php _e( 'If enabled, ChimpRewriter will spin single words inside already spun phrases.', 'page-generator-pro' ); ?>
                </p>
            </div>
        </div>

        <div class="wpzinc-option">
            <div class="left">
                <label for="chimprewriter_change_phrase_sentence_structure"><?php _e( 'Change Phrase and Sentence Structure', 'page-generator-pro' ); ?></label>
            </div>
            <div class="right">
                <select name="<?php echo $this->base->plugin->name; ?>-spintax[chimprewriter_change_phrase_sentence_structure]" id="chimprewriter_change_phrase_sentence_structure" size="1">
                    <option value=""<?php selected( $settings['chimprewriter_change_phrase_sentence_structure'], '' ); ?>><?php _e( 'No', 'page-generator-pro' ); ?></option>
                    <option value="1"<?php selected( $settings['chimprewriter_change_phrase_sentence_structure'], '1' ); ?>><?php _e( 'Yes', 'page-generator-pro' ); ?></option>
                </select>

                <p class="description">
                    <?php _e( 'If enabled, ChimpRewriter will change the entire structure of phrases and sentences.', 'page-generator-pro' ); ?>
                </p>
            </div>
        </div>
    </div>

    <!-- SpinnerChief -->
    <div id="spinnerchief">
        <div class="wpzinc-option">
            <div class="left">
                <label for="spinnerchief_username"><?php _e( 'Username', 'page-generator-pro' ); ?></label>
            </div>
            <div class="right">
                <input type="text" name="<?php echo $this->base->plugin->name; ?>-spintax[spinnerchief_username]" id="spinnerchief_username" value="<?php echo $settings['spinnerchief_username']; ?>" class="widefat" />
                <p class="description">
                    <?php 
                    echo sprintf( 
                        /* translators: SpinnerChief Registration Link */
                        __( 'The username you use when logging into SpinnerChief. %s if you don\'t have one.', 'page-generator-pro' ),
                        '<a href="' . $this->base->get_class( 'spinnerchief' )->get_registration_url() . '" target="_blank" rel="noopener">' . __( 'Register an account', 'page-generator-pro' ) . '</a>'
                    );
                    ?>
                </p>
            </div>
        </div>

        <div class="wpzinc-option">
            <div class="left">
                <label for="spinnerchief_password"><?php _e( 'Password', 'page-generator-pro' ); ?></label>
            </div>
            <div class="right">
                <input type="password" name="<?php echo $this->base->plugin->name; ?>-spintax[spinnerchief_password]" id="spinnerchief_password" value="<?php echo $settings['spinnerchief_password']; ?>" class="widefat" />
                <p class="description">
                    <?php 
                    echo sprintf( 
                        /* translators: SpinnerChief Registration Link */
                        __( 'The password you use when logging into SpinnerChief. %s if you don\'t have one.', 'page-generator-pro' ),
                        '<a href="' . $this->base->get_class( 'spinnerchief' )->get_registration_url() . '" target="_blank" rel="noopener">' . __( 'Register an account', 'page-generator-pro' ) . '</a>'
                    );
                    ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Spin Rewriter -->
    <div id="spin_rewriter">
        <div class="wpzinc-option">
            <div class="left">
                <label for="spin_rewriter_email_address"><?php _e( 'Email Address', 'page-generator-pro' ); ?></label>
            </div>
            <div class="right">
                <input type="text" name="<?php echo $this->base->plugin->name; ?>-spintax[spin_rewriter_email_address]" id="spin_rewriter_email_address" value="<?php echo $settings['spin_rewriter_email_address']; ?>" class="widefat" />
                <p class="description">
                    <?php 
                    echo sprintf( 
                        /* translators: Spin Rewriter Registration Link */
                        __( 'The email address you use when logging into Spin Rewriter. %s if you don\'t have one.', 'page-generator-pro' ),
                        '<a href="' . $this->base->get_class( 'spin_rewriter' )->get_registration_url() . '" target="_blank" rel="noopener">' . __( 'Register an account', 'page-generator-pro' ) . '</a>'
                    );
                    ?>
                </p>
            </div>
        </div>

        <div class="wpzinc-option">
            <div class="left">
                <label for="spin_rewriter_api_key"><?php _e( 'API Key', 'page-generator-pro' ); ?></label>
            </div>
            <div class="right">
                <input type="text" name="<?php echo $this->base->plugin->name; ?>-spintax[spin_rewriter_api_key]" id="spin_rewriter_api_key" value="<?php echo $settings['spin_rewriter_api_key']; ?>" class="widefat" />
                <p class="description">
                    <?php
                    echo sprintf( 
                        /* translators: %1$s: Spin Rewriter Account Link, %2$s: ChimpRewriter Registration Link */
                        __( 'Enter your Spin Rewriter API key, %1$s. Don\'t have an account? %2$s.', 'page-generator-pro' ),
                        '<a href="' . $this->base->get_class( 'spin_rewriter' )->get_account_url() . '" target="_blank" rel="noopener">' . __( 'which can be found here', 'page-generator-pro' ) . '</a>',
                    	'<a href="' . $this->base->get_class( 'spin_rewriter' )->get_registration_url() . '" target="_blank" rel="noopener">' . __( 'Register an account', 'page-generator-pro' ) . '</a>'
                    );
                    ?>
                </p>
            </div>
        </div>

        <div class="wpzinc-option">
            <div class="left">
                <label for="spin_rewriter_confidence_level"><?php _e( 'Confidence Level', 'page-generator-pro' ); ?></label>
            </div>
            <div class="right">
                <select name="<?php echo $this->base->plugin->name; ?>-spintax[spin_rewriter_confidence_level]" id="spin_rewriter_confidence_level" size="1">
                    <?php
                    foreach ( $confidence_levels['spin_rewriter'] as $level => $label ) {
                        ?>
                        <option value="<?php echo $level; ?>"<?php selected( $settings['spin_rewriter_confidence_level'], $level ); ?>>
                            <?php echo $label; ?>
                        </option>
                        <?php
                    }
                    ?>
                </select>

                <p class="description">
                    <?php _e( 'The higher the confidence level, the more readable the text and the less number of spins and variations produced.', 'page-generator-pro' ); ?>
                </p>
            </div>
        </div>

        <div class="wpzinc-option">
            <div class="left">
                <label for="spin_rewriter_nested_spintax"><?php _e( 'Apply Nested Spintax', 'page-generator-pro' ); ?></label>
            </div>
            <div class="right">
                <select name="<?php echo $this->base->plugin->name; ?>-spintax[spin_rewriter_nested_spintax]" id="spin_rewriter_nested_spintax" size="1">
                    <option value=""<?php selected( $settings['spin_rewriter_nested_spintax'], '' ); ?>><?php _e( 'No', 'page-generator-pro' ); ?></option>
                    <option value="1"<?php selected( $settings['spin_rewriter_nested_spintax'], '1' ); ?>><?php _e( 'Yes', 'page-generator-pro' ); ?></option>
                </select>

                <p class="description">
                    <?php _e( 'If enabled, Spin Rewriter will spin single words inside already spun phrases.', 'page-generator-pro' ); ?>
                </p>
            </div>
        </div>

        <div class="wpzinc-option">
            <div class="left">
                <label for="spin_rewriter_auto_sentences"><?php _e( 'Spin Sentences', 'page-generator-pro' ); ?></label>
            </div>
            <div class="right">
                <select name="<?php echo $this->base->plugin->name; ?>-spintax[spin_rewriter_auto_sentences]" id="spin_rewriter_auto_sentences" size="1">
                    <option value=""<?php selected( $settings['spin_rewriter_auto_sentences'], '' ); ?>><?php _e( 'No', 'page-generator-pro' ); ?></option>
                    <option value="1"<?php selected( $settings['spin_rewriter_auto_sentences'], '1' ); ?>><?php _e( 'Yes', 'page-generator-pro' ); ?></option>
                </select>

                <p class="description">
                    <?php _e( 'If enabled, Spin Rewriter will spin sentences.', 'page-generator-pro' ); ?>
                </p>
            </div>
        </div>

        <div class="wpzinc-option">
            <div class="left">
                <label for="spin_rewriter_auto_paragraphs"><?php _e( 'Spin Paragraphs', 'page-generator-pro' ); ?></label>
            </div>
            <div class="right">
                <select name="<?php echo $this->base->plugin->name; ?>-spintax[spin_rewriter_auto_paragraphs]" id="spin_rewriter_auto_paragraphs" size="1">
                    <option value=""<?php selected( $settings['spin_rewriter_auto_paragraphs'], '' ); ?>><?php _e( 'No', 'page-generator-pro' ); ?></option>
                    <option value="1"<?php selected( $settings['spin_rewriter_auto_paragraphs'], '1' ); ?>><?php _e( 'Yes', 'page-generator-pro' ); ?></option>
                </select>

                <p class="description">
                    <?php _e( 'If enabled, Spin Rewriter will spin paragraphs.', 'page-generator-pro' ); ?>
                </p>
            </div>
        </div>

        <div class="wpzinc-option">
            <div class="left">
                <label for="spin_rewriter_auto_new_paragraphs"><?php _e( 'Add Paragraphs', 'page-generator-pro' ); ?></label>
            </div>
            <div class="right">
                <select name="<?php echo $this->base->plugin->name; ?>-spintax[spin_rewriter_auto_new_paragraphs]" id="spin_rewriter_auto_new_paragraphs" size="1">
                    <option value=""<?php selected( $settings['spin_rewriter_auto_new_paragraphs'], '' ); ?>><?php _e( 'No', 'page-generator-pro' ); ?></option>
                    <option value="1"<?php selected( $settings['spin_rewriter_auto_new_paragraphs'], '1' ); ?>><?php _e( 'Yes', 'page-generator-pro' ); ?></option>
                </select>

                <p class="description">
                    <?php _e( 'If enabled, Spin Rewriter may add additional paragraphs.', 'page-generator-pro' ); ?>
                </p>
            </div>
        </div>

        <div class="wpzinc-option">
            <div class="left">
                <label for="spin_rewriter_auto_sentence_trees"><?php _e( 'Change Phrase and Sentence Structure', 'page-generator-pro' ); ?></label>
            </div>
            <div class="right">
                <select name="<?php echo $this->base->plugin->name; ?>-spintax[spin_rewriter_auto_sentence_trees]" id="spin_rewriter_auto_sentence_trees" size="1">
                    <option value=""<?php selected( $settings['spin_rewriter_auto_sentence_trees'], '' ); ?>><?php _e( 'No', 'page-generator-pro' ); ?></option>
                    <option value="1"<?php selected( $settings['spin_rewriter_auto_sentence_trees'], '1' ); ?>><?php _e( 'Yes', 'page-generator-pro' ); ?></option>
                </select>

                <p class="description">
                    <?php _e( 'If enabled, Spin Rewriter change the entire structure of phrases and sentences.', 'page-generator-pro' ); ?>
                </p>
            </div>
        </div>
    </div>

    <!-- WordAI -->
    <div id="wordai">
        <div class="wpzinc-option">
            <div class="left">
                <label for="wordai_email_address"><?php _e( 'Email Address', 'page-generator-pro' ); ?></label>
            </div>
            <div class="right">
                <input type="text" name="<?php echo $this->base->plugin->name; ?>-spintax[wordai_email_address]" id="wordai_email_address" value="<?php echo $settings['wordai_email_address']; ?>" class="widefat" />
                <p class="description">
                    <?php 
                    echo sprintf( 
                        /* translators: WordAI Registration Link */
                        __( 'The email address you use when logging into WordAI. %s if you don\'t have one.', 'page-generator-pro' ),
                        '<a href="' . $this->base->get_class( 'wordai' )->get_registration_url() . '" target="_blank" rel="noopener">' . __( 'Register an account', 'page-generator-pro' ) . '</a>'
                    );
                    ?>
                </p>
            </div>
        </div>

        <div class="wpzinc-option">
            <div class="left">
                <label for="wordai_api_key"><?php _e( 'API Key', 'page-generator-pro' ); ?></label>
            </div>
            <div class="right">
                <input type="text" name="<?php echo $this->base->plugin->name; ?>-spintax[wordai_api_key]" id="wordai_api_key" value="<?php echo $settings['wordai_api_key']; ?>" class="widefat" />
                <p class="description">
                    <?php 
                    echo sprintf( 
                        /* translators: %1$s: WordAI Account Link, %2$s: ChimpRewriter Registration Link */
                        __( 'Enter your WordAI API key, %1$s. Don\'t have an account? %2$s.', 'page-generator-pro' ),
                        '<a href="' . $this->base->get_class( 'wordai' )->get_account_url() . '" target="_blank" rel="noopener">' . __( 'which can be found here', 'page-generator-pro' ) . '</a>',
                    	'<a href="' . $this->base->get_class( 'wordai' )->get_registration_url() . '" target="_blank" rel="noopener">' . __( 'Register an account', 'page-generator-pro' ) . '</a>'
                    );
                    ?>
                </p>
            </div>
        </div>

        <div class="wpzinc-option">
            <div class="left">
                <label for="wordai_confidence_level"><?php _e( 'Confidence Level', 'page-generator-pro' ); ?></label>
            </div>
            <div class="right">
                <select name="<?php echo $this->base->plugin->name; ?>-spintax[wordai_confidence_level]" id="wordai_confidence_level" size="1">
                    <?php
                    foreach ( $confidence_levels['wordai'] as $level => $label ) {
                        ?>
                        <option value="<?php echo $level; ?>"<?php selected( $settings['wordai_confidence_level'], $level ); ?>>
                            <?php echo $label; ?>
                        </option>
                        <?php
                    }
                    ?>
                </select>

                <p class="description">
                    <?php _e( 'More Conservative will result in more readable text, but less spun.', 'page-generator-pro' ); ?>
                </p>
            </div>
        </div>
    </div>
</div>