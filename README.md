EWZRecaptchaBundle
==================

This bundle provides easy reCAPTCHA form field for Symfony.

## Installation

### Step 1: Using Composer (recommended)

To install EWZRecaptchaBundle with Composer just add the following to your
`composer.json` file:

```js
// composer.json
{
    // ...
    "require": {
        // ...
        "excelwebzone/recaptcha-bundle": "dev-master"
    }
}
```

**NOTE**: Please replace `dev-master` in the snippet above with the latest stable
branch, for example ``2.0.*``.

Then, you can install the new dependencies by running Composer's ``update``
command from the directory where your ``composer.json`` file is located:

```bash
$ php composer.phar update
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

### Step 1 (alternative): Using ``deps`` file (Symfony 2.0.x)

First, checkout a copy of the code. Just add the following to the ``deps``
file of your Symfony Standard Distribution:

```ini
[EWZRecaptchaBundle]
    git=http://github.com/excelwebzone/EWZRecaptchaBundle.git
    target=/bundles/EWZ/Bundle/RecaptchaBundle
```

**NOTE**: You can add `version` tag in the snippet above with the latest stable
branch, for example ``version=origin/2.0``.

Then register the bundle with your kernel:

```php
<?php

// in AppKernel::registerBundles()
$bundles = array(
    // ...
    new EWZ\Bundle\RecaptchaBundle\EWZRecaptchaBundle(),
    // ...
);
```

Make sure that you also register the namespace with the autoloader:

```php
<?php

// app/autoload.php
$loader->registerNamespaces(array(
    // ...
    'EWZ'              => __DIR__.'/../vendor/bundles',
    // ...
));
```

Now use the ``vendors`` script to clone the newly added repositories
into your project:

```bash
$ php bin/vendors install
```

### Step 1 (alternative): Using submodules (Symfony 2.0.x)

If you're managing your vendor libraries with submodules, first create the
`vendor/bundles/EWZ/Bundle` directory:

``` bash
$ mkdir -pv vendor/bundles/EWZ/Bundle
```

Next, add the necessary submodule:

``` bash
$ git submodule add git://github.com/excelwebzone/EWZRecaptchaBundle.git vendor/bundles/EWZ/Bundle/RecaptchaBundle
```

### Step2: Configure the autoloader

Add the following entry to your autoloader:

``` php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...

    'EWZ' => __DIR__.'/../vendor/bundles',
));
```

### Step3: Enable the bundle

Finally, enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...

        new EWZ\Bundle\RecaptchaBundle\EWZRecaptchaBundle(),
    );
}
```

### Step4: Configure the bundle's

Finally, add the following to your config file:

``` yaml
# app/config/config.yml

ewz_recaptcha:
    public_key:   here_is_your_public_key
    private_key:  here_is_your_private_key
    locale_key:   kernel.default_locale
```

**NOTE**: This Bundle lets the client browser choose the secure https or unsecure http API.

You can easily disable reCAPTCHA (for example in a local or test environment):

``` yaml
# app/config/config.yml

ewz_recaptcha:
    // ...
    enabled: false
```

Congratulations! You're ready!

## Basic Usage

When creating a new form class add the following line to create the field:

``` php
<?php

public function buildForm(FormBuilder $builder, array $options)
{
    // ...
    $builder->add('recaptcha', 'ewz_recaptcha');
    // ...
}
```

You can pass extra options to reCAPTCHA with the "attr > options" option:

``` php
<?php

public function buildForm(FormBuilder $builder, array $options)
{
    // ...
    $builder->add('recaptcha', 'ewz_recaptcha', array(
        'attr' => array(
            'options' => array(
                'theme' => 'clean'
            )
        )
    ));
    // ...
}
```

To validate the field use:

``` php
<?php

use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;

/**
 * @Recaptcha\True
 */
public $recaptcha;
```

Another method would consist to pass the validation constraints as an options of your FormType. This way, your data class contains only meaningful properties.
If we take the example from above, the buildForm method would look like this. 
Please note that if you set ```mapped=>false``` then the annotation will not work. You have to also set ```constraints```:

``` php
<?php

use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\True;

public function buildForm(FormBuilder $builder, array $options)
{
    // ...
    $builder->add('recaptcha', 'ewz_recaptcha', array(
        'attr'          => array(
            'options' => array(
                'theme' => 'clean'
            )
        ),
        'mapped' => false,
        'constraints'   => array(
            new True()
        )
    ));
    // ...
```


Cool, now you are ready to implement the form widget:

**PHP**:

``` php
<?php $view['form']->setTheme($form, array('EWZRecaptchaBundle:Form')) ?>

<?php echo $view['form']->widget($form['recaptcha'], array(
    'attr' => array(
        'options' => array(
            'theme' => 'clean',
        ),
    ),
)) ?>
```

**Twig**:

``` jinja
{% form_theme form 'EWZRecaptchaBundle:Form:ewz_recaptcha_widget.html.twig' %}

{{ form_widget(form.recaptcha, { 'attr': {
    'options' : {
        'theme' : 'clean',
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
        $.getScript("<?php echo \EWZ\Bundle\RecaptchaBundle\Form\Type\RecaptchaType::RECAPTCHA_API_JS_SERVER ?>", function() {
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
        $.getScript("{{ constant('\\EWZ\\Bundle\\RecaptchaBundle\\Form\\Type\\RecaptchaType::RECAPTCHA_API_JS_SERVER') }}", function() {
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

{% form_theme form 'EWZRecaptchaBundle:Form:ewz_recaptcha_widget.html.twig' %}

{{ form_widget(form.recaptcha, { 'attr': {
    'options' : {
        'theme' : 'custom',
    },
} }) }}
```

**Further reading**: [Customizing the Look and Feel of reCAPTCHA](https://developers.google.com/recaptcha/docs/customization)
