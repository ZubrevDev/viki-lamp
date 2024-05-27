<?php
// Определите пространство имен для вашего виджета
namespace vikiCustom\Elementor;


// Добавьте необходимые use-инструкции
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin;

// Создайте класс для вашего кастомного виджета
class CustomProductSliderWidget extends Widget_Base {
    public function get_name() {
        return 'custom-product-slider';
    }

    public function get_title() {
        return 'Custom Product Slider';
    }

    public function get_icon() {
        return 'fa fa-shopping-cart'; // Выберите подходящую иконку
    }

    public function get_categories() {
        return ['basic']; // Выберите подходящую категорию
    }

    protected function _register_controls() {
        // Определите контролы для вашего виджета, включая поле ввода ID товаров.
        // Также добавьте контролы для выбора категорий, если необходимо.
        // Пример:
        $this->start_controls_section(
            'section_content',
            [
                'label' => 'Content',
            ]
        );

        $this->add_control(
            'product_ids',
            [
                'label' => 'Product IDs',
                'type' => Controls_Manager::TEXT,
                'description' => 'Enter the product IDs separated by commas.',
            ]
        );

        // Добавьте другие контролы по необходимости.

        $this->end_controls_section();
    }

    protected function render() {
        // Получите и отобразите кастомные товары и категории на основе введенных данных.
    }

    protected function _content_template() {
        // Определите контент-шаблон для редактора Elementor, если необходимо.
    }
}

// Зарегистрируйте ваш кастомный виджет

function register_new_widgets( $widgets_manager ) {

	require_once( __DIR__ . '/widgets/test-wiget.php' );

	$widgets_manager->register( new \CustomProductSliderWidget() );

}
add_action( 'elementor/widgets/register', 'register_new_widgets' );