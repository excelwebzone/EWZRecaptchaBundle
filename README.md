EWZRecaptchaBundle
==================

This bundle provides easy reCAPTCHA form field for Symfony2.

## Installation

Installation depends on how your project is setup:

### Step 1: Installation using the `bin/vendors.php` method

If you're using the `bin/vendors.php` method to manage your vendor libraries,
add the following entry to the `deps` in the root of your project file:

```
[EWZTimeBundle]
    git=http://github.com/excelwebzone/EWZRecaptchaBundle.git
    target=/bundles/EWZ/Bundle/EWZRecaptchaBundle
```

Next, update your vendors by running:

``` bash
$ ./bin/vendors
```

Great! Now skip down to *Step 2*.

### Step 1 (alternative): Installation with submodules

If you're managing your vendor libraries with submodules, first create the
`vendor/bundles/EWZ/Bundle` directory:

``` bash
$ mkdir -pv vendor/bundles/EWZ/Bundle
```

Next, add the necessary submodule:

``` bash
$ git submodule add git://github.com/excelwebzone/EWZRecaptchaBundle.git vendor/bundles/EWZ/Bundle/EWZRecaptchaBundle
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
    secure:       false
```

**NOTE**: If you use secure url for reCAPTCHA put true in secure (false is the default value).

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
