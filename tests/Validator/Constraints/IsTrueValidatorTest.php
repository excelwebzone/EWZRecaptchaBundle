<?php declare(strict_types=1);

namespace EWZ\Tests\Bundle\RecaptchaBundle\Validator\Constraints;

use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrueValidator;
use PHPUnit\Framework\TestCase;
use ReCaptcha\ReCaptcha;
use ReCaptcha\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class IsTrueValidatorTest extends TestCase
{

    public function tesNotEnabled(): void
    {
        $reCaptcha = $this->createMock(ReCaptcha::class);
        $reCaptcha->expects(self::never())
            ->method('verify');
        $requestStack = $this->createMock(RequestStack::class);
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects(self::never())
            ->method('addViolation');
        $context->expects(self::never())
            ->method('buildViolation');

        $authorizationChecker->expects(self::never())
            ->method('isGranted');

        $validator = new IsTrueValidator(false, $reCaptcha, $requestStack, true, $authorizationChecker, []);
        $validator->initialize($context);
        $validator->validate('', new IsTrue());
    }

    public function testTrustedRolesAreNotValidated(): void
    {
        $trustedRoles = ['ROLE_TEST'];
        $reCaptcha = $this->createMock(ReCaptcha::class);
        $reCaptcha->expects(self::never())
            ->method('verify');
        $requestStack = $this->createMock(RequestStack::class);
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects(self::never())
            ->method('addViolation');
        $context->expects(self::never())
            ->method('buildViolation');

        $authorizationChecker->expects(self::once())
            ->method('isGranted')
            ->with($trustedRoles)
            ->willReturn(true);

        if (\is_callable([$requestStack, 'getMainRequest'])) {
            $requestStack->expects(self::never())
                ->method('getMainRequest');
        } else {
            $requestStack->expects(self::never())
                ->method('getMasterRequest');
        }

        $validator = new IsTrueValidator(true, $reCaptcha, $requestStack, true, $authorizationChecker, $trustedRoles);
        $validator->validate('', new IsTrue());
    }

    public function testResponseNotSuccess(): void
    {
        $trustedRoles = ['ROLE_TEST'];
        $clientIp = '127.0.0.1';
        $recaptchaAnswer = 'encoded response';
        $constraint = new IsTrue();
        $reCaptcha = $this->createMock(ReCaptcha::class);
        $requestStack = $this->createMock(RequestStack::class);
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects(self::never())
            ->method('buildViolation');

        $authorizationChecker->expects(self::once())
            ->method('isGranted')
            ->with($trustedRoles)
            ->willReturn(false);

        $request = $this->createMock(Request::class);
        $request->expects(self::once())
            ->method('getClientIp')
            ->willReturn($clientIp);
        $request->expects(self::once())
            ->method('get')
            ->with('g-recaptcha-response')
            ->willReturn($recaptchaAnswer);

        if (\is_callable([$requestStack, 'getMainRequest'])) {
            $requestStack->expects(self::once())
                ->method('getMainRequest')
                ->willReturn($request);
        } else {
            $requestStack->expects(self::once())
                ->method('getMasterRequest')
                ->willReturn($request);
        }

        $response = $this->createMock(Response::class);
        $response->expects(self::once())
            ->method('isSuccess')
            ->willReturn(false);

        $reCaptcha->expects(self::once())
            ->method('verify')
            ->with($recaptchaAnswer, $clientIp)
            ->willReturn($response);

        $context->expects(self::once())
            ->method('addViolation')
            ->with($constraint->message);

        $validator = new IsTrueValidator(true, $reCaptcha, $requestStack, true, $authorizationChecker, $trustedRoles);
        $validator->initialize($context);
        $validator->validate('', $constraint);
    }

    public function testInvalidHostWithVerifyHost(): void
    {
        $trustedRoles = ['ROLE_TEST'];
        $clientIp = '127.0.0.1';
        $recaptchaAnswer = 'encoded response';
        $constraint = new IsTrue();
        $reCaptcha = $this->createMock(ReCaptcha::class);
        $requestStack = $this->createMock(RequestStack::class);
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects(self::never())
            ->method('buildViolation');

        $authorizationChecker->expects(self::once())
            ->method('isGranted')
            ->with($trustedRoles)
            ->willReturn(false);

        $request = $this->createMock(Request::class);
        $request->expects(self::once())
            ->method('getClientIp')
            ->willReturn($clientIp);
        $request->expects(self::once())
            ->method('get')
            ->with('g-recaptcha-response')
            ->willReturn($recaptchaAnswer);
        $request->expects(self::once())
            ->method('getHost')
            ->willReturn('host1');

        if (\is_callable([$requestStack, 'getMainRequest'])) {
            $requestStack->expects(self::once())
                ->method('getMainRequest')
                ->willReturn($request);
        } else {
            $requestStack->expects(self::once())
                ->method('getMasterRequest')
                ->willReturn($request);
        }

        $response = $this->createMock(Response::class);
        $response->expects(self::once())
            ->method('isSuccess')
            ->willReturn(true);
        $response->expects(self::once())
            ->method('getHostname')
            ->willReturn('host2');

        $reCaptcha->expects(self::once())
            ->method('verify')
            ->with($recaptchaAnswer, $clientIp)
            ->willReturn($response);

        $context->expects(self::once())
            ->method('addViolation')
            ->with($constraint->invalidHostMessage);

        $validator = new IsTrueValidator(true, $reCaptcha, $requestStack, true, $authorizationChecker, $trustedRoles);
        $validator->initialize($context);
        $validator->validate('', $constraint);
    }

    public function testInvalidHostWithoutVerifyHost(): void
    {
        $trustedRoles = ['ROLE_TEST'];
        $clientIp = '127.0.0.1';
        $recaptchaAnswer = 'encoded response';
        $constraint = new IsTrue();
        $reCaptcha = $this->createMock(ReCaptcha::class);
        $requestStack = $this->createMock(RequestStack::class);
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects(self::never())
            ->method('buildViolation');

        $authorizationChecker->expects(self::once())
            ->method('isGranted')
            ->with($trustedRoles)
            ->willReturn(false);

        $request = $this->createMock(Request::class);
        $request->expects(self::once())
            ->method('getClientIp')
            ->willReturn($clientIp);
        $request->expects(self::once())
            ->method('get')
            ->with('g-recaptcha-response')
            ->willReturn($recaptchaAnswer);
        $request->expects(self::never())
            ->method('getHost');

        if (\is_callable([$requestStack, 'getMainRequest'])) {
            $requestStack->expects(self::once())
                ->method('getMainRequest')
                ->willReturn($request);
        } else {
            $requestStack->expects(self::once())
                ->method('getMasterRequest')
                ->willReturn($request);
        }

        $response = $this->createMock(Response::class);
        $response->expects(self::once())
            ->method('isSuccess')
            ->willReturn(true);
        $response->expects(self::never())
            ->method('getHostname');

        $reCaptcha->expects(self::once())
            ->method('verify')
            ->with($recaptchaAnswer, $clientIp)
            ->willReturn($response);

        $context->expects(self::never())
            ->method('addViolation');

        $validator = new IsTrueValidator(true, $reCaptcha, $requestStack, false, $authorizationChecker, $trustedRoles);
        $validator->initialize($context);
        $validator->validate('', $constraint);
    }

    public function testValidWithVerifyHost(): void
    {
        $trustedRoles = ['ROLE_TEST'];
        $clientIp = '127.0.0.1';
        $recaptchaAnswer = 'encoded response';
        $host = 'host';
        $constraint = new IsTrue();
        $reCaptcha = $this->createMock(ReCaptcha::class);
        $requestStack = $this->createMock(RequestStack::class);
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects(self::never())
            ->method('buildViolation');
        $context->expects(self::never())
            ->method('addViolation');

        $authorizationChecker->expects(self::once())
            ->method('isGranted')
            ->with($trustedRoles)
            ->willReturn(false);

        $request = $this->createMock(Request::class);
        $request->expects(self::once())
            ->method('getClientIp')
            ->willReturn($clientIp);
        $request->expects(self::once())
            ->method('get')
            ->with('g-recaptcha-response')
            ->willReturn($recaptchaAnswer);
        $request->expects(self::once())
            ->method('getHost')
            ->willReturn($host);

        if (\is_callable([$requestStack, 'getMainRequest'])) {
            $requestStack->expects(self::once())
                ->method('getMainRequest')
                ->willReturn($request);
        } else {
            $requestStack->expects(self::once())
                ->method('getMasterRequest')
                ->willReturn($request);
        }

        $response = $this->createMock(Response::class);
        $response->expects(self::once())
            ->method('isSuccess')
            ->willReturn(true);
        $response->expects(self::once())
            ->method('getHostname')
            ->willReturn($host);

        $reCaptcha->expects(self::once())
            ->method('verify')
            ->with($recaptchaAnswer, $clientIp)
            ->willReturn($response);

        $validator = new IsTrueValidator(true, $reCaptcha, $requestStack, true, $authorizationChecker, $trustedRoles);
        $validator->initialize($context);
        $validator->validate('', $constraint);
    }

}
