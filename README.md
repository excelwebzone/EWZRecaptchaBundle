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

    ...
    public function buildForm(FormBuilder $builder, array $options)
    {
        ...
        $builder->add('recaptcha', 'recaptcha');
        ...
    }
    ...

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

    <?php echo $view['form']->widget($form['recaptcha'], array(
        'attr' => array(
            'options' => array(
                'theme' => 'clean',
            ),
        ),
        'theme' => 'EWZRecaptchaBundle:Form:recaptcha_widget.html.php',
    )) ?>

Twig:

    {{ form_widget(form.recaptcha, { 'attr': {
        'options' : {
            'theme' : 'clean',
        },
    } }, { 'theme' : 'EWZRecaptchaBundle:Form:recaptcha_widget.html.twig' }) }}


Or using JavaScript:

PHP:

    <div id="recaptcha-container"></div>
    <script type="text/javascript">
        $(document).ready(function() {
            $.getScript("<?php echo \EWZ\Bundle\RecaptchaBundle\Form\Extension\Core\Type\RecaptchaType::RECAPTCHA_API_JS_SERVER ?>", function() {
                Recaptcha.create("<?php echo $form['recaptcha']->get('pubkey') ?>", "recaptcha-container", {
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
                Recaptcha.create("{{ form.recaptcha.get('pubkey') }}", "recaptcha-container", {
                    theme: "clean"
                });
            });
        });
    </script>
