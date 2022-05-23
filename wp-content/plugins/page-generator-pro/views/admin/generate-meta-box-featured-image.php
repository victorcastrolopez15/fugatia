<div class="wpzinc-option">
    <div class="full">
        <label for="featured_image_source"><?php _e( 'Image Source', 'page-generator-pro' ); ?></label>
    </div>
    <div class="full">
        <select name="<?php echo $this->base->plugin->name; ?>[featured_image_source]" id="featured_image_source" size="1" class="widefat">
            <?php
            foreach ( $featured_image_sources as $featured_image_source => $label ) {
                ?>
                <option value="<?php echo $featured_image_source; ?>"<?php selected( $this->settings['featured_image_source'], $featured_image_source ); ?>><?php echo $label; ?></option>
                <?php
            }
            ?>
        </select>
    </div>
</div>

<div class="wpzinc-vertical-tabbed-ui no-border featured_image id url creative_commons pexels pixabay wikipedia">
    <!-- Tabs -->
    <ul class="wpzinc-nav-tabs wpzinc-js-tabs" data-panels-container="#featured-image-container" data-panel=".featured-image-panel" data-active="wpzinc-nav-tab-vertical-active">
        <li class="wpzinc-nav-tab link">
            <a href="#featured-image-search-parameters" class="wpzinc-nav-tab-vertical-active">
                <?php _e( 'Search Parameters', 'page-generator-pro' ); ?>
            </a>
        </li>
        <li class="wpzinc-nav-tab tag">
            <a href="#featured-image-output">
                <?php _e( 'Output', 'page-generator-pro' ); ?>
            </a>
        </li>
        <li class="wpzinc-nav-tab aperture">
            <a href="#featured-image-exif">
                <?php _e( 'EXIF', 'page-generator-pro' ); ?>
            </a>
        </li>
    </ul>

    <!-- Content -->
    <div id="featured-image-container" class="wpzinc-nav-tabs-content no-padding">
        <!-- Search Parameters -->
        <div id="featured-image-search-parameters" class="featured-image-panel">
            <div class="postbox">
                <header>
                    <h3><?php _e( 'Search Parameters', 'page-generator-pro' ); ?></h3>
                    <p class="description">
                        <?php _e( 'Defines search query parameters to fetch an image.', 'page-generator-pro' ); ?>
                    </p>
                </header>

                <!-- Media Library Image Options -->
                <div class="wpzinc-option featured_image id">
                    <div class="left">
                        <label for="featured_image_media_library_title"><?php _e( 'Title', 'page-generator-pro' ); ?></label>
                    </div>
                    <div class="right">
                        <input type="text" name="<?php echo $this->base->plugin->name; ?>[featured_image_media_library_title]" id="featured_image_media_library_title" value="<?php echo $this->settings['featured_image_media_library_title']; ?>" placeholder="<?php _e( 'e.g. building', 'page-generator-pro' ); ?>" class="widefat" />
                    </div>

                    <p class="description">
                        <?php _e( 'Fetch an image at random with a partial or full match to the given Title.', 'page-generator-pro' ); ?>
                    </p>
                </div>
                <div class="wpzinc-option featured_image id">
                    <div class="left">
                        <label for="featured_image_media_library_caption"><?php _e( 'Caption', 'page-generator-pro' ); ?></label>
                    </div>
                    <div class="right">
                        <input type="text" name="<?php echo $this->base->plugin->name; ?>[featured_image_media_library_caption]" id="featured_image_media_library_caption" value="<?php echo $this->settings['featured_image_media_library_caption']; ?>" placeholder="<?php _e( 'e.g. building', 'page-generator-pro' ); ?>" class="widefat" />
                    </div>
                    <p class="description">
                        <?php _e( 'Fetch an image at random with a partial or full match to the given Caption.', 'page-generator-pro' ); ?>
                    </p>
                </div>
                <div class="wpzinc-option featured_image id">
                    <div class="left">
                        <label for="featured_image_media_library_alt"><?php _e( 'Alt Text', 'page-generator-pro' ); ?></label>
                    </div>
                    <div class="right">
                        <input type="text" name="<?php echo $this->base->plugin->name; ?>[featured_image_media_library_alt]" id="featured_image_media_library_alt" value="<?php echo $this->settings['featured_image_media_library_alt']; ?>" placeholder="<?php _e( 'e.g. building', 'page-generator-pro' ); ?>" class="widefat" />
                    </div>
                    <p class="description">
                        <?php _e( 'Fetch an image at random with a partial or full match to the given Alt Text.', 'page-generator-pro' ); ?>
                    </p>
                </div>
                <div class="wpzinc-option featured_image id">
                    <div class="left">
                        <label for="featured_image_media_library_description"><?php _e( 'Description', 'page-generator-pro' ); ?></label>
                    </div>
                    <div class="right">
                        <input type="text" name="<?php echo $this->base->plugin->name; ?>[featured_image_media_library_description]" id="featured_image_media_library_description" value="<?php echo $this->settings['featured_image_media_library_description']; ?>" placeholder="<?php _e( 'e.g. building', 'page-generator-pro' ); ?>" class="widefat" />
                    </div>
                    <p class="description">
                        <?php _e( 'Fetch an image at random with a partial or full match to the given Description.', 'page-generator-pro' ); ?>
                    </p>
                </div>
                <div class="wpzinc-option featured_image id">
                    <div class="left">
                        <label for="featured_image_media_library_operator"><?php _e( 'Operator', 'page-generator-pro' ); ?></label>
                    </div>
                    <div class="right">
                        <select name="<?php echo $this->base->plugin->name; ?>[featured_image_media_library_operator]" id="featured_image_media_library_operator" size="1" class="widefat">
                            <?php
                            foreach ( $operators as $operator => $label ) {
                                ?>
                                <option value="<?php echo $operator; ?>"<?php selected( $this->settings['featured_image_media_library_operator'], $operator ); ?>><?php echo $label; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <p class="description">
                        <?php _e( 'Determines whether images should match <b>all</b> or <b>any</b> of the Title, Caption, Alt Text and Descriptions specified above.', 'page-generator-pro' ); ?>
                    </p>
                </div>
                <div class="wpzinc-option featured_image id">
                    <div class="left">
                        <label for="featured_image_media_library_ids"><?php _e( 'Image IDs', 'page-generator-pro' ); ?></label>
                    </div>
                    <div class="right">
                        <input type="text" name="<?php echo $this->base->plugin->name; ?>[featured_image_media_library_ids]" id="featured_image_media_library_ids" value="<?php echo $this->settings['featured_image_media_library_ids']; ?>" placeholder="<?php _e( 'e.g. 100, 150, 200', 'page-generator-pro' ); ?>" class="widefat" />
                    </div>
                    <p class="description">
                        <?php _e( 'Comma separated list of Media Library Image ID(s) to use.  If multiple image IDs are specified, one will be chosen at random for each generated Page', 'page-generator-pro' ); ?>
                    </p>
                </div>
                <div class="wpzinc-option featured_image id">
                    <div class="left">
                        <label for="featured_image_media_library_min_id"><?php _e( 'Min. Image ID', 'page-generator-pro' ); ?></label>
                    </div>
                    <div class="right">
                        <input type="number" name="<?php echo $this->base->plugin->name; ?>[featured_image_media_library_min_id]" id="featured_image_media_library_min_id" value="<?php echo $this->settings['featured_image_media_library_min_id']; ?>" min="0" max="999999999" step="1" placeholder="<?php _e( 'e.g. 100', 'page-generator-pro' ); ?>" class="widefat" />
                    </div>
                    <p class="description">
                        <?php _e( 'Fetch an image whose ID matches or is greater than the given ID.', 'page-generator-pro' ); ?>
                    </p>
                </div>
                <div class="wpzinc-option featured_image id">
                    <div class="left">
                        <label for="featured_image_media_library_max_id"><?php _e( 'Max. Image ID', 'page-generator-pro' ); ?></label>
                    </div>
                    <div class="right">
                        <input type="number" name="<?php echo $this->base->plugin->name; ?>[featured_image_media_library_max_id]" id="featured_image_media_library_max_id" value="<?php echo $this->settings['featured_image_media_library_max_id']; ?>" min="0" max="999999999" step="1" placeholder="<?php _e( 'e.g. 200', 'page-generator-pro' ); ?>" class="widefat" />
                    </div>
                    <p class="description">
                        <?php _e( 'Fetch an image whose ID matches or is less than the given ID.', 'page-generator-pro' ); ?>
                    </p>
                </div>

                <!-- URL, Creative Commons, Pexels, Pixabay, Wikipedia -->
                <div class="wpzinc-option featured_image url creative_commons pexels pixabay wikipedia">
                    <div class="left">
                        <label for="featured_image" class="featured_image url"><?php _e( 'URL', 'page-generator-pro' ); ?></label>
                        <label for="featured_image" class="featured_image creative_commons pexels pixabay wikipedia"><?php _e( 'Term', 'page-generator-pro' ); ?></label>
                    </div>
                    <div class="right">
                        <input type="text" name="<?php echo $this->base->plugin->name; ?>[featured_image]" id="featured_image" value="<?php echo $this->settings['featured_image']; ?>" class="widefat" />
                    </div>

                    <p class="featured_image description url">
                        <?php _e( 'Enter an image URL. This can be a dynamic image URL; the contents will be saved in the WordPress Media Library as an image.', 'page-generator-pro' ); ?>
                    </p>
                    <p class="featured_image description creative_commons pexels pixabay">
                        <?php _e( 'The search term to use. For example, "laptop" would return an image of a laptop. Each generated page will use a different image based on this tag. You can use keyword tags and spintax here.', 'page-generator-pro' ); ?>
                    </p>
                </div>

                <!-- Creative Commons, Pexels and Pixabay -->
                <div class="wpzinc-option featured_image creative_commons pexels pixabay">
                    <div class="left">
                        <label for="featured_image_orientation"><?php _e( 'Image Orientation', 'page-generator-pro' ); ?></label>
                    </div>
                    <div class="right">
                        <select name="<?php echo $this->base->plugin->name; ?>[featured_image_orientation]" id="featured_image_orientation" size="1">
                            <?php
                            foreach ( $image_orientations as $image_orientation => $label ) {
                                ?>
                                <option value="<?php echo $image_orientation; ?>"<?php selected( $this->settings['featured_image_orientation'], $image_orientation ); ?>>
                                    <?php echo $label; ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <p class="description">
                        <?php _e( 'Restrict query to match images with the given orientation.', 'page-generator-pro' ); ?>
                    </p>
                </div>

                <!-- Pixabay -->
                <div class="wpzinc-option featured_image pixabay">
                    <div class="left">
                        <label for="featured_image_pixabay_language"><?php _e( 'Language', 'page-generator-pro' ); ?></label>
                    </div>
                    <div class="right">
                        <select name="<?php echo $this->base->plugin->name; ?>[featured_image_pixabay_language]" id="featured_image_pixabay_language" size="1">
                            <?php
                            foreach ( $pixabay_languages as $language => $label ) {
                                ?>
                                <option value="<?php echo $language; ?>"<?php selected( $this->settings['featured_image_pixabay_language'], $language ); ?>>
                                    <?php echo $label; ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <p class="description">
                        <?php _e( 'The language the search term is in.', 'page-generator-pro' ); ?>
                    </p>
                </div>
                <div class="wpzinc-option featured_image pixabay">
                    <div class="left">
                        <label for="featured_image_pixabay_image_type"><?php _e( 'Image Type', 'page-generator-pro' ); ?></label>
                    </div>
                    <div class="right">
                        <select name="<?php echo $this->base->plugin->name; ?>[featured_image_pixabay_image_type]" id="featured_image_pixabay_image_type" size="1">
                            <?php
                            foreach ( $pixabay_image_types as $image_type => $label ) {
                                ?>
                                <option value="<?php echo $image_type; ?>"<?php selected( $this->settings['featured_image_pixabay_image_type'], $image_type ); ?>>
                                    <?php echo $label; ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <p class="description">
                        <?php _e( 'The image type to search.', 'page-generator-pro' ); ?>
                    </p>
                </div>
                <div class="wpzinc-option featured_image pixabay">
                    <div class="left">
                        <label for="featured_image_pixabay_image_category"><?php _e( 'Image Category', 'page-generator-pro' ); ?></label>
                    </div>
                    <div class="right">
                        <select name="<?php echo $this->base->plugin->name; ?>[featured_image_pixabay_image_category]" id="featured_image_pixabay_image_category" size="1">
                            <?php
                            foreach ( $pixabay_image_categories as $image_category => $label ) {
                                ?>
                                <option value="<?php echo $image_category; ?>"<?php selected( $this->settings['featured_image_pixabay_image_category'], $image_category ); ?>>
                                    <?php echo $label; ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <p class="description">
                        <?php _e( 'The image category to search.', 'page-generator-pro' ); ?>
                    </p>
                </div>
                <div class="wpzinc-option featured_image pixabay">
                    <div class="left">
                        <label for="featured_image_pixabay_image_color"><?php _e( 'Image Color', 'page-generator-pro' ); ?></label>
                    </div>
                    <div class="right">
                        <select name="<?php echo $this->base->plugin->name; ?>[featured_image_pixabay_image_color]" id="featured_image_pixabay_image_color" size="1">
                            <?php
                            foreach ( $pixabay_image_colors as $image_color => $label ) {
                                ?>
                                <option value="<?php echo $image_color; ?>"<?php selected( $this->settings['featured_image_pixabay_image_color'], $image_color ); ?>>
                                    <?php echo $label; ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <p class="description">
                        <?php _e( 'Returns an image primarily comprising of the given color.', 'page-generator-pro' ); ?>
                    </p>
                </div>

                <!-- Wikipedia -->
                <div class="wpzinc-option featured_image wikipedia">
                    <div class="left">
                        <label for="featured_image_wikipedia_language"><?php _e( 'Language', 'page-generator-pro' ); ?></label>
                    </div>
                    <div class="right">
                        <select name="<?php echo $this->base->plugin->name; ?>[featured_image_wikipedia_language]" id="featured_image_wikipedia_language" size="1">
                            <?php
                            foreach ( $wikipedia_languages as $language => $label ) {
                                ?>
                                <option value="<?php echo $language; ?>"<?php selected( $this->settings['featured_image_wikipedia_language'], $language ); ?>>
                                    <?php echo $label; ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <p class="description">
                        <?php _e( 'The language the search term is in.', 'page-generator-pro' ); ?>
                    </p>
                </div>
            </div>
            <!-- /.postbox -->
        </div>
        <!-- /Search Parameters -->

        <!-- Output -->
        <div id="featured-image-output" class="featured-image-panel">
            <div class="postbox">
                <header>
                    <h3><?php _e( 'Output', 'page-generator-pro' ); ?></h3>
                    <p class="description">
                        <?php _e( 'Defines output parameters for the Featured Image.', 'page-generator-pro' ); ?>
                    </p>
                </header>

                <!-- Media Library -->
                <div class="wpzinc-option featured_image id">
                    <div class="left">
                        <label for="featured_image_title"><?php _e( 'Create as Copy', 'page-generator-pro' ); ?></label>
                    </div>
                    <div class="right">
                        <select name="<?php echo $this->base->plugin->name; ?>[featured_image_copy]" id="featured_image_copy" size="1">
                            <option value=""<?php selected( $this->settings['featured_image_copy'], '' ); ?>><?php _e( 'No', 'page-generator-pro' ); ?></option>
                            <option value="1"<?php selected( $this->settings['featured_image_copy'], '1' ); ?>><?php _e( 'Yes', 'page-generator-pro' ); ?></option>
                        </select>
                    </div>

                    <p class="description">
                        <?php _e( 'Store the found image as a new copy in the Media Library. This is recommended if defining Output and EXIF data that is Keyword-specific.', 'page-generator-pro' ); ?>
                    </p>
                </div>

                <div class="wpzinc-option featured_image id url creative_commons pexels pixabay wikipedia">
                    <div class="left">
                        <label for="featured_image_title"><?php _e( 'Title', 'page-generator-pro' ); ?></label>
                    </div>
                    <div class="right">
                        <input type="text" name="<?php echo $this->base->plugin->name; ?>[featured_image_title]" id="featured_image_title" value="<?php echo $this->settings['featured_image_title']; ?>" class="widefat" />
                    </div>

                    <p class="description">
                        <?php _e( 'The title to assign to the image.  Note: it is up to your Theme to output this when it outputs your Featured Image.', 'page-generator-pro' ); ?>
                    </p>
                </div>
                <div class="wpzinc-option featured_image id url creative_commons pexels pixabay wikipedia">
                    <div class="left">
                        <label for="featured_image_caption"><?php _e( 'Caption', 'page-generator-pro' ); ?></label>
                    </div>
                    <div class="right">
                        <input type="text" name="<?php echo $this->base->plugin->name; ?>[featured_image_caption]" id="featured_image_caption" value="<?php echo $this->settings['featured_image_caption']; ?>" class="widefat" />
                    </div>

                    <p class="description">
                        <?php _e( 'The caption to assign to the image.  Note: it is up to your Theme to output this when it outputs your Featured Image.', 'page-generator-pro' ); ?>
                    </p>
                </div>
                <div class="wpzinc-option featured_image id url creative_commons pexels pixabay wikipedia">
                    <div class="left">
                        <label for="featured_image_alt"><?php _e( 'Alt Tag', 'page-generator-pro' ); ?></label>
                    </div>
                    <div class="right">
                        <input type="text" name="<?php echo $this->base->plugin->name; ?>[featured_image_alt]" id="featured_image_alt" value="<?php echo $this->settings['featured_image_alt']; ?>" class="widefat" />
                    </div>

                    <p class="description">
                        <?php _e( 'The alt tag to assign to the image.  Note: it is up to your Theme to output this when it outputs your Featured Image.', 'page-generator-pro' ); ?>
                    </p>
                </div>
                <div class="wpzinc-option featured_image id url creative_commons pexels pixabay wikipedia">
                    <div class="left">
                        <label for="featured_image_description"><?php _e( 'Description', 'page-generator-pro' ); ?></label>
                    </div>
                    <div class="right">
                        <input type="text" name="<?php echo $this->base->plugin->name; ?>[featured_image_description]" id="featured_image_description" value="<?php echo $this->settings['featured_image_description']; ?>" class="widefat" />
                    </div>

                    <p class="description">
                        <?php _e( 'The description to assign to the image.  Note: it is up to your Theme to output this when it outputs your Featured Image.', 'page-generator-pro' ); ?>
                    </p>
                </div>
                <div class="wpzinc-option featured_image id url creative_commons pexels pixabay wikipedia">
                    <div class="left">
                        <label for="featured_image_filename"><?php _e( 'Filename', 'page-generator-pro' ); ?></label>
                    </div>
                    <div class="right">
                        <input type="text" name="<?php echo $this->base->plugin->name; ?>[featured_image_filename]" id="featured_image_filename" value="<?php echo $this->settings['featured_image_filename']; ?>" class="widefat" />
                    </div>

                    <p class="description">
                        <?php _e( 'Define the filename for the image, excluding the extension.', 'page-generator-pro' ); ?>
                    </p>
                </div>

            </div>
            <!-- /.postbox -->
        </div>
        <!-- /Output -->

        <!-- EXIF -->
        <div id="featured-image-exif" class="featured-image-panel">
            <div class="postbox">
                <header>
                    <h3><?php _e( 'EXIF', 'page-generator-pro' ); ?></h3>
                    <p class="description">
                        <?php _e( 'Defines EXIF metadata to store in the Featured Image.', 'page-generator-pro' ); ?>
                    </p>
                </header>

                <div class="wpzinc-option featured_image id url creative_commons pexels pixabay wikipedia">
                    <div class="left">
                        <label for="featured_image_exif_latitude"><?php _e( 'Latitude', 'page-generator-pro' ); ?></label>
                    </div>
                    <div class="right">
                        <input type="text" name="<?php echo $this->base->plugin->name; ?>[featured_image_exif_latitude]" id="featured_image_exif_latitude" value="<?php echo $this->settings['featured_image_exif_latitude']; ?>" class="widefat" />
                    </div>
                </div>

                <div class="wpzinc-option featured_image id url creative_commons pexels pixabay wikipedia">
                    <div class="left">
                        <label for="featured_image_exif_longitude"><?php _e( 'Longitude', 'page-generator-pro' ); ?></label>
                    </div>
                    <div class="right">
                        <input type="text" name="<?php echo $this->base->plugin->name; ?>[featured_image_exif_longitude]" id="featured_image_exif_longitude" value="<?php echo $this->settings['featured_image_exif_longitude']; ?>" class="widefat" />
                    </div>
                </div>

                <div class="wpzinc-option featured_image id url creative_commons pexels pixabay wikipedia">
                    <div class="left">
                        <label for="featured_image_exif_comments"><?php _e( 'Comments', 'page-generator-pro' ); ?></label>
                    </div>
                    <div class="right">
                        <input type="text" name="<?php echo $this->base->plugin->name; ?>[featured_image_exif_comments]" id="featured_image_exif_comments" value="<?php echo $this->settings['featured_image_exif_comments']; ?>" class="widefat" />
                    </div>
                </div>

                <div class="wpzinc-option featured_image id url creative_commons pexels pixabay wikipedia">
                    <div class="left">
                        <label for="featured_image_exif_description"><?php _e( 'Description', 'page-generator-pro' ); ?></label>
                    </div>
                    <div class="right">
                        <input type="text" name="<?php echo $this->base->plugin->name; ?>[featured_image_exif_description]" id="featured_image_exif_description" value="<?php echo $this->settings['featured_image_exif_description']; ?>" class="widefat" />
                    </div>
                </div>
            </div>
            <!-- /.postbox -->
        </div>
        <!-- /Output -->
    </div>
</div>