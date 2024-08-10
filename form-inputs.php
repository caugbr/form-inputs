<?php
/**
 * Plugin name: Form Inputs
 * Description: Create form inputs easely
 * Version: 1.0
 * Author: Cau Guanabara
 * Author URI: mailto:cauguanabara@gmail.com
 * Text Domain: inputs
 * Domain Path: langs
 * License: Wordpress
 */

if (!defined('ABSPATH')) {
    exit;
}


class FormInputs {

    public $line_open = '<div class="formline">';
    public $line_close = '</div>';
    public $before_input = '<div class="input">';
    public $after_input = '</div>';
    public $before_list = '<ul>';
    public $after_list = '</ul>';
    public $before_list_item = '<li>';
    public $after_list_item = '</li>';
    public $url;

    public function __construct() {
        $this->url = site_url() . '/wp-content/plugins/form-inputs/';
        add_action('wp_enqueue_scripts', function() {
            wp_enqueue_script('form-inputs', $this->url . 'form-inputs.js');
            wp_enqueue_style('form-inputs', $this->url . 'form-inputs.css');
        });
        add_action('admin_enqueue_scripts', function() {
            wp_enqueue_script('form-inputs', $this->url . 'form-inputs.js');
            wp_enqueue_style('form-inputs', $this->url . 'form-inputs.css');
        });
    }

    private function extract_clean($arr, $names) {
        if (!in_array("label", $names)) {
            $names[] = "label";
        }
        $ret = [ "values" => [ "label" => "" ], "array" => [] ];
        foreach ($arr as $name => $value) {
            if (in_array($name, $names)) {
                $ret["values"][$name] = $value;
            } else {
                $ret["array"][$name] = $value;
            }
        }
        return $ret;
    }

    public function input_line($type, $props = [], $label = '', $print = true) {
        $html = $this->line_open;
        if (!empty($label)) {
            $html .= "<label for=\"{$props['id']}\">{$label}</label>";
        }
        $html .= $this->before_input;
        $def_re = "/^(password|text|number|time|date|datetime-local|email|tel|search|url|color)$/";
        if (method_exists($this, $type)) {
            $html .= $this->{$type}($props);
        } else if (preg_match($def_re, $type)) {
            $html .= $this->input($type, $props);
        }
        if (!empty($props['description'])) {
            $html .= "<div class=\"description\">{$props['description']}</div>";
        }
        $html .= $this->after_input;
        $html .= $this->line_close;
        if ($print) {
            print $html;
        }
        return $html;
    }

    private function arr2props($arr) {
        $props = [];
        foreach ($arr as $key => $value) {
            $props[] = "{$key}=\"{$value}\"";
        }
        return join(" ", $props);
    }

    private function input($type, $props = []) {
        $p = $this->arr2props($props);
        return "<input type=\"{$type}\" {$p}>";
    }

    private function number($props = []) {
        $p = $this->arr2props($props);
        $html = "<div class=\"input-number\">";
        $html .= "<a href=\"javascript://\" class=\"minus\">-</a>";
        $html .= "<input type=\"number\" {$p}>";
        $html .= "<a href=\"javascript://\" class=\"plus\">+</a>";
        $html .= "</div>";
        return $html;
    }

    private function textarea($props = []) {
        $clean = $this->extract_clean($props, ['value']);
        $value = $clean['values']['value'] ?? '';
        $props = $clean['array'];
        $p = $this->arr2props($props);
        return "<textarea {$p}>{$value}</textarea>";
    }

    private function select($props = []) {
        $clean = $this->extract_clean($props, ['options', 'value']);
        $options = $clean['values']['options'] ?? [];
        $props = $clean['array'];
        $p = $this->arr2props($props);
        $html = "<select {$p}>";
        foreach ($options as $value => $label) {
            $selected = ($value && $value == $clean['values']['value']) ? ' selected' : '';
            $html .= "<option value=\"{$value}\"{$selected}>{$label}</option>";
        }
        $html .= "</select>";
        return $html;
    }

    private function checkbox($props) {
        return $this->check_list($props);
    }

    private function radio($props) {
        return $this->check_list($props, 'radio');
    }

    private function check_list($props = [], $type = 'checkbox') {
        $clean = $this->extract_clean($props, ['options']);
        $options = $clean['values']['options'] ?? [];
        $values = $clean['values']['values'] ?? [];
        $props = $clean['array'];
        $p = $this->arr2props($props);
        $html = $this->before_list;
        foreach ($options as $value => $label) {
            $id = $props['name'] . '_' . $value;
            $checked = in_array($value, $values) ? ' checked' : '';
            $html .= $this->before_list_item;
            $html .= "<label><input type=\"{$type}\" value=\"{$value}\" id=\"{$id}\" {$p} {$checked}> {$label}</label>";
            $html .= $this->after_list_item;
        }
        $html .= $this->after_list;
        return $html;
    }

    private function switch($props) {
        $p = $this->arr2props($props);
        $html = "<label class=\"input-switch\">";
        $html .= "<input type=\"checkbox\" {$p}>";
        $html .= "<span class=\"trail\"><span class=\"handler\"></span></span>";
        $html .= "</label>";
        return $html;
    }
}

global $f_inputs;
$f_inputs = new FormInputs();