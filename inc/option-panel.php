<?php

// Get Options
$opcfedd_opt = opcfedd_get_option();

// -> START Basic Fields
$sections[] = array(
    'title'  => __( 'General', 'onepage-checkout-for-edd' ),
    'desc'   => __( 'General Options', 'onepage-checkout-for-edd' ),
    'icon'   => 'el el-cogs',
    'classes'=> ' active ',
    'fields' => array(
        array(
            'id'       => 'cart-position',
            'type'     => 'button_set',
            'title'    => __( 'Cart Position', 'onepage-checkout-for-edd' ),
            'options' => array(
                '0' => __( 'Left', 'onepage-checkout-for-edd' ),
                '1' => __( 'Right', 'onepage-checkout-for-edd' ),
             ),
            'default'   => '0', 
        ),
        array(
            'id'       => 'show-close-btn',
            'type'     => 'button_set',
            'title'    => __( 'Show Close Button?', 'onepage-checkout-for-edd' ),
            'subtitle' => __( 'This will show a close button top of the panel ', 'onepage-checkout-for-edd' ),
            'options' => array(
                '1' => __( 'Yes', 'onepage-checkout-for-edd' ),
                '0' => __( 'No', 'onepage-checkout-for-edd' ),
             ),
            'default'   => '1',
        ),
        array(
            'id'       => 'panel-width',
            'type'     => 'slider',
            'title'    => __( 'Panel Width', 'onepage-checkout-for-edd' ),
            'subtitle' => __( 'Control panel size. Default: 40%', 'onepage-checkout-for-edd' ),
            'min' => 0,
            'step' => 1,
            'max' => 100,
            'default'  => 40,
        ),
        array(
            'id'       => 'panel-min-width',
            'type'     => 'slider',
            'title'    => __( 'Minimum Width', 'onepage-checkout-for-edd' ),
            'subtitle' => __( 'Control panel minimum width size for Tablet and larger devices. Default: 600px', 'onepage-checkout-for-edd' ),
            'min' => 500,
            'step' => 1,
            'max' => 1000,
            'default'  => 600,
        ),
        array(
            'id'       => 'panel-height',
            'type'     => 'slider',
            'title'    => __( 'Panel Height', 'onepage-checkout-for-edd' ),
            'subtitle' => __( 'Control panel size. Default: 70%', 'onepage-checkout-for-edd' ),
            'min' => 0,
            'step' => 1,
            'max' => 100,
            'default'  => 70,
        ),
    )
);

$sections[] = array(
    'title'  => __( 'Design', 'onepage-checkout-for-edd' ),
    'desc'   => __( 'Design Options', 'onepage-checkout-for-edd' ),
    'icon'   => 'el el-magic',
    'fields' => array(
        array(
            'id'       => 'panel-bg',
            'type'     => 'color',
            'title'    => __( 'Panel Background Color', 'onepage-checkout-for-edd' ),
            'default'  => '',
            'transparent'  => false,
        ),
         
        // Section Start
        array(
            'id'       => 'sticky-cart-section-start',
            'type'     => 'section_start',
            'title'    => __( 'Sticky Cart Toggler', 'onepage-checkout-for-edd' ),
            'indent' => true,
        ),
        array(
            'id'       => 'sticky-cart-bg',
            'type'     => 'color',
            'title'    => __( 'Background Color', 'onepage-checkout-for-edd' ),
            'default'  => '',
            'transparent'  => false,
        ),
        array(
            'id'       => 'sticky-cart-color',
            'type'     => 'color',
            'title'    => __( 'Cart Count Text Color', 'onepage-checkout-for-edd' ),
            'default'  => '',
            'transparent'  => false,
        ),
        array(
            'id'       => 'sticky-cart-count-bg',
            'type'     => 'color',
            'title'    => __( 'Cart Count Background Color', 'onepage-checkout-for-edd' ),
            'default'  => '',
            'transparent'  => false,
        ),
        array(
            'id'       => 'sticky-cart-icon-color',
            'type'     => 'color',
            'title'    => __( 'Icon Color', 'onepage-checkout-for-edd' ),
            'default'  => '',
            'transparent'  => false,
        ),
        // Section End
        array(
            'id'       => 'section-reset',
            'type'     => 'section_end',
            'indent'   => false,
        ),
        array(
            'id'       => 'panel-zindex',
            'type'     => 'slider',
            'title'    => __( 'Panel z-index', 'onepage-checkout-for-edd' ),
            'subtitle' => __( 'Control panel z-index from this option.', 'onepage-checkout-for-edd' ),
            'default' => 99999,
            'min' => 99999,
            'step' => 10,
            'max' => 999999,
        ),
        array(
            'id'       => 'custom-css',
            'type'     => 'ace_editor',
            'title'    => __( 'Custom CSS', 'onepage-checkout-for-edd' ),
            'subtitle' => __( 'If you want to make extra CSS then you can do it from here', 'onepage-checkout-for-edd' ),
            'mode'   => 'css',
            'theme'    => 'monokai',
        ),
    )
);

