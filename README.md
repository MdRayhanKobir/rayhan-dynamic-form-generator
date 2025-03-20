# Dynamic Form Generator for Laravel
The **Dynamic Form Generator** is a Laravel package designed to simplify form creation by generating forms dynamically from a configuration array. It supports multiple input types, Bootstrap grid layouts, and integrates seamlessly with Laravel's features like CSRF protection and validation.
## Installation
Run the following command in your terminal to install the package:
```bash
composer require rayhan-kobir/dynamic-form-generator
```
# Verify Service Provider Registration
The package uses Laravel's auto-discovery to register its service provider. If auto-discovery is disabled in your project, manually add the provider to the providers array in config/app.php:
```bash
'providers' => [
    // Other providers...
    Rayhan\DynamicFormGenerator\DynamicFormGeneratorServiceProvider::class,
],
```
# How to use in blade file
```bash
 @rayhanDynamicForm([
                        'action' => '/submit-form',
                        'method' => 'POST',
                        'fields' => [
                            ['type' => 'text', 'name' => 'full_name', 'label' => 'Full Name', 'class' => 'form--control', 'col' => 'col-md-6'],
                            ['type' => 'email', 'name' => 'email', 'label' => 'Email Address', 'class' => 'form--control', 'col' => 'col-md-6'],
                            ['type' => 'password', 'name' => 'password', 'label' => 'Password', 'class' => 'form--control', 'col' => 'col-md-6'],
                            ['type' => 'number', 'name' => 'age', 'label' => 'Your Age', 'class' => 'form--control', 'col' => 'col-md-6'],
                            ['type' => 'date', 'name' => 'dob', 'label' => 'Date of Birth', 'class' => 'form--control', 'col' => 'col-md-6'],
                            ['type' => 'range', 'name' => 'experience', 'label' => 'Experience Level', 'min' => 1, 'max' => 10, 'class' => 'form--control', 'col' => 'col-md-6'],
                            ['type' => 'hidden', 'name' => 'token', 'value' => 'xyz123'],
                            ['type' => 'textarea', 'name' => 'address', 'label' => 'Your Address', 'class' => 'form--control', 'col' => 'col-md-12'],
                            [
                                'type' => 'select',
                                'name' => 'country',
                                'label' => 'Select Country',
                                'class' => 'form--control',
                                'col' => 'col-md-6',
                                'options' => [
                                    'bd' => 'Bangladesh',
                                    'us' => 'United States',
                                    'uk' => 'United Kingdom',
                                    'ca' => 'Canada',
                                ],
                            ],
                            [
                                'type' => 'radio',
                                'name' => 'gender',
                                'label' => 'Gender',
                                'class' => 'form-check-input',
                                'col' => 'col-md-6',
                                'options' => [
                                    'male' => 'Male',
                                    'female' => 'Female',
                                    'other' => 'Other',
                                ],
                            ],
                            [
                                'type' => 'checkbox',
                                'name' => 'hobbies[]',
                                'label' => 'Select Your Hobbies',
                                'class' => 'form-check-input',
                                'col' => 'col-md-6',
                                'options' => [
                                    'reading' => 'Reading',
                                    'sports' => 'Sports',
                                    'music' => 'Music',
                                ],
                            ],
                            ['type' => 'file', 'name' => 'profile_picture', 'label' => 'Upload Profile Picture', 'class' => 'form--control', 'col' => 'col-md-6'],
                        ],
                        'buttons' => [['type' => 'submit', 'label' => 'Submit', 'class' => 'btn btn--base'], ['type' => 'reset', 'label' => 'Reset', 'class' => 'btn btn--danger']],
                    ])

```
