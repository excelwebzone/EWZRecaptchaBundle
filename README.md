Provides use reCAPTCHA as form field.

Installation
============

**Add RecaptchaBundle to your src/Bundle dir**

You can download it from here http://excelwebzone.github.com/RecaptchaBundle

**Add RecaptchaBundle to your application kernel:**

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new EWZ\RecaptchaBundle\EWZRecaptchaBundle(),
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
                    recaptcha: EWZ\RecaptchaBundle\Validator\Constraints\

    ...

    ewz_recaptcha:
        pubkey:   here_is_your_publick_key
        privkey:  here_is_your_private_key
        secure:   true
    

Use in forms
------------

In your form class add following lines

    use use EWZ\RecaptchaBundle\Form\RecaptchaField;

When you create form (if you create it in separated class not in the controller) 
you need pass container into the method that preparing form.

Let's see how it works.

In the controller we have some action. In this action we try to create the form. 

    public function someAction(){
        ...
        $form = new RegisterForm('register');
        // init values
        $form->get('recaptcha')->setScriptURLs($this->container);
        ...
    }

In the Register form class

    protected function configure()
    {
        ...
        $this->add(new RecaptchaField('recaptcha'));
        ...
    }

Use in view
-----------

In template add following lines

    <?php echo $view['form']->render($form['recaptcha'], array(
        'options' => array(
            'theme' => 'clean',
        ),
    ), array(), 'RecaptchaBundle:Form:recaptcha_field.html.php') ?>

Or using JavaScript

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
