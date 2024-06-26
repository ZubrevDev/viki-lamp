<?php

use XTS\Singleton;

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

class WOODMART_HB_Styles {
	/**
	 * Header elements css.
	 *
	 * @var array
	 */
	private $elements_css;

	public function get_elements_css() {
		return $this->elements_css;
	}

	public function get_all_css( $el, $options ) {
		$this->set_elements_css( $el );

		return $this->get_header_css( $options ) . $this->get_elements_css();
	}

	/**
	 * Set header elements css.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $el Header structure.
	 */
	public function set_elements_css( $el = false ) {
		if ( ! $el ) {
			$el = woodmart_get_config( 'header-builder-structure' );
		}

		$selector = 'whb-' . $el['id'];

		if ( isset( $el['content'] ) && is_array( $el['content'] ) ) {
			foreach ( $el['content'] as $element ) {
				$this->set_elements_css( $element );
			}
		}

		$css        = '';
		$rules      = '';
		$border_css = '';

		if ( isset( $el['params']['background'] ) && ( 'categories' !== $el['type'] ) ) {
			$rules .= $this->generate_background_css( $el['params']['background']['value'] );
		}

		if ( isset( $el['params']['border'] ) && ( 'categories' !== $el['type'] ) ) {
			$sides      = isset( $el['params']['border']['value']['sides'] ) ? $el['params']['border']['value']['sides'] : array( 'bottom' );
			$border_css = $this->generate_border_css( $el['params']['border']['value'], $sides );
		}

		if ( isset( $el['params']['border'] ) && isset( $el['params']['border']['value']['applyFor'] ) && 'boxed' === $el['params']['border']['value']['applyFor'] ) {
			$css .= '.' . $selector . '-inner { ' . $border_css . ' }';
		} elseif ( $border_css ) {
			$rules .= $border_css;
		}

		if ( 'categories' === $el['type'] ) {
			if ( isset( $el['params']['background'] ) && $el['params']['background']['value'] ) {
				$css .= '.' . $selector . ' .menu-opener { ' . $this->generate_background_css( $el['params']['background']['value'] ) . ' }';
			}

			if ( isset( $el['params']['border'] ) && $el['params']['border']['value'] ) {
				$sides = isset( $el['params']['border']['value']['sides'] ) ? $el['params']['border']['value']['sides'] : array( 'bottom' );
				$css  .= '.' . $selector . ' .menu-opener { ' . $this->generate_border_css( $el['params']['border']['value'], $sides ) . ' }';
			}

			if ( isset( $el['params']['more_cat_button'] ) && $el['params']['more_cat_button']['value'] ) {
				$count = $el['params']['more_cat_button_count']['value'] + 1;
				$css  .= '.' . $selector . '.wd-more-cat:not(.wd-show-cat) .item-level-0:nth-child(n+' . $count . '):not(:last-child) {
				    display: none;
				}.
				wd-more-cat .item-level-0:nth-child(n+' . $count . ') {
				    animation: wd-fadeIn .3s ease both;
				}';
			}
		}
		if ( $rules ) {
			$css .= "\n" . '.' . $selector . ' {' . "\n";
			$css .= "\t" . $rules . "\n";
			$css .= '}' . "\n";
		}

		$css_selectors = array();

		if ( isset( $el['params'] ) && $el['params'] ) {
			foreach ( $el['params'] as $params ) {
				if ( empty( $params['selectors'] ) || ( isset( $params['generate_zero'] ) && '' === $params['value'] ) || ( ! isset( $params['generate_zero'] ) && empty( $params['value'] ) ) || ! $this->check_dependencies( $params['id'] , $el ) ) {
					continue;
				}

				foreach ( $params['selectors'] as $selectors => $attributes ) {
					$active_selector = str_replace( '{{WRAPPER}}', $selector, $selectors );

					foreach ( $attributes as $attribute ) {
						$value = $params['value'];

						if ( isset( $params['value']['r'] ) && isset( $params['value']['g'] ) && isset( $params['value']['b'] ) && isset( $params['value']['a'] ) ) {
							$value = 'rgba(' . $params['value']['r'] . ', ' . $params['value']['g'] . ', ' . $params['value']['b'] . ', ' . $params['value']['a'] . ')';
						}

						$css_selectors[ $active_selector ][] = "\t" . str_replace( '{{VALUE}}', $value, $attribute ) . "\n";
					}
				}
			}
		}

