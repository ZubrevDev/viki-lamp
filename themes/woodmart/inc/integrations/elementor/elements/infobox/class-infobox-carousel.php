<?php
/**
 * Information box map.
 */

namespace XTS\Elementor;

use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Infobox_Carousel extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'wd_infobox_carousel';
	}

	/**
	 * Get widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Information box carousel', 'woodmart' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'wd-icon-infobox-carousel';
	}

	/**
	 * Get widget categories.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'wd-elements' ];
	}

	/**
	 * Register the widget controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		/**
		 * Content tab.
		 */

		/**
		 * General settings.
		 */
		$this->start_controls_section(
			'general_content_section',
			[
				'label' => esc_html__( 'General', 'woodmart' ),
			]
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'banner_tabs' );

		$repeater->start_controls_tab(
			'link_tab',
			[
				'label' => esc_html__( 'Link', 'woodmart' ),
			]
		);

		$repeater->add_control(
			'link',
			[
				'label'       => esc_html__( 'Link', 'woodmart' ),
				'description' => esc_html__( 'Enter URL if you want this banner to have a link.', 'woodmart' ),
				'type'        => Controls_Manager::URL,
				'default'     => [
					'url'         => '#',
					'is_external' => false,
					'nofollow'    => false,
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'icon_tab',
			[
				'label' => esc_html__( 'Icon', 'woodmart' ),
			]
		);

		$repeater->add_control(
			'icon_type',
			[
				'label'   => esc_html__( 'Type', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'icon' => esc_html__( 'Icon', 'woodmart' ),
					'text' => esc_html__( 'Text', 'woodmart' ),
				],
				'default' => 'text',
			]
		);

		$repeater->add_control(
			'icon_text',
			[
				'label'     => esc_html__( 'Text', 'woodmart' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '01.',
				'condition' => [
					'icon_type' => 'text',
				],
			]
		);

		$repeater->add_control(
			'image',
			[
				'label'     => esc_html__( 'Choose image', 'woodmart' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => [
					'icon_type' => [ 'icon' ],
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				'default'   => 'thumbnail',
				'separator' => 'none',
				'condition' => [
					'icon_type' => [ 'icon' ],
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'text_tab',
			[
				'label' => esc_html__( 'Text', 'woodmart' ),
			]
		);

		$repeater->add_control(
			'subtitle',
			[
				'label'   => esc_html__( 'Subtitle', 'woodmart' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => 'Infobox subtitle text',
			]
		);

		$repeater->add_control(
			'title',
			[
				'label'   => esc_html__( 'Title', 'woodmart' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => 'Infobox title, click to edit.',
			]
		);

		$repeater->add_control(
			'content',
			[
				'label'   => esc_html__( 'Content', 'woodmart' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => 'Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web designs.',
			]
		);

		$repeater->add_control(
			'btn_text',
			[
				'label'   => esc_html__( 'Button text', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Read more',
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		/**
		 * Repeater settings
		 */
		$this->add_control(
			'content_repeater',
			[
				'type'        => Controls_Manager::REPEATER,
				'title_field' => '{{{ title }}}',
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'title'    => 'Infobox title.',
						'subtitle' => 'Infobox subtitle',
					],
					[
						'title'    => 'Infobox title.',
						'subtitle' => 'Infobox subtitle',
					],
					[
						'title'    => 'Infobox title.',
						'subtitle' => 'Infobox subtitle',
					],
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Style tab.
		 */

		/**
		 * General settings.
		 */
		$this->start_controls_section(
			'general_style_section',
			[
				'label' => esc_html__( 'General', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'style',
			[
				'label'   => esc_html__( 'Style', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'base'     => esc_html__( 'Base', 'woodmart' ),
					'border'   => esc_html__( 'Bordered', 'woodmart' ),
					'shadow'   => esc_html__( 'Shadow', 'woodmart' ),
					'bg-hover' => esc_html__( 'Background on hover', 'woodmart' ),
				],
				'default' => 'base',
			]
		);

		$this->add_control(
			'woodmart_color_scheme',
			[
				'label'   => esc_html__( 'Color Scheme', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					''      => esc_html__( 'Inherit', 'woodmart' ),
					'light' => esc_html__( 'Light', 'woodmart' ),
					'dark'  => esc_html__( 'Dark', 'woodmart' ),
				],
				'default' => '',
			]
		);

		$this->add_control(
			'alignment',
			[
				'label'   => esc_html__( 'Text alignment', 'woodmart' ),
				'type'    => 'wd_buttons',
				'options' => [
					'left'   => [
						'title' => esc_html__( 'Left', 'woodmart' ),
						'image' => WOODMART_ASSETS_IMAGES . '/settings/align/left.jpg',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'woodmart' ),
						'image' => WOODMART_ASSETS_IMAGES . '/settings/align/center.jpg',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'woodmart' ),
						'image' => WOODMART_ASSETS_IMAGES . '/settings/align/right.jpg',
					],
				],
				'default' => 'center',
			]
		);

		$this->add_control(
			'image_alignment',
			[
				'label'   => esc_html__( 'Image alignment', 'woodmart' ),
				'type'    => 'wd_buttons',
				'options' => [
					'left'   => [
						'title' => esc_html__( 'Left', 'woodmart' ),
						'image' => WOODMART_ASSETS_IMAGES . '/settings/infobox/position/left.png',
					],
					'top' => [
						'title' => esc_html__( 'Top', 'woodmart' ),
						'image' => WOODMART_ASSETS_IMAGES . '/settings/infobox/position/top.png',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'woodmart' ),
						'image' => WOODMART_ASSETS_IMAGES . '/settings/infobox/position/right.png',
					],
				],
				'default' => 'top',
			]
		);

		$this->add_control(
			'image_vertical_alignment',
			array(
				'label'     => esc_html__( 'Vertical alignment', 'woodmart' ),
				'type'      => 'wd_buttons',
				'options'   => array(
					'top'    => array(
						'title' => esc_html__( 'Top', 'woodmart' ),
						'image' => WOODMART_ASSETS_IMAGES . '/settings/infobox/vertical-position/top.png',
					),
					'middle' => array(
						'title' => esc_html__( 'Middle', 'woodmart' ),
						'image' => WOODMART_ASSETS_IMAGES . '/settings/infobox/vertical-position/middle.png',
					),
					'bottom' => array(
						'title' => esc_html__( 'Bottom', 'woodmart' ),
						'image' => WOODMART_ASSETS_IMAGES . '/settings/infobox/vertical-position/bottom.png',
					),
				),
				'default'   => 'top',
				'condition' => array(
					'image_alignment' => array( 'left', 'right' ),
				),
			)
		);

		$this->add_control(
			'title_size',
			[
				'label'   => esc_html__( 'Predefined size', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'default'     => esc_html__( 'Default (18px)', 'woodmart' ),
					'small'       => esc_html__( 'Small (16px)', 'woodmart' ),
					'large'       => esc_html__( 'Large (26px)', 'woodmart' ),
					'extra-large' => esc_html__( 'Extra Large (36px)', 'woodmart' ),
				],
				'default' => 'default',
			]
		);

		$this->add_responsive_control(
			'padding',
			array(
				'label'      => esc_html__( 'Padding', 'woodmart' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .wd-info-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => esc_html__( 'Border radius', 'woodmart' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .wd-info-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Icon settings.
		 */
		$this->start_controls_section(
			'icon_content_section',
			[
				'label' => esc_html__( 'Icon', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_style',
			[
				'label'   => esc_html__( 'Style', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'simple'      => esc_html__( 'Simple', 'woodmart' ),
					'with-bg'     => esc_html__( 'With background', 'woodmart' ),
					'with-border' => esc_html__( 'With border', 'woodmart' ),
				],
				'default' => 'simple',
			]
		);

		$this->add_control(
			'icon_bg_color',
			[
				'label'     => esc_html__( 'Icon background color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wd-info-box .info-box-icon' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'icon_style' => 'with-bg',
				],
			]
		);

		$this->add_control(
			'icon_bg_hover_color',
			[
				'label'     => esc_html__( 'Icon background color on hover', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wd-info-box:hover .info-box-icon' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'icon_style' => 'with-bg',
				],
			]
		);

		$this->add_control(
			'icon_border_color',
			[
				'label'     => esc_html__( 'Icon border color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wd-info-box .info-box-icon' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'icon_style' => 'with-border',
				],
			]
		);

		$this->add_control(
			'icon_border_hover_color',
			[
				'label'     => esc_html__( 'Icon border color on hover', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wd-info-box:hover .info-box-icon' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'icon_style' => 'with-border',
				],
			]
		);

		$this->add_control(
			'icon_text_size',
			[
				'label'   => esc_html__( 'Icon text size', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'default' => esc_html__( 'Default (52px)', 'woodmart' ),
					'small'   => esc_html__( 'Small (38px)', 'woodmart' ),
					'large'   => esc_html__( 'Large (74px)', 'woodmart' ),
				],
				'default' => 'default',
			]
		);

		$this->add_control(
			'icon_text_color',
			[
				'label'     => esc_html__( 'Icon text color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .box-with-text' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'rounding_size',
			array(
				'label'     => esc_html__( 'Rounding', 'woodmart' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''       => esc_html__( 'Inherit', 'woodmart' ),
					'0'      => esc_html__( '0', 'woodmart' ),
					'5'      => esc_html__( '5', 'woodmart' ),
					'8'      => esc_html__( '8', 'woodmart' ),
					'12'     => esc_html__( '12', 'woodmart' ),
					'custom' => esc_html__( 'Custom', 'woodmart' ),
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--wd-brd-radius: {{VALUE}}px;',
				),
			)
		);

		$this->add_control(
			'custom_rounding_size',
			array(
				'label'      => esc_html__( 'Custom rounding', 'woodmart' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 1,
						'max'  => 300,
						'step' => 1,
					),
					'%'  => array(
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--wd-brd-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'rounding_size' => array( 'custom' ),
				),
			)
		);

		$this->end_controls_section();

		/**
		 * Hover settings.
		 */
		$this->start_controls_section(
			'hover_style_section',
			[
				'label'     => esc_html__( 'Background', 'woodmart' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'bg_hover_colorpicker',
			[
				'label'   => esc_html__( 'Background type', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'colorpicker' => esc_html__( 'Color or image', 'woodmart' ),
					'gradient'    => esc_html__( 'Gradient', 'woodmart' ),
				],
				'default' => 'colorpicker',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'box_bg_color_gradient',
				'fields_options' => array(
					'background' => array(
						'label' => esc_html__( 'Background gradient',  'woodmart' ),
					),
				),
				'types'          => [ 'gradient' ],
				'selector'       => '{{WRAPPER}} .wd-info-box',
				'condition'      => [
					'bg_hover_colorpicker' => 'gradient',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'box_bg_hover_color_gradient',
				'fields_options' => array(
					'background' => array(
						'label' => esc_html__( 'Hover background gradient', 'woodmart' ),
					),
				),
				'types'          => [ 'gradient' ],
				'selector'       => '{{WRAPPER}} .wd-info-box:after',
				'condition'      => [
					'style'                => 'bg-hover',
					'bg_hover_colorpicker' => 'gradient',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'           => 'box_bg_image',
				'fields_options' => array(
					'background' => array(
						'label' => esc_html__( 'Background image', 'woodmart' ),
					),
					'image'      => array(
						'responsive' => false,
					),
					'position'   => array(
						'label'      => esc_html__( 'Background position', 'woodmart' ),
						'responsive' => false,
						'options'    => [
							''              => esc_html_x( 'Default', 'Background Control', 'elementor' ),
							'center center' => esc_html_x( 'Center Center', 'Background Control', 'elementor' ),
							'center left'   => esc_html_x( 'Center Left', 'Background Control', 'elementor' ),
							'center right'  => esc_html_x( 'Center Right', 'Background Control', 'elementor' ),
							'top center'    => esc_html_x( 'Top Center', 'Background Control', 'elementor' ),
							'top left'      => esc_html_x( 'Top Left', 'Background Control', 'elementor' ),
							'top right'     => esc_html_x( 'Top Right', 'Background Control', 'elementor' ),
							'bottom center' => esc_html_x( 'Bottom Center', 'Background Control', 'elementor' ),
							'bottom left'   => esc_html_x( 'Bottom Left', 'Background Control', 'elementor' ),
							'bottom right'  => esc_html_x( 'Bottom Right', 'Background Control', 'elementor' ),
						],
					),
					'repeat'     => array(
						'label'      => esc_html__( 'Background repeat', 'woodmart' ),
						'responsive' => false,
					),
					'size'       => array(
						'label'      => esc_html__( 'Background size', 'woodmart' ),
						'responsive' => false,
						'options'    => [
							''        => esc_html_x( 'Default', 'Background Control', 'elementor' ),
							'cover'   => esc_html_x( 'Cover', 'Background Control', 'elementor' ),
							'contain' => esc_html_x( 'Contain', 'Background Control', 'elementor' ),
						],
					),
				),
				'include'        => array( 'background', 'image', 'position', 'repeat', 'size' ),
				'types'          => array( 'classic' ),
				'selector'       => '{{WRAPPER}} .wd-info-box',
				'condition'      => array(
					'bg_hover_colorpicker' => 'colorpicker',
				),
			)
		);

		$this->add_control(
			'box_bg_color',
			[
				'label'     => esc_html__( 'Background color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wd-info-box' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'bg_hover_colorpicker' => 'colorpicker',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'           => 'box_bg_hover_image',
				'fields_options' => array(
					'background' => array(
						'label' => esc_html__( 'Hover background image', 'woodmart' ),
					),
					'image'      => array(
						'responsive' => false,
					),
					'position'   => array(
						'label'      => esc_html__( 'Background position', 'woodmart' ),
						'responsive' => false,
						'options'    => [
							''              => esc_html_x( 'Default', 'Background Control', 'elementor' ),
							'center center' => esc_html_x( 'Center Center', 'Background Control', 'elementor' ),
							'center left'   => esc_html_x( 'Center Left', 'Background Control', 'elementor' ),
							'center right'  => esc_html_x( 'Center Right', 'Background Control', 'elementor' ),
							'top center'    => esc_html_x( 'Top Center', 'Background Control', 'elementor' ),
							'top left'      => esc_html_x( 'Top Left', 'Background Control', 'elementor' ),
							'top right'     => esc_html_x( 'Top Right', 'Background Control', 'elementor' ),
							'bottom center' => esc_html_x( 'Bottom Center', 'Background Control', 'elementor' ),
							'bottom left'   => esc_html_x( 'Bottom Left', 'Background Control', 'elementor' ),
							'bottom right'  => esc_html_x( 'Bottom Right', 'Background Control', 'elementor' ),
						],
					),
					'repeat'     => array(
						'label'      => esc_html__( 'Background repeat', 'woodmart' ),
						'responsive' => false,
					),
					'size'       => array(
						'label'      => esc_html__( 'Background size', 'woodmart' ),
						'responsive' => false,
						'options'    => [
							''        => esc_html_x( 'Default', 'Background Control', 'elementor' ),
							'cover'   => esc_html_x( 'Cover', 'Background Control', 'elementor' ),
							'contain' => esc_html_x( 'Contain', 'Background Control', 'elementor' ),
						],
					),
				),
				'include'        => array( 'background', 'image', 'position', 'repeat', 'size' ),
				'types'          => array( 'classic' ),
				'selector'       => '{{WRAPPER}} .wd-info-box:after',
				'condition'      => array(
					'style'                => 'bg-hover',
					'bg_hover_colorpicker' => 'colorpicker',
				),
			)
		);

		$this->add_control(
			'box_bg_hover_color',
			[
				'label'     => esc_html__( 'Hover background color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wd-info-box:after' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'style'                => 'bg-hover',
					'bg_hover_colorpicker' => 'colorpicker',
				],
			]
		);

		$this->add_control(
			'woodmart_hover_color_scheme',
			[
				'label'     => esc_html__( 'Hover color scheme', 'woodmart' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'light' => esc_html__( 'Light', 'woodmart' ),
					'dark'  => esc_html__( 'Dark', 'woodmart' ),
				],
				'default'   => 'light',
				'condition' => [
					'style' => 'bg-hover',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Carousel settings.
		 */
		$this->start_controls_section(
			'carousel_style_section',
			[
				'label' => esc_html__( 'Carousel', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'slides_per_view',
			[
				'label'       => esc_html__( 'Slides per view', 'woodmart' ),
				'description' => esc_html__( 'Set numbers of slides you want to display at the same time on slider\'s container for carousel mode.', 'woodmart' ),
				'type'        => Controls_Manager::SLIDER,
				'default'     => [
					'size' => 3,
				],
				'size_units'  => '',
				'range'       => [
					'px' => [
						'min'  => 1,
						'max'  => 8,
						'step' => 1,
					],
				],
			]
		);

		$this->add_control(
			'slider_spacing',
			[
				'label'   => esc_html__( 'Space between', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					0  => esc_html__( '0 px', 'woodmart' ),
					2  => esc_html__( '2 px', 'woodmart' ),
					6  => esc_html__( '6 px', 'woodmart' ),
					10 => esc_html__( '10 px', 'woodmart' ),
					20 => esc_html__( '20 px', 'woodmart' ),
					30 => esc_html__( '30 px', 'woodmart' ),
				],
				'default' => 30,
			]
		);

		$this->add_control(
			'scroll_per_page',
			[
				'label'        => esc_html__( 'Scroll per page', 'woodmart' ),
				'description'  => esc_html__( 'Scroll per page not per item. This affect next/prev buttons and mouse/touch dragging.', 'woodmart' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Yes', 'woodmart' ),
				'label_off'    => esc_html__( 'No', 'woodmart' ),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'hide_pagination_control',
			[
				'label'        => esc_html__( 'Hide pagination control', 'woodmart' ),
				'description'  => esc_html__( 'If "YES" pagination control will be removed.', 'woodmart' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Yes', 'woodmart' ),
				'label_off'    => esc_html__( 'No', 'woodmart' ),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'hide_prev_next_buttons',
			[
				'label'        => esc_html__( 'Hide prev/next buttons', 'woodmart' ),
				'description'  => esc_html__( 'If "YES" prev/next control will be removed', 'woodmart' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Yes', 'woodmart' ),
				'label_off'    => esc_html__( 'No', 'woodmart' ),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'wrap',
			[
				'label'        => esc_html__( 'Slider loop', 'woodmart' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Yes', 'woodmart' ),
				'label_off'    => esc_html__( 'No', 'woodmart' ),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'        => esc_html__( 'Slider autoplay', 'woodmart' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Yes', 'woodmart' ),
				'label_off'    => esc_html__( 'No', 'woodmart' ),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'speed',
			[
				'label'       => esc_html__( 'Slider speed', 'woodmart' ),
				'description' => esc_html__( 'Duration of animation between slides (in ms)', 'woodmart' ),
				'default'     => '5000',
				'type'        => Controls_Manager::NUMBER,
				'condition'   => [
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'scroll_carousel_init',
			[
				'label'        => esc_html__( 'Init carousel on scroll', 'woodmart' ),
				'description'  => esc_html__( 'This option allows you to init carousel script only when visitor scroll the page to the slider. Useful for performance optimization.', 'woodmart' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Yes', 'woodmart' ),
				'label_off'    => esc_html__( 'No', 'woodmart' ),
				'return_value' => 'yes',
			]
		);

		$this->end_controls_section();

		/**
		 * Subtitle settings.
		 */
		$this->start_controls_section(
			'subtitle_style_section',
			[
				'label' => esc_html__( 'Subtitle', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'subtitle_style',
			[
				'label'   => esc_html__( 'Style', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'default'    => esc_html__( 'Default', 'woodmart' ),
					'background' => esc_html__( 'Background', 'woodmart' ),
				],
				'default' => 'default',
			]
		);

		$this->add_control(
			'subtitle_custom_bg_color',
			[
				'label'     => esc_html__( 'Background color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .info-box-subtitle' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'subtitle_style' => 'background',
				],
			]
		);

		$this->add_control(
			'subtitle_color',
			[
				'label'   => esc_html__( 'Predefined color', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'default' => esc_html__( 'Default', 'woodmart' ),
					'primary' => esc_html__( 'Primary', 'woodmart' ),
					'alt'     => esc_html__( 'Alternative', 'woodmart' ),
				],
				'default' => 'default',
			]
		);

		$this->add_control(
			'subtitle_custom_color',
			[
				'label'     => esc_html__( 'Color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .info-box-subtitle' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'subtitle_typography',
				'label'    => esc_html__( 'Custom typography', 'woodmart' ),
				'selector' => '{{WRAPPER}} .info-box-subtitle',
			]
		);

		$this->end_controls_section();

		/**
		 * Title settings.
		 */
		$this->start_controls_section(
			'title_style_section',
			[
				'label' => esc_html__( 'Title', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_style',
			[
				'label'   => esc_html__( 'Style', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'default'    => esc_html__( 'Default', 'woodmart' ),
					'underlined' => esc_html__( 'Underline', 'woodmart' ),
				],
				'default' => 'default',
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label'   => esc_html__( 'Tag', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'h1'   => esc_html__( 'h1', 'woodmart' ),
					'h2'   => esc_html__( 'h2', 'woodmart' ),
					'h3'   => esc_html__( 'h3', 'woodmart' ),
					'h4'   => esc_html__( 'h4', 'woodmart' ),
					'h5'   => esc_html__( 'h5', 'woodmart' ),
					'h6'   => esc_html__( 'h6', 'woodmart' ),
					'p'    => esc_html__( 'p', 'woodmart' ),
					'div'  => esc_html__( 'div', 'woodmart' ),
					'span' => esc_html__( 'span', 'woodmart' ),
				],
				'default' => 'h4',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .info-box-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Custom typography', 'woodmart' ),
				'selector' => '{{WRAPPER}} .info-box-title',
			]
		);

		$this->end_controls_section();

		/**
		 * Content settings.
		 */
		$this->start_controls_section(
			'content_style_section',
			[
				'label' => esc_html__( 'Content', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'custom_text_color',
			[
				'label'     => esc_html__( 'Color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .info-box-inner' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'content_typography',
				'label'    => esc_html__( 'Custom typography', 'woodmart' ),
				'selector' => '{{WRAPPER}} .info-box-inner',
			]
		);

		$this->end_controls_section();

		/**
		 * Button settings.
		 */
		$this->start_controls_section(
			'button_content_section',
			[
				'label' => esc_html__( 'Button', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'btn_position',
			[
				'label'   => esc_html__( 'Button position', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'hover'  => esc_html__( 'Show on hover', 'woodmart' ),
					'static' => esc_html__( 'Static', 'woodmart' ),
				],
				'default' => 'static',
			]
		);

		$this->add_control(
			'btn_size',
			[
				'label'   => esc_html__( 'Predefined size', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'default'     => esc_html__( 'Default', 'woodmart' ),
					'extra-small' => esc_html__( 'Extra Small', 'woodmart' ),
					'small'       => esc_html__( 'Small', 'woodmart' ),
					'large'       => esc_html__( 'Large', 'woodmart' ),
					'extra-large' => esc_html__( 'Extra Large', 'woodmart' ),
				],
				'default' => 'default',
			]
		);

		$this->add_control(
			'btn_color',
			[
				'label'   => esc_html__( 'Predefined color', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'default' => esc_html__( 'Default', 'woodmart' ),
					'primary' => esc_html__( 'Primary', 'woodmart' ),
					'alt'     => esc_html__( 'Alternative', 'woodmart' ),
					'black'   => esc_html__( 'Black', 'woodmart' ),
					'white'   => esc_html__( 'White', 'woodmart' ),
					'custom'  => esc_html__( 'Custom', 'woodmart' ),
				],
				'default' => 'default',
			]
		);

		$this->start_controls_tabs(
			'button_tabs_style',
			[
				'condition' => [
					'color' => [ 'custom' ],
				],
			]
		);

		$this->start_controls_tab(
			'button_tab_normal',
			[
				'label' => esc_html__( 'Normal', 'woodmart' ),
			]
		);

		$this->add_control(
			'bg_color',
			[
				'label'     => esc_html__( 'Background color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wd-button-wrapper a' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'color_scheme',
			[
				'label'   => esc_html__( 'Text color scheme', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'inherit' => esc_html__( 'Inherit', 'woodmart' ),
					'dark'    => esc_html__( 'Dark', 'woodmart' ),
					'light'   => esc_html__( 'Light', 'woodmart' ),
				],
				'default' => 'inherit',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_tab_hover',
			[
				'label' => esc_html__( 'Hover', 'woodmart' ),
			]
		);

		$this->add_control(
			'bg_color_hover',
			[
				'label'     => esc_html__( 'Background color hover', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wd-button-wrapper:hover a' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'color_scheme_hover',
			[
				'label'   => esc_html__( 'Text color scheme on hover', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'inherit' => esc_html__( 'Inherit', 'woodmart' ),
					'dark'    => esc_html__( 'Dark', 'woodmart' ),
					'light'   => esc_html__( 'Light', 'woodmart' ),
				],
				'default' => 'inherit',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'btn_style',
			[
				'label'   => esc_html__( 'Style', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'default'  => esc_html__( 'Default', 'woodmart' ),
					'bordered' => esc_html__( 'Bordered', 'woodmart' ),
					'link'     => esc_html__( 'Link button', 'woodmart' ),
					'3d'       => esc_html__( '3D', 'woodmart' ),
				],
				'default' => 'default',
			]
		);

		$this->add_control(
			'btn_shape',
			[
				'label'     => esc_html__( 'Shape', 'woodmart' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'rectangle'  => esc_html__( 'Rectangle', 'woodmart' ),
					'round'      => esc_html__( 'Circle', 'woodmart' ),
					'semi-round' => esc_html__( 'Round', 'woodmart' ),
				],
				'condition' => [
					'btn_style!' => [ 'link' ],
				],
				'default'   => 'rectangle',
			]
		);

		$this->add_control(
			'button_icon_heading',
			[
				'label'     => esc_html__( 'Icon', 'woodmart' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		woodmart_get_button_style_icon_map( $this, 'btn_' );

		$this->add_control(
			'button_layout_heading',
			[
				'label'     => esc_html__( 'Layout', 'woodmart' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'full_width',
			[
				'label'        => esc_html__( 'Full width', 'woodmart' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Yes', 'woodmart' ),
				'label_off'    => esc_html__( 'No', 'woodmart' ),
				'return_value' => 'yes',
			]
		);

		$this->end_controls_section();

		/**
		 * Extra settings.
		 */
		$this->start_controls_section(
			'extra_content_section',
			[
				'label' => esc_html__( 'Extra', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'svg_animation',
			[
				'label'        => esc_html__( 'SVG animation', 'woodmart' ),
				'description'  => esc_html__( 'By default, your SVG files will not be animated.', 'woodmart' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Yes', 'woodmart' ),
				'label_off'    => esc_html__( 'No', 'woodmart' ),
				'return_value' => 'yes',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		woodmart_elementor_infobox_carousel_template( $this->get_settings_for_display() );
	}
}

Plugin::instance()->widgets_manager->register( new Infobox_Carousel() );
