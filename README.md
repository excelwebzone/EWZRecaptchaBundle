Provides use reCAPTCHA as form field.

Installation
============

**Add RecaptchaBundle to your application kernel:**

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new EWZ\Bundle\RecaptchaBundle\EWZRecaptchaBundle(),
            // ...
        );
    }

**Add the EWZ namespace to your autoloader:**

    // app/autoload.php
    $loader->registerNamespaces(array(
        // ...
        'EWZ' => __DIR__.'/../src',
        // ...
    ));

**Add your private and public key for reCAPTCHA in configuration file:**

    // app/config/config.yml
    // ...
    ewz_recaptcha:
        public_key:   here_is_your_publick_key
        private_key:  here_is_your_private_key
        secure:       false

Note: If you use secure url for reCAPTCHA put true in secure (false is the default value).


Disable reCAPTCHA
-----------------

You can easily disable reCAPTCHA (for example in a local or test environment):

    // app/config/config.yml
    // ...
    ewz_recaptcha:
        // ...
        enabled: false


Use in forms
------------

**Add the following lines to your form class:**

    public function buildForm(FormBuilder $builder, array $options)
    {
        // ...
        $builder->add('recaptcha', 'ewz_recaptcha');
        // ...
    }

You can pass extra options to reCAPTCHA with the attr > options option:

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


**To validate the field use:**

    use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;

    /**
     * @Recaptcha\True
     */
    public $recaptcha;


Use in view
-----------

**Display field using form widget:**

PHP (Note: this is still under development):

    <?php echo $view['form']->widget($form['recaptcha'], array(
        'attr' => array(
            'options' => array(
                'theme' => 'clean',
            ),
        ),
    ), array(
        'theme' => 'EWZRecaptchaBundle:Form:recaptcha_widget.html.php',
    )) ?>

Twig:

    {% form_theme form 'EWZRecaptchaBundle:Form:recaptcha_widget.html.twig' %}

    {{ form_widget(form.recaptcha, { 'attr': {
        'options' : {
            'theme' : 'clean',
        },
    } }) }}


**Or using JavaScript:**

PHP:

    <div id="recaptcha-container"></div>
    <script type="text/javascript">
        $(document).ready(function() {
            $.getScript("<?php echo \EWZ\Bundle\RecaptchaBundle\Form\Extension\Core\Type\RecaptchaType::RECAPTCHA_API_JS_SERVER ?>", function() {
                Recaptcha.create("<?php echo $form['recaptcha']->get('public_key') ?>", "recaptcha-container", {
                    theme: "clean",
                });
            });
        };
    </script>

Twig:

    <div id="recaptcha-container"></div>
    <script type="text/javascript">
        $(document).ready(function() {
            $.getScript("{{ constant('\\EWZ\\Bundle\\RecaptchaBundle\\Form\\Extension\\Core\\Type\\RecaptchaType::RECAPTCHA_API_JS_SERVER') }}", function() {
                Recaptcha.create("{{ form.recaptcha.get('public_key') }}", "recaptcha-container", {
                    theme: "clean"
                });
            });
        });
    </script>