		if ( $css_selectors ) {
			foreach ( $css_selectors as $selector => $css_atts ) {
				if ( ! $css_atts ) {
					continue;
				}

				$css .= "\n." . $selector . " {\n";
				$css .= implode( '', $css_atts );
				$css .= '}';
			}
		}

		$this->elements_css .= $css;
	}

	/**
	 * Generate background CSS code.
	 *
	 * @since 1.0.0
	 *
	 * @param array $bg Background data.
	 *
	 * @return string
	 */
	public function generate_background_css( $bg ) {
		$css = '';

		if ( isset( $bg['background-color'] ) ) {
			extract( $bg['background-color'] );
		}

		if ( isset( $r ) && isset( $g ) && isset( $b ) && isset( $a ) ) {
			$css .= 'background-color: rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . $a . ');';
		}

		if ( isset( $bg['background-image'] ) ) {
			extract( $bg['background-image'] );
		}

		if ( isset( $url ) ) {
			$css .= 'background-image: url(' . $url . ');';

			if ( isset( $bg['background-size'] ) ) {
				$css .= 'background-size: ' . $bg['background-size'] . ';';
			}

			if ( isset( $bg['background-attachment'] ) ) {
				$css .= 'background-attachment: ' . $bg['background-attachment'] . ';';
			}

			if ( isset( $bg['background-position'] ) ) {
				$css .= 'background-position: ' . $bg['background-position'] . ';';
			}

			if ( isset( $bg['background-repeat'] ) ) {
				$css .= 'background-repeat: ' . $bg['background-repeat'] . ';';
			}
		}

		return $css;
	}

	/**
	 * Generate border CSS code.
	 *
	 * @since 1.0.0
	 *
	 * @param array $border Border data.
	 * @param array $sides Sides data.
	 *
	 * @return string
	 */
	public function generate_border_css( $border, $sides ) {
		$css = '';

		if ( is_array( $border ) ) {
			extract( $border );
		}
		if ( isset( $color ) ) {
			extract( $color );
		}

		if ( isset( $r ) && isset( $g ) && isset( $b ) && isset( $a ) && isset( $width ) ) {
			$css .= 'border-color: rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . $a . ');';
		}

		foreach ( $sides as $side ) {
			if ( isset( $width ) ) {
				$css .= 'border-' . $side . '-width: ' . $width . 'px;';

				$css .= ( isset( $style ) ) ? 'border-' . $side . '-style: ' . $style . ';' : 'border-' . $side . '-style: solid;';
			}
		}

		return $css;
	}

	public function get_header_css( $options ) {
		$top_border    = ( isset( $options['top-bar']['border']['width'] ) ) ? (int) $options['top-bar']['border']['width'] : 0;
		$header_border = ( isset( $options['general-header']['border']['width'] ) ) ? (int) $options['general-header']['border']['width'] : 0;
		$bottom_border = ( isset( $options['header-bottom']['border']['width'] ) ) ? (int) $options['header-bottom']['border']['width'] : 0;
		$sticky_clone  = $options['sticky_clone'] && 'slide' === $options['sticky_effect'];

		$total_border_height = $top_border + $header_border + $bottom_border;

		$total_height = $options['top-bar']['height'] + $options['general-header']['height'] + $options['header-bottom']['height'];

		$mobile_height = $options['top-bar']['mobile_height'] + $options['general-header']['mobile_height'] + $options['header-bottom']['mobile_height'] + $total_border_height;

		$total_height += $total_border_height;

		if ( $options['boxed'] && ( $options['top-bar']['hide_desktop'] || ( ! $options['top-bar']['hide_desktop'] && $options['top-bar']['background'] ) ) ) {
			$total_height = $total_height + 30;
		}

		$sticky_elements = array_filter(
			$options,
			function( $el ) {
				return isset( $el['sticky'] ) && $el['sticky'];
			}
		);

		$total_sticky_height = 0;

		foreach ( $sticky_elements as $key => $el ) {
			if ( isset( $el['height'] ) && $el['height'] ) {
				$total_sticky_height += $el['height'];
			}
		}
		ob_start();

		?>
:root{
	--wd-top-bar-h: <?php echo esc_html( ! $options['top-bar']['hide_desktop'] && $options['top-bar']['height'] ? $options['top-bar']['height'] : 0.001 ); ?>px;
	--wd-top-bar-sm-h: <?php echo esc_html( ! $options['top-bar']['hide_mobile'] && $options['top-bar']['mobile_height'] ? $options['top-bar']['mobile_height'] : 0.001 ); ?>px;
	--wd-top-bar-sticky-h: <?php echo esc_html( ! $sticky_clone && $options['top-bar']['sticky'] && $options['top-bar']['sticky_height'] ? $options['top-bar']['sticky_height'] : 0.001 ); ?>px;

	--wd-header-general-h: <?php echo esc_html( ! $options['general-header']['hide_desktop'] && $options['general-header']['height'] ? $options['general-header']['height'] : 0.001 ); ?>px;
	--wd-header-general-sm-h: <?php echo esc_html( ! $options['general-header']['hide_mobile'] && $options['general-header']['mobile_height'] ? $options['general-header']['mobile_height'] : 0.001 ); ?>px;
	--wd-header-general-sticky-h: <?php echo esc_html( ! $sticky_clone && $options['general-header']['sticky'] && $options['general-header']['sticky_height'] ? $options['general-header']['sticky_height'] : 0.001 ); ?>px;

	--wd-header-bottom-h: <?php echo esc_html( ! $options['header-bottom']['hide_desktop'] && $options['header-bottom']['height'] ? $options['header-bottom']['height'] : 0.001 ); ?>px;
	--wd-header-bottom-sm-h: <?php echo esc_html( ! $options['header-bottom']['hide_mobile'] && $options['header-bottom']['mobile_height'] ? $options['header-bottom']['mobile_height'] : 0.001 ); ?>px;
	--wd-header-bottom-sticky-h: <?php echo esc_html( ! $sticky_clone && $options['header-bottom']['sticky'] && $options['header-bottom']['sticky_height'] ? $options['header-bottom']['sticky_height'] : 0.001 ); ?>px;

	--wd-header-clone-h: <?php echo esc_html( $sticky_clone ? $options['sticky_height'] : 0.001 ); ?>px;
}

<?php if ( ! $options['top-bar']['hide_desktop'] ) : ?>
<?php // DROPDOWN ALIGN BOTTOM IN TOP BAR. ?>
.whb-top-bar .wd-dropdown {
	margin-top: <?php echo esc_html( $options['top-bar']['height'] / 2 - 20 ); ?>px;
}

.whb-top-bar .wd-dropdown:after {
	height: <?php echo esc_html( $options['top-bar']['height'] / 2 - 10 ); ?>px;
}
<?php if ( ! $sticky_clone && $options['top-bar']['sticky'] ) : ?>
.whb-sticked .whb-top-bar .wd-dropdown {
	margin-top: <?php echo esc_html( $options['top-bar']['sticky_height'] / 2 - 20 ); ?>px;
}

.whb-sticked .whb-top-bar .wd-dropdown:after {
	height: <?php echo esc_html( $options['top-bar']['sticky_height'] / 2 - 10 ); ?>px;
}
<?php endif; ?>
<?php endif; ?>

<?php if ( ! $options['general-header']['hide_desktop'] && ! $sticky_clone && $options['general-header']['sticky'] ) : ?>
.whb-sticked .whb-general-header .wd-dropdown {
	margin-top: <?php echo esc_html( $options['general-header']['sticky_height'] / 2 - 20 ); ?>px;
}

.whb-sticked .whb-general-header .wd-dropdown:after {
	height: <?php echo esc_html( $options['general-header']['sticky_height'] / 2 - 10 ); ?>px;
}
<?php endif; ?>

<?php if ( ! $options['header-bottom']['hide_desktop'] ) : ?>
<?php // DROPDOWN ALIGN BOTTOM IN HEADER BOTTOM. ?>
.whb-header-bottom .wd-dropdown {
	margin-top: <?php echo esc_html( $options['header-bottom']['height'] / 2 - 20 ); ?>px;
}

.whb-header-bottom .wd-dropdown:after {
	height: <?php echo esc_html( $options['header-bottom']['height'] / 2 - 10 ); ?>px;
}

<?php if ( ! $sticky_clone && $options['header-bottom']['sticky'] ) : ?>
.whb-sticked .whb-header-bottom .wd-dropdown {
	margin-top: <?php echo esc_html( $options['header-bottom']['sticky_height'] / 2 - 20 ); ?>px;
}

.whb-sticked .whb-header-bottom .wd-dropdown:after {
	height: <?php echo esc_html( $options['header-bottom']['sticky_height'] / 2 - 10 ); ?>px;
	margin-top:0!important;

}
<?php endif; ?>

<?php // HEADER ELEMENTS BOTTOM. ?>
.whb-header .whb-header-bottom .wd-header-cats {
	margin-top: -<?php echo esc_html( $header_border ); ?>px;
	margin-bottom: -<?php echo esc_html( $bottom_border ); ?>px;
	height: calc(100% + <?php echo esc_html( $header_border + $bottom_border ); ?>px);
}
<?php endif; ?>

<?php if ( $sticky_clone ) : ?>
<?php // DROPDOWN ALIGN BOTTOM IN HEADER CLONE. ?>
.whb-clone.whb-sticked .wd-dropdown {
	margin-top: <?php echo esc_html( $options['sticky_height'] / 2 - 20 ); ?>px;
}

.whb-clone.whb-sticked .wd-dropdown:after {
	height: <?php echo esc_html( $options['sticky_height'] / 2 - 10 ); ?>px;
}
<?php endif; ?>

<?php // HEADER ELEMENTS. ?>
@media (min-width: 1025px) {
	<?php if ( ! $options['top-bar']['hide_desktop'] ) : ?>
	.whb-top-bar-inner {
		height: <?php echo esc_html( $options['top-bar']['height'] ); ?>px;
		max-height: <?php echo esc_html( $options['top-bar']['height'] ); ?>px;
	}

	<?php if ( ! $sticky_clone ) : ?>
	.whb-sticked .whb-top-bar-inner {
		height: <?php echo esc_html( $options['top-bar']['sticky_height'] ); ?>px;
		max-height: <?php echo esc_html( $options['top-bar']['sticky_height'] ); ?>px;
	}
	<?php endif; ?>
	<?php endif; ?>

	<?php if ( ! $options['general-header']['hide_desktop'] ) : ?>
	.whb-general-header-inner {
		
	}

	<?php if ( ! $sticky_clone ) : ?>
	.whb-sticked .whb-general-header-inner {
		
	}
	<?php endif; ?>
	<?php endif; ?>

	<?php if ( ! $options['header-bottom']['hide_desktop'] ) : ?>
	.whb-header-bottom-inner {
		height: <?php echo esc_html( $options['header-bottom']['height'] ); ?>px;
		max-height: <?php echo esc_html( $options['header-bottom']['height'] ); ?>px;
	}

	<?php if ( ! $sticky_clone ) : ?>
	.whb-sticked .whb-header-bottom-inner {
		height: <?php echo esc_html( $options['header-bottom']['sticky_height'] ); ?>px;
		max-height: <?php echo esc_html( $options['header-bottom']['sticky_height'] ); ?>px;
	}
	<?php endif; ?>
	<?php endif; ?>

	<?php if ( $sticky_clone ) : ?>
	<?php // HEIGHT OF HEADER CLONE. ?>
	.whb-clone .whb-general-header-inner {
		
	}
	<?php endif; ?>

	<?php if ( $options['overlap'] ) : ?>
	<?php // HEADER OVERCONTENT. ?>
	.wd-header-overlap .title-size-small {
		padding-top: <?php echo esc_html( $total_height + 20 ); ?>px;
	}

	.wd-header-overlap .title-size-default {
		padding-top: <?php echo esc_html( $total_height + 60 ); ?>px;
	}

	.wd-header-overlap .title-size-large {
		padding-top: <?php echo esc_html( $total_height + 100 ); ?>px;
	}

	<?php // HEADER OVERCONTENT WHEN SHOP PAGE TITLE TURN OFF. ?>
	.wd-header-overlap .without-title.title-size-small {
		padding-top: <?php echo esc_html( $total_height ); ?>px;
	}

	.wd-header-overlap .without-title.title-size-default {
		padding-top: <?php echo esc_html( $total_height + 35 ); ?>px;
	}

	.wd-header-overlap .without-title.title-size-large {
		padding-top: <?php echo esc_html( $total_height + 60 ); ?>px;
	}

	<?php // HEADER OVERCONTENT ON SINGLE PRODUCT. ?>
	.single-product .whb-overcontent:not(.whb-custom-header) {
		padding-top: <?php echo esc_html( $total_height ); ?>px;
	}
	<?php endif; ?>
}

@media (max-width: 1024px) {
	<?php if ( ! $options['top-bar']['hide_mobile'] ) : ?>
	.whb-top-bar-inner {
		height: <?php echo esc_html( $options['top-bar']['mobile_height'] ); ?>px;
		max-height: <?php echo esc_html( $options['top-bar']['mobile_height'] ); ?>px;
	}
	<?php endif; ?>

	<?php if ( ! $options['general-header']['hide_mobile'] ) : ?>
	.whb-general-header-inner {
		
	}
	<?php endif; ?>

	<?php if ( ! $options['header-bottom']['hide_mobile'] ) : ?>
	.whb-header-bottom-inner {
		height: <?php echo esc_html( $options['header-bottom']['mobile_height'] ); ?>px;
		max-height: <?php echo esc_html( $options['header-bottom']['mobile_height'] ); ?>px;
	}
	<?php endif; ?>

	<?php if ( $sticky_clone ) : ?>
	<?php // HEIGHT OF HEADER CLONE. ?>
	.whb-clone .whb-general-header-inner {
		
	}
	<?php endif; ?>

	<?php if ( $options['overlap'] ) : ?>
	<?php // HEADER OVERCONTENT. ?>
	.wd-header-overlap .page-title {
		padding-top: <?php echo esc_html( $mobile_height + 15 ); ?>px;
	}

	<?php // HEADER OVERCONTENT WHEN SHOP PAGE TITLE TURN OFF. ?>
	.wd-header-overlap .without-title.title-shop {
		padding-top: <?php echo esc_html( $mobile_height ); ?>px;
	}

	<?php // HEADER OVERCONTENT ON SINGLE PRODUCT. ?>
	.single-product .whb-overcontent:not(.whb-custom-header) {
		padding-top: <?php echo esc_html( $mobile_height ); ?>px;
	}
	<?php endif; ?>
}
		<?php

		return ob_get_clean();
	}

	/**
	 * Check whether dependencies for a specific option in the specified element are fulfilled.
	 *
	 * @param  string $option_id - The id of the dependency option to check.
	 * @param  array $el - List of settings for this item.
	 * @return bool - return true if all dependencies for this option have been met, or the no dependencies option.
	 */
	private function check_dependencies( $option_id, $el ) {
		$res = array();

		if ( ! isset( $el['params'][ $option_id ]['requires'] ) ) {
			return true;
		}

		foreach ( $el['params'][ $option_id ]['requires'] as $require_option => $require_condition ) {
			if ( is_array( $require_condition['value'] ) ) {
				foreach ( $require_condition['value'] as $require_condition_value ) {
					if ( 'equal' === $require_condition['comparison'] ) {
						if ( $el['params'][ $require_option ]['value'] === $require_condition_value ) {
							$res[ $require_option ] = true;
							break;
						} else {
							$res[ $require_option ] = false;
						}
					} else {
						$res[ $require_option ] = $el['params'][ $require_option ]['value'] !== $require_condition_value;
					}
				}
			} else {
				if ( 'equal' === $require_condition['comparison'] ) {
					$res[ $require_option ] = $el['params'][ $require_option ]['value'] === $require_condition['value'];
				} else {
					$res[ $require_option ] = $el['params'][ $require_option ]['value'] !== $require_condition['value'];
				}
			}
		}

		return ! in_array( false, $res );
	}
}
