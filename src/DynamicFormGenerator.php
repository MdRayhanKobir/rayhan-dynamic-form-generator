<?php

namespace Rayhan\DynamicFormGenerator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

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

        $action = $this->getActionUrl($this->config['action']);
        $html = '<form method="' . $method . '" action="' . $action . '" enctype="multipart/form-data">';
        $html .= csrf_field();
        //hidden input
        $html .= '<input type="hidden" name="form_config" value="' . htmlspecialchars(json_encode($this->config)) . '">';
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
        $required = $field['required'] ?? false;

        //col
        $html = '<div class="' . $colClass . '">';
        $html .= '<div class="form-group">';

        // label
        if (!in_array($type, ['hidden', 'submit', 'button'])) {
            $html .= '<label class="' . $labelClass . '" for="' . $id . '">' . $label;
            if ($required) {
                $html .= ' <span class="text-danger">*</span>'; 
            }
            $html .= '</label>';
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
                $html .= '<span color="text-danger">' . $errors->first($name) . '</span>';
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

    private function getActionUrl($action)
    {
        if (Route::has($action)) {
            return route($action); 
        }
        return $action;
    }

    // input validation
    public function validate($request)
    {
        $rules = [];

        foreach ($this->config['fields'] as $field) {
            $name = str_replace('[]', '', $field['name']);
            $type = $field['type'] ?? 'text';
            $required = $field['required'] ?? false;

            $fieldRules = [];

            // check required
            if ($required) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable'; 
            }

            // rules set
            switch ($type) {
                case 'text':
                case 'textarea':
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:255';
                    break;
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'password':
                    if ($required) {
                        $fieldRules[] = 'min:8';
                    }
                    break;
                case 'number':
                case 'range':
                    $fieldRules[] = 'numeric';
                    if (isset($field['min'])) {
                        $fieldRules[] = 'min:' . $field['min'];
                    }
                    if (isset($field['max'])) {
                        $fieldRules[] = 'max:' . $field['max'];
                    }
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                case 'select':
                case 'radio':
                    if (isset($field['options'])) {
                        $validOptions = implode(',', array_keys($field['options']));
                        $fieldRules[] = 'in:' . $validOptions;
                    }
                    break;
                case 'checkbox':
                    if (isset($field['options'])) {
                        $validOptions = implode(',', array_keys($field['options']));
                        $fieldRules[] = 'array';
                        $fieldRules[] = 'in:' . $validOptions;
                    }
                    break;
                case 'file':
                    $fieldRules[] = 'file';
                    $fieldRules[] = 'mimes:jpeg,png,jpg,gif';
                    $fieldRules[] = 'max:2048'; 
                    break;
            }

            $rules[$name] = implode('|', $fieldRules);
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        return true; 
    }

}