?>
<div class="tab-full-section">
    <form action="" class="am-options-form" method="post">
        <input type="hidden" name="action" Value="one-page-edd-options">
        <?php wp_nonce_field( 'one-page-edd-options-nonce' ); ?>
        <div class="tab-head-box">
            <div class="left-site">
                <div class="display_header">
                    <h2>One Page Checkout Settings</h2>
                </div>
                
            </div>
            <div class="left-right">
                <div class="right-btn">
                    <span class="spinner"></span>
                    <button type="submit" class="am-submit-btn">Save Changes</button>
                </div>
            </div>
        </div>

        <div class="tabs-container">

            <div class="box-head">
                <ul class="am-tab-nav">
                    <?php foreach( $sections as $key => $section ) : 
                        $classes = isset( $section['classes'] ) ? sanitize_text_field( $section['classes'] ) : '';
                        $title = isset( $section['title'] ) ? sanitize_text_field( $section['title'] ) : '';
                        ?>
                        <li class="<?php echo esc_attr( $classes ); ?>">
                            <a href="#<?php echo esc_attr( sanitize_title( $title ) . '-' . $key ); ?>"><?php echo esc_html( $title ); ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="box-content">
                <div class="from-buttom">
                    <span class="spinner"></span>
                    <button type="submit" class="am-submit-btn">Save Changes</button>
                </div>
                <div class="tab-box">
                    <?php foreach( $sections as $key => $section ) : 
                        $classes = isset( $section['classes'] ) ? sanitize_text_field( $section['classes'] ) : '';
                        $title = isset( $section['title'] ) ? sanitize_text_field( $section['title'] ) : '';
                        ?>
                        <div id="<?php echo esc_attr( sanitize_title( $title ) . '-' . $key ); ?>" class="tf-tab-content <?php echo esc_attr( $classes ); ?>">
                            <div class="tab-title-section">
                                <h2><?php echo esc_html( $title ); ?></h2>
                                <div class="tf-section-desc"><?php echo esc_html( $section['desc'] ); ?></div>
                            </div>

                            <!-- Fields -->
                            <?php if( isset( $section['fields'] ) && !empty( $section['fields'] ) ): ?>
                                <!-- Table Start -->
                                <table class="form-table" role="presentation"><tbody>
                                <?php foreach( $section['fields'] as $fkey => $field ) : 
                                    $field_type = isset( $field['type'] ) ? sanitize_text_field( $field['type'] ) : '';
                                    $field_id = isset( $field['id'] ) ? sanitize_text_field( $field['id'] ) : '';
                                    
                                    // Section Handle
                                    if( $field_type == 'section_start' ) {
                                        echo '<tr class="section-start"><td colspan="2"><table class="form-table form-table-section-indented" role="presentation"><tbody>';
                                    
                                    } elseif( $field_type == 'section_end' ) {
                                        echo '</tbody></table></td></tr>';
                                    
                                    }
                                    ?>
                                    <tr>
                                        <th scope="row" <?php if($field_type == 'section_start') {
                                            echo 'colspan="2"';
                                            echo ' class="section-start-th" ';
                                        } ?>>
                                            <div class="am_field_th">
                                                <?php echo esc_html( $field['title'] ); ?>
                                                <div class="description"><?php echo esc_html( $field['subtitle'] ); ?></div>
                                            </div>
                                        </th>

                                        <?php 
                                        // That's all for Section Start; Continue loop for other fields
                                        if($field_type == 'section_start') {
                                            continue;
                                        }
                                        ?>

                                        <td>
                                            <?php
                                            switch( $field_type ) {
                                                case 'radio': 
                                                case 'button_set': 
                                                    ?>
                                                    <div class="am_field_td">
                                                        <div class="am_field_radio">
                                                            <?php foreach( $field['options'] as $okey => $option ) : ?>
                                                                <label>
                                                                    <input type="radio" name="<?php echo esc_attr( opcfedd_field_name( $field_id ) ); ?>" value="<?php echo esc_attr( $okey ); ?>" <?php checked( $okey, $opcfedd_opt[$field_id] ); ?>>
                                                                    <?php echo esc_html( $option ); ?>
                                                                </label>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                    <?php
                                                    break;
                                                case 'section_start':
                                                    ?>
                                                
                                                    <?php
                                                    break;
                                                case 'section_end':
                                                    ?>
                                                
                                                    <?php
                                                    break;
                                                case 'color':
                                                    ?>
                                                    <input type="text" name="<?php echo esc_attr( opcfedd_field_name( $field_id ) ); ?>" value="<?php echo esc_attr( $opcfedd_opt[$field_id] ); ?>" class="color-picker" />
                                                    <?php
                                                    break;
                                                case 'slider':
                                                    ?>
                                                    <div class="am-slide-container">
                                                        <input type="number" class="am-slider-number" name="<?php echo esc_attr( opcfedd_field_name( $field_id ) ); ?>" value="<?php echo esc_attr( $opcfedd_opt[$field_id] ); ?>" min="<?php echo esc_attr( $field['min'] ); ?>" max="<?php echo esc_attr( $field['max'] ); ?>" step="<?php echo esc_attr( $field['step'] ); ?>" />
                                                        <input type="range" class="am-slider" value="<?php echo esc_attr( $opcfedd_opt[$field_id] ); ?>" min="<?php echo esc_attr( $field['min'] ); ?>" max="<?php echo esc_attr( $field['max'] ); ?>" step="<?php echo esc_attr( $field['step'] ); ?>" />
                                                    </div>
                                                    
                                                    <?php
                                                    break;
                                                case 'ace_editor':
                                                    ?>
                                                    <textarea id="custom-css" name="<?php echo esc_attr( opcfedd_field_name( $field_id ) ); ?>" class="ace-editor" data-mode="<?php echo esc_attr( $field['mode'] ); ?>" data-theme="<?php echo esc_attr( $field['theme'] ); ?>"><?php echo esc_textarea( $opcfedd_opt[$field_id] ); ?></textarea>
                                                    <?php
                                                    break;
                                                default : 
                                                    ?>
                                                    <input type="<?php echo esc_attr( $field['type'] ); ?>" name="<?php echo esc_attr( opcfedd_field_name( $field_id ) ); ?>" value="<?php echo esc_attr( $opcfedd_opt[$field_id] ); ?>" class="regular-text"/>
                                                    <?php
                                                    break;
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                    
                                    ?>
                                <?php endforeach; ?>
                                </tbody></table> <!-- Table End -->
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    jQuery(document).on('click', '.am-tab-nav a', function(e){
        e.preventDefault();
        let thisAttr = jQuery(this).attr('href')
    
        jQuery('.tf-tab-content').hide()
        jQuery(thisAttr).show()
    
        jQuery('.am-tab-nav li').removeClass('active')
        jQuery(this).closest('li').addClass('active')
    
      
        console.log(thisAttr)
    });

    jQuery(document).on('submit', '.am-options-form', function(e){
        e.preventDefault();
        let thisForm = jQuery(this);
        let formData = thisForm.serialize();

        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            beforeSend: function(){
                // disable submit button
                thisForm.find('.am-submit-btn').attr('disabled', 'disabled');
                // spinner
                thisForm.find('.spinner').addClass('is-active');
            },
            success: function(res){
                thisForm.find('.am-submit-btn').removeAttr('disabled');
                thisForm.find('.spinner').removeClass('is-active');
                console.log(res);
            }
        })

	
    });

    // Slider to Number Field JS
    jQuery(document).on('input', '.am-slider', function(e){
        e.preventDefault();
        let $this = jQuery(this);
        let thisVal = $this.val();
        // set number value
        $this.closest('.am-slide-container').find('.am-slider-number').val(thisVal);
    });

    // Number to Slider Field JS
    jQuery(document).on('input', '.am-slider-number', function(e){
        e.preventDefault();
        let $this = jQuery(this);
        let thisVal = $this.val();
        // set number value
        $this.closest('.am-slide-container').find('.am-slider').val(thisVal);
    });

    jQuery(document).ready(function($){
        jQuery('.color-picker').wpColorPicker();
    })
</script>