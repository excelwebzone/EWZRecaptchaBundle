<?php

namespace EWZ\Tests\Bundle\RecaptchaBundle\DependencyInjection;

use EWZ\Bundle\RecaptchaBundle\DependencyInjection\EWZRecaptchaExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Yaml\Parser;

/**
 * @author Andrej Hudec <pulzarraider@gmail.com>
 */
class EWZRecaptchaExtensionTest extends TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $configuration;

    protected function tearDown()
    {
        $this->configuration = null;
    }

    public function testSimpleConfiguration()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new EWZRecaptchaExtension();
        $config = $this->getSimpleConfig();
        $loader->load([$config], $this->configuration);

        $this->assertParameter(true, 'ewz_recaptcha.enabled');
        $this->assertParameter('foo_public_key', 'ewz_recaptcha.public_key');
        $this->assertParameter('bar_private_key', 'ewz_recaptcha.private_key');
        $this->assertParameter(false, 'ewz_recaptcha.verify_host');
        $this->assertParameter(false, 'ewz_recaptcha.ajax');
        $this->assertParameter('%kernel.default_locale%', 'ewz_recaptcha.locale_key');
        $this->assertParameter('www.google.com', 'ewz_recaptcha.api_host');
        $this->assertParameter(false, 'ewz_recaptcha.locale_from_request');
        $this->assertParameter(null, 'ewz_recaptcha.timeout');
        $this->assertParameter([], 'ewz_recaptcha.trusted_roles');
        $this->assertParameter(
            ['host' => null, 'port' => null, 'auth' => null],
            'ewz_recaptcha.http_proxy'
        );

        $this->assertHasDefinition('ewz_recaptcha.locale.resolver');
        $this->assertHasDefinition('ewz_recaptcha.form.type');
        $this->assertHasDefinition('ewz_recaptcha.validator.true');
        $this->assertHasDefinition('ewz_recaptcha.recaptcha');
        $this->assertHasDefinition('ewz_recaptcha.extension.recaptcha.request_method.post');
        $this->assertHasDefinition('ewz_recaptcha.extension.recaptcha.request_method.proxy_post');

        $this->assertDefinitionHasReferenceArgument(
            'ewz_recaptcha.recaptcha',
            1,
            'ewz_recaptcha.extension.recaptcha.request_method.post'
        );
    }

    public function testFullConfiguration()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new EWZRecaptchaExtension();
        $config = $this->getFullConfig();
        $loader->load([$config], $this->configuration);

        $this->assertParameter(true, 'ewz_recaptcha.enabled');
        $this->assertParameter('foo_public_key', 'ewz_recaptcha.public_key');
        $this->assertParameter('bar_private_key', 'ewz_recaptcha.private_key');
        $this->assertParameter(true, 'ewz_recaptcha.verify_host');
        $this->assertParameter(true, 'ewz_recaptcha.ajax');
        $this->assertParameter('sk', 'ewz_recaptcha.locale_key');
        $this->assertParameter('www.example.com', 'ewz_recaptcha.api_host');
        $this->assertParameter(true, 'ewz_recaptcha.locale_from_request');
        $this->assertParameter(10, 'ewz_recaptcha.timeout');
        $this->assertParameter(['role_foo'], 'ewz_recaptcha.trusted_roles');
        $this->assertParameter(
            ['host' => 'http://foo.example.com', 'port' => 80, 'auth' => 'bar:baz'],
            'ewz_recaptcha.http_proxy'
        );

        $this->assertHasDefinition('ewz_recaptcha.locale.resolver');
        $this->assertHasDefinition('ewz_recaptcha.form.type');
        $this->assertHasDefinition('ewz_recaptcha.validator.true');
        $this->assertHasDefinition('ewz_recaptcha.recaptcha');
        $this->assertHasDefinition('ewz_recaptcha.extension.recaptcha.request_method.post');
        $this->assertHasDefinition('ewz_recaptcha.extension.recaptcha.request_method.proxy_post');

        $this->assertDefinitionHasReferenceArgument(
            'ewz_recaptcha.recaptcha',
            1,
            'ewz_recaptcha.extension.recaptcha.request_method.proxy_post'
        );
    }

    private function getSimpleConfig()
    {
        $yaml = <<<EOF
public_key: 'foo_public_key'
private_key: 'bar_private_key'
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    private function getFullConfig()
    {
        $yaml = <<<EOF
public_key: 'foo_public_key'
private_key: 'bar_private_key'
enabled: true
verify_host: true
ajax: true
locale_key: 'sk'
api_host: 'www.example.com'
locale_from_request: true
timeout: 10
trusted_roles:
    - 'role_foo'
http_proxy:
    host: 'http://foo.example.com'
    port: 80
    auth: 'bar:baz'
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    private function assertParameter($value, $key)
    {
        $this->assertSame($value, $this->configuration->getParameter($key), sprintf('%s parameter is correct', $key));
    }

    private function assertHasDefinition($id)
    {
        $this->assertTrue(($this->configuration->hasDefinition($id) ?: $this->configuration->hasAlias($id)));
    }

    private function assertDefinitionHasReferenceArgument($id, $index, $expectedArgumentValue)
    {
        $definition = $this->configuration->getDefinition($id);
        $argumentValue = $definition->getArgument($index);

        $this->assertInstanceOf(Reference::class, $argumentValue);
        $this->assertSame($expectedArgumentValue, (string)$argumentValue);
    }
}
