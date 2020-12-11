EWZRecaptchaBundle
==================

[![Build Status](https://api.travis-ci.org/excelwebzone/EWZRecaptchaBundle.svg)](https://travis-ci.org/excelwebzone/EWZRecaptchaBundle)

This bundle provides easy reCAPTCHA form field for Symfony.

## Installation

### Step 1: Use composer and enable Bundle

To install EWZRecaptchaBundle with Composer just type in your terminal:

```bash
php composer.phar require excelwebzone/recaptcha-bundle
```

Now, Composer will automatically download all required files, and install them
for you. All that is left to do is to update your ``AppKernel.php`` file, and
register the new bundle:

```php
<?php

// in AppKernel::registerBundles()
$bundles = array(
    // ...
    new EWZ\Bundle\RecaptchaBundle\EWZRecaptchaBundle(),
    // ...
);
```

### Step2: Configure the bundle's

> **NOTE**: The configuration options differ between Version 2 and Version 3 of the reCAPTCHA system. Some of the previous options have no effect on Version 3.

Add the following to your config file:

> **NOTE**: If you're using symfony 4, the config will be in `config/packages/ewz_recaptcha.yaml`. The local dev enviroment has its own config in `config/packages/dev/ewz_recaptcha.yaml`.

#### Main configuration for both v2 and v3

The version setting determines which configuration options are available. Set the version corresponding to your Google reCAPTCHA settings (valid values: 2 or 3):

``` yaml
# app/config/config.yml

ewz_recaptcha:
    // ...
    version: 2
```

You can easily disable reCAPTCHA (for example in a local or test environment):

``` yaml
# app/config/config.yml

ewz_recaptcha:
    // ...
    enabled: false
```

Enter the public and private keys here:

``` yaml
# app/config/config.yml

ewz_recaptcha:
    // ...
    public_key:  here_is_your_public_key
    private_key: here_is_your_private_key

```

`www.google.com` is blocked in Mainland China, you can override the default server like this (See https://developers.google.com/recaptcha/docs/faq#can-i-use-recaptcha-globally for further information):

``` yaml
# app/config/config.yml

ewz_recaptcha:
    // ...
    api_host: recaptcha.net
```

#### v2 only Configuration

Sets the default locale:

``` yaml
# app/config/config.yml

ewz_recaptcha:
    // ...
    # Not needed as "%kernel.default_locale%" is the default value for the locale key
    locale_key:  %kernel.default_locale%
```

**NOTE**: This Bundle lets the client browser choose the secure https or unsecure http API.

If you want to use the language default for the reCAPTCHA the same as the
request locale you must activate the resolver (deactivated by default):

``` yaml
# app/config/config.yml

ewz_recaptcha:
    // ...
    locale_from_request: true
```

You can load the reCAPTCHA using Ajax:

``` yaml
# app/config/config.yml

ewz_recaptcha:
    // ...
    ajax: true
```
You can add HTTP Proxy configuration:

``` yaml
# app/config/config.yml

ewz_recaptcha:
    // ...
    http_proxy:
        host: proxy.mycompany.com
        port: 3128
        auth: proxy_username:proxy_password
```
In case you have turned off the domain name checking on reCAPTCHA's end, you'll need to check the origin of the response by enabling the ``verify_host`` option:

``` yaml
# app/config/config.yml

ewz_recaptcha:
    // ...
    verify_host: true
```

**NOTE**: If you're using symfony 5 and want to configure the bundle with PHP files instead of YAML, the configuration is like this:

``` php
// config/packages/ewz_recaptcha.php

<?php declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void
{
    $configurator->extension('ewz_recaptcha', [
        'public_key' => 'here_is_your_public_key',
        'private_key' => 'here_is_your_private_key',
        'locale_key' => '%kernel.default_locale%'
    ]);
};
```

#### v3 only Configuration

For the v3 reCAPTCHA an information badge is shown. If you inform your users about using the reCAPTCHA on another way, you can hide it with the following option (see https://developers.google.com/recaptcha/docs/faq#hiding-badge for further information):

``` yaml
# app/config/config.yml

ewz_recaptcha:
    // ...
    hide_badge: true
```

To modify the default threshold score of 0.5 set this option (see https://developers.google.com/recaptcha/docs/v3#interpreting_the_score for further information):

``` yaml
# app/config/config.yml

ewz_recaptcha:
    // ...
    score_threshold: 0.6
```

Congratulations! You're ready!

## Basic Usage

> **NOTE**: The basic usage differs between Version 2 and Version 3 of the reCAPTCHA system.

### v2 Usage

When creating a new form class add the following line to create the field:

``` php
<?php

use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;

public function buildForm(FormBuilder $builder, array $options)
{
    // ...
    $builder->add('recaptcha', EWZRecaptchaType::class);
    // ...
}
```

> *Note that in Symfony versions lower than 2.8 refers to form types by name instead of class name, use:*
>
> ``` php
> <?php
>
> public function buildForm(FormBuilder $builder, array $options)
> {
>    // ...
>    $builder->add('recaptcha', 'ewz_recaptcha');
>    // ...
> }
> ```

You can pass extra options to reCAPTCHA with the "attr > options" option:

``` php
<?php

use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;

public function buildForm(FormBuilder $builder, array $options)
{
    // ...
    $builder->add('recaptcha', EWZRecaptchaType::class, array(
        'attr' => array(
            'options' => array(
                'theme' => 'light',
                'type'  => 'image',
                'size'  => 'normal',
                'defer' => true,
                'async' => true,
            )
        )
    ));
    // ...
}
```

Support Google's Invisible is super easy:

``` php
<?php

public function buildForm(FormBuilder $builder, array $options)
{
    // ...
    $builder->add('recaptcha', EWZRecaptchaType::class, array(
        'attr' => array(
            'options' => array(
                'theme' => 'light',
                'type'  => 'image',
                'size' => 'invisible',              // set size to invisible
                'defer' => true,
                'async' => true,
                'callback' => 'onReCaptchaSuccess', // callback will be set by default if not defined (along with JS function that validate the form on success)
                'bind' => 'btn_submit',             // this is the id of the form submit button
                // ...
             )
        )
    ));
    // ...
}
```

> Note: If you use the pre-defined callback, you would need to add `recaptcha-form` class to your `<form>` tag.

If you need to configure the language of the captcha depending on your site
language (multisite languages) you can pass the language with the "language"
option:

``` php
<?php

public function buildForm(FormBuilder $builder, array $options)
{
    // ...
    $builder->add('recaptcha', EWZRecaptchaType::class, array(
        'language' => 'en'
        // ...
    ));
    // ...
}
```

To validate the field use:

``` php
<?php

use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;

/**
 * @Recaptcha\IsTrue
 */
public $recaptcha;
```

Another method would consist to pass the validation constraints as an options of your FormType. This way, your data class contains only meaningful properties.
If we take the example from above, the buildForm method would look like this.
Please note that if you set ```mapped=>false``` then the annotation will not work. You have to also set ```constraints```:

``` php
<?php

use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;

public function buildForm(FormBuilder $builder, array $options)
{
    // ...
    $builder->add('recaptcha', EWZRecaptchaType::class, array(
        'attr'        => array(
            'options' => array(
                'theme' => 'light',
                'type'  => 'image',
                'size'  => 'normal'
            )
        ),
        'mapped'      => false,
        'constraints' => array(
            new RecaptchaTrue()
        )
    ));
    // ...
```

The form template resource is now auto registered via an extension of the container.
However, you can always implement your own custom form widget.

**PHP**:

``` php
<?php $view['form']->setTheme($form, array('EWZRecaptchaBundle:Form')) ?>

<?php echo $view['form']->widget($form['recaptcha'], array(
    'attr' => array(
        'options' => array(
            'theme' => 'light',
            'type'  => 'image',
            'size'  => 'normal'
        ),
    ),
)) ?>
```

**Twig**:

``` jinja
{% form_theme form '@EWZRecaptcha/Form/ewz_recaptcha_widget.html.twig' %}

{{ form_widget(form.recaptcha, { 'attr': {
    'options' : {
        'theme': 'light',
        'type': 'image',
        'size': 'normal'
    },
} }) }}
```

If you are not using a form, you can still implement the reCAPTCHA field
using JavaScript:

**PHP**:

``` php
<div id="recaptcha-container"></div>
<script type="text/javascript">
    $(document).ready(function() {
        $.getScript("<?php echo \EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType::RECAPTCHA_API_JS_SERVER ?>", function() {
            Recaptcha.create("<?php echo $form['recaptcha']->get('public_key') ?>", "recaptcha-container", {
                theme: "clean",
            });
        });
    };
</script>
```

**Twig**:

``` jinja
<div id="recaptcha-container"></div>
<script type="text/javascript">
    $(document).ready(function() {
        $.getScript("{{ constant('\\EWZ\\Bundle\\RecaptchaBundle\\Form\\Type\\EWZRecaptchaType::RECAPTCHA_API_JS_SERVER') }}", function() {
            Recaptcha.create("{{ form.recaptcha.get('public_key') }}", "recaptcha-container", {
                theme: "clean"
            });
        });
    });
</script>
```

## Customization

If you want to use a custom theme, put your chunk of code before setting the theme:

``` jinja
 <div id="recaptcha_widget">
   <div id="recaptcha_image"></div>
   <div class="recaptcha_only_if_incorrect_sol" style="color:red">Incorrect please try again</div>

   <span class="recaptcha_only_if_image">Enter the words above:</span>
   <span class="recaptcha_only_if_audio">Enter the numbers you hear:</span>

   <input type="text" id="recaptcha_response_field" name="recaptcha_response_field" />

   <div><a href="javascript:Recaptcha.reload()">Get another CAPTCHA</a></div>
   <div class="recaptcha_only_if_image"><a href="javascript:Recaptcha.switch_type('audio')">Get an audio CAPTCHA</a></div>
   <div class="recaptcha_only_if_audio"><a href="javascript:Recaptcha.switch_type('image')">Get an image CAPTCHA</a></div>

   <div><a href="javascript:Recaptcha.showhelp()">Help</a></div>
 </div>

{% form_theme form '@EWZRecaptcha/Form/ewz_recaptcha_widget.html.twig' %}

{{ form_widget(form.recaptcha, { 'attr': {
    'options' : {
        'theme' : 'custom',
    },
} }) }}
```

### v3 Usage

When creating a new form class add the following line to create the field:

``` php
<?php

use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaV3Type;

public function buildForm(FormBuilder $builder, array $options)
{
    // ...
    $builder->add('recaptcha', EWZRecaptchaV3Type::class);
    // ...
}
```

You can pass the action to reCAPTCHA with the "action_name" option (see https://developers.google.com/recaptcha/docs/v3#actions for further information)::

``` php
<?php

use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;

public function buildForm(FormBuilder $builder, array $options)
{
    // ...
    $builder->add('recaptcha', EWZRecaptchaType::class, array(
        'action_name' => 'contact'
    ));
    // ...
}
```

To validate the field use:

``` php
<?php

use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;

/**
 * @Recaptcha\IsTrueV3
 */
public $recaptcha;
```

Another method would consist to pass the validation constraints as an options of your FormType. This way, your data class contains only meaningful properties.
If we take the example from above, the buildForm method would look like this. You have to also set ```constraints```:

``` php
<?php

use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaV3Type;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrueV3;

public function buildForm(FormBuilder $builder, array $options)
{
    // ...
    $builder->add('recaptcha', EWZRecaptchaV3Type::class, array(
        'action_name' => 'contact',
        'constraints' => array(
            new IsTrueV3()
        )
    ));
    // ...
```

## Advanced Usage

It is possible to register reCAPTCHA form services. To accomplish this, enter the service definition as follows (in this example we did it in PHP):

``` php
<?php

$ewzRecaptchaConfiguration = array();
$ewzRecaptchaConfiguration['enabled'] = isset($_ENV['RECAPTCHA_PUBLIC'], $_ENV['RECAPTCHA_PRIVATE']);
$ewzRecaptchaConfiguration['public_key'] = $_ENV['RECAPTCHA_PUBLIC'] ?? null;
$ewzRecaptchaConfiguration['private_key'] = $_ENV['RECAPTCHA_PRIVATE'] ?? null;
$ewzRecaptchaConfiguration['api_host'] = 'recaptcha.net';
$ewzRecaptchaConfiguration['version'] = 3;

$ewzRecaptchaConfiguration['service_definition'] = array();
$ewzRecaptchaConfiguration['service_definition'][] = [
    'service_name' => 'ContactRecaptchaService',
    'options' => [
        'action_name' => 'form'
    ]
];

// Add more form services here

// ...
$container->loadFromExtension('ewz_recaptcha', $ewzRecaptchaConfiguration);
// ...
```

Now the services are now accessible with ```ewz_recaptcha.[service_name]```. They can be registered to your form type class:

``` php
<?php

namespace MyNamespace\DependencyInjection;

use MyNamespace\Form\ContactType;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Extension\Extension;

class ContactFormExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $container->register(ContactType::class)
            ->addArgument(new Reference('ewz_recaptcha.ContactRecaptchaService'))
            ->addTag('form.type');
    }
}
// ...
```

The form type class itself uses the injected service this way:

``` php
<?php

namespace MyNamespace\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ContactType extends AbstractType
{
    /** @var FormBuilderInterface */
    private $recaptcha;

    public function __construct(?FormBuilderInterface $recaptcha)
    {
        $this->recaptcha = $recaptcha;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // ...
        if(null !== $this->recaptcha) {
            $builder->add($this->recaptcha);
        }
        // ...
    }

```
