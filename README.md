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
        ...
        'EWZ' => __DIR__.'/../src',
    ));

**Add your private and public key for reCAPTCHA in configuration file:**

If you use secure url for reCAPTCHA put true in secure.

    // app/config/config.yml
    framework:
        ...
        validation:
            enabled: true
            annotations:
                namespaces:
                    recaptcha: EWZ\Bundle\RecaptchaBundle\Validator\Constraints\

    ...

    ewz_recaptcha:
        pubkey:   here_is_your_publick_key
        privkey:  here_is_your_private_key
        secure:   true


Use in forms
------------

In your form class add following lines:

    use EWZ\Bundle\RecaptchaBundle\Form\RecaptchaField;

    ...
    protected function configure()
    {
        ...
        $this->add(new RecaptchaField('recaptcha'));
        ...
    }

To validate the field use:

    /**
     * @recaptcha:Recaptcha
     */
    public $recaptcha;

When you create form you need to pass the container into the method that 
preparing form.

    public function someAction(){
        ...
        $form = new RegisterForm('register');
        // init values
        $form->get('recaptcha')->setScriptURLs(
            $this->container->getParameter('recaptcha.pubkey'),
            $this->container->getParameter('recaptcha.secure')
        );
        ...
    }


Use in view
-----------

In template add following lines:

PHP:

    <?php echo $view['form']->render($form['recaptcha'], array(
        'options' => array(
            'theme' => 'clean',
        ),
    ), array(), 'RecaptchaBundle:Form:recaptcha_field.html.php') ?>

Twig:

    {{ form_field(form.recaptcha, {
        'options': {
            'theme': 'clean',
        },
    }, [], 'EWZRecaptchaBundle:Form:recaptcha_field.html.twig') }}


Or using JavaScript:

PHP:

    <div id="recaptcha-container"></div>
    <script type="text/javascript">
        window.onload = function () {
            $.getScript("<?php echo $form['recaptcha']::RECAPTCHA_API_JS_SERVER ?>", function() {
                Recaptcha.create("<?php echo $form['recaptcha']->getPublicKey() ?>", "recaptcha-container", {
                    theme: "clean",
                });
            });
        };
    </script>

Twig:

    <div id="recaptcha-container"></div>
    <script type="text/javascript">
        NUI.onPageLoad(function () {
            $.getScript("{{ constant('\\EWZ\\Bundle\\RecaptchaBundle\\Form\\RecaptchaField::RECAPTCHA_API_JS_SERVER') }}", function() {
                Recaptcha.create("{{ form.recaptcha.getPublicKey() }}", "recaptcha-container", {
                    theme: "clean"
                });
            });
        });
    </script>
