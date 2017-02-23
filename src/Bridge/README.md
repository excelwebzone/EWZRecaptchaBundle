Silex Recaptcha Service Provider
================================

The silex recaptcha service provider is a bridge to use the different form type in Silex when the form component is loaded.

## Installation

### Step 1: Install the bundle

Follow the step 1 (except the part on kernel bundle activation) of the [symfony bundle installation.](../README.md)

### Step 2: Enable the service provider

Add the recaptcha service provider to your bootstrap

```php
<?php

$app->register(new \EWZ\Bundle\RecaptchaBundle\Bridge\RecaptchaServiceProvider(), array(
    'ewz_recaptcha.public_key' => here_is_your_public_key,
    'ewz_recaptcha.private_key' => here_is_your_private_key
));
```

**NOTE**: The service should be registered after the form and validator service because it add the recaptcha form type and validator constraint.
In case you have services overriding this parameters, you can extends :

    * 'twig.form.templates' to add the ewz_recaptcha_widget.html.twig in Resources/views/Form
    * 'validator.validator_service_ids' to add the validator constraint services ewz_recaptcha.true

### Step 3: Use it with your form builder

```php
<?php

use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;

$form = $app['form.factory']->createBuilder('form')
    ->add('captcha', 'ewz_recaptcha', array(
        'constraints' => new RecaptchaTrue()
    ))
```
