<?php

namespace Rayhan\DynamicFormGenerator;

class DynamicFormGenerator
{
    protected $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function render()
    {
        $method = $this->config['method'] ?? 'POST';
        $rowClass = $this->config['row_class'] ?? 'gy-3';

        $html = '<form method="' . $method . '" action="' . $this->config['action'] . '" enctype="multipart/form-data">';
        $html .= csrf_field();

        $html .= '<div class="row ' . $rowClass . '">';

        foreach ($this->config['fields'] as $field) {
            $html .= $this->generateField($field);
        }

        $html .= '</div>'; 

        if (isset($this->config['buttons'])) {
            $html .= '<div class="row gy-md-4 gy-3">';
            $html .= '<div class="col-md-12">'; //button
            $html .= '<div class="form-group">';
            foreach ($this->config['buttons'] as $button) {
                $html .= $this->generateButton($button);
            }
            $html .= '</div></div></div>';
        }

        $html .= '</form>';

        return $html;
    }

    protected function generateField($field)
    {
        $type = $field['type'] ?? 'text';
        $name = $field['name'];
        $label = $field['label'] ?? ucfirst($name);
        $id = $field['id'] ?? $name;
        $fieldClass = $field['class'] ?? 'form--control';
        $labelClass = $field['label_class'] ?? 'form--label';
        $colClass = $field['col'] ?? 'col-md-6';

        //col
        $html = '<div class="' . $colClass . '">';
        $html .= '<div class="form-group">';

        // label
        if (!in_array($type, ['hidden', 'submit', 'button'])) {
            $html .= '<label class="' . $labelClass . '" for="' . $id . '">' . $label . '</label>';
        }

        // filed
        switch ($type) {
            case 'text':
            case 'email':
            case 'password':
            case 'number':
            case 'date':
            case 'range':
            case 'hidden':
                $value = $field['value'] ?? old($name);
                $min = isset($field['min']) ? 'min="' . $field['min'] . '"' : '';
                $max = isset($field['max']) ? 'max="' . $field['max'] . '"' : '';
                $html .= '<input type="' . $type . '" name="' . $name . '" id="' . $id . '" class="' . $fieldClass . '" value="' . $value . '" ' . $min . ' ' . $max . '>';
                break;

            case 'textarea':
                $html .= '<textarea name="' . $name . '" id="' . $id . '" class="' . $fieldClass . '">' . old($name) . '</textarea>';
                break;

            case 'select':
                $html .= '<select name="' . $name . '" id="' . $id . '" class="' . $fieldClass . '">';
                foreach ($field['options'] as $value => $text) {
                    $selected = (old($name) == $value) ? 'selected' : '';
                    $html .= '<option value="' . $value . '" ' . $selected . '>' . $text . '</option>';
                }
                $html .= '</select>';
                break;

            case 'radio':
            case 'checkbox':
                foreach ($field['options'] as $value => $text) {
                    $checked = (old($name) == $value) ? 'checked' : '';
                    $inputName = ($type === 'checkbox' && str_ends_with($name, '[]')) ? $name : $name;
                    $html .= '<div class="' . $type . '-group">';
                    $html .= '<input type="' . $type . '" name="' . $inputName . '" id="' . $id . '_' . $value . '" class="' . $fieldClass . '" value="' . $value . '" ' . $checked . '>';
                    $html .= '<label for="' . $id . '_' . $value . '">' . $text . '</label>';
                    $html .= '</div>';
                }
                break;

            case 'file':
                $html .= '<input type="file" name="' . $name . '" id="' . $id . '" class="' . $fieldClass . '">';
                break;
        }

        // validation error
        if ($errors = session('errors')) {
            if ($errors->has($name)) {
                $html .= '<span style="color: red;">' . $errors->first($name) . '</span>';
            }
        }

        // close view
        $html .= '</div></div>';
        return $html;
    }

    protected function generateButton($button)
    {
        $type = $button['type'] ?? 'submit';
        $name = $button['name'] ?? $type;
        $label = $button['label'] ?? ucfirst($type);
        $id = $button['id'] ?? $name;
        $class = $button['class'] ?? 'btn btn-primary';

        return '<button type="' . $type . '" name="' . $name . '" id="' . $id . '" class="' . $class . '">' . $label . '</button>';
    }

}