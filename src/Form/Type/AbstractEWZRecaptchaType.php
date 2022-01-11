<?php

namespace EWZ\Bundle\RecaptchaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

abstract class AbstractEWZRecaptchaType extends AbstractType
{
    /**
     * The public key.
     *
     * @var string
     */
    protected $publicKey;

    /**
     * Enable recaptcha?
     *
     * @var bool
     */
    protected $enabled;

    /**
     * The API server host name.
     *
     * @var string
     */
    protected $apiHost;

    /**
     * The reCAPTCHA server URL.
     *
     * @var string
     */
    protected $recaptchaApiServer;

    /**
     * @param string $publicKey Recaptcha public key
     * @param bool $enabled   Recaptcha status
     * @param string $apiHost   Api host
     */
    public function __construct(string $publicKey, bool $enabled, string $apiHost = 'www.google.com')
    {
        $this->publicKey = $publicKey;
        $this->enabled = $enabled;
        $this->apiHost = $apiHost;
        $this->recaptchaApiServer = sprintf('https://%s/recaptcha/api.js', $apiHost);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars = array_replace($view->vars, array(
            'ewz_recaptcha_enabled' => $this->enabled,
            'ewz_recaptcha_api_host' => $this->apiHost,
            'ewz_recaptcha_api_uri' => $this->recaptchaApiServer,
            'public_key' => $this->publicKey,
        ));

        if (!$this->enabled) {
            return;
        }

        $this->addCustomVars($view, $form, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'ewz_recaptcha';
    }

    /**
     * Gets the public key.
     *
     * @return string The javascript source URL
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    /**
     * Gets the API host name.
     *
     * @return string The hostname for API
     */
    public function getApiHost(): string
    {
        return $this->apiHost;
    }

    abstract protected function addCustomVars(FormView $view, FormInterface $form, array $options): void;
}
