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

Add the following to your config file:

**NOTE**: If you're using symfony 4, the config will be in `config/packages/ewz_recaptcha.yaml`. The local dev enviroment has its own config in `config/packages/dev/ewz_recaptcha.yaml`.

``` yaml
# app/config/config.yml

ewz_recaptcha:
    public_key:  here_is_your_public_key
    private_key: here_is_your_private_key
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

You can easily disable reCAPTCHA (for example in a local or test environment):

``` yaml
# app/config/config.yml

ewz_recaptcha:
    // ...
    enabled: false
```

Or even load reCAPTCHA using Ajax:

``` yaml
# app/config/config.yml

ewz_recaptcha:
    // ...
    ajax: true
```

`www.google.com` is blocked in Mainland China, you can override the default server like this:

``` yaml
# app/config/config.yml

ewz_recaptcha:
    // ...
    api_host: recaptcha.net
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

Congratulations! You're ready!

## Basic Usage

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
