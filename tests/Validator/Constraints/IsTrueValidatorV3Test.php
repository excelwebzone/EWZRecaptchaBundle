<?php declare(strict_types=1);

namespace Validator\Constraints;

use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrueV3;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrueValidatorV3;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReCaptcha\ReCaptcha;
use stdClass;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class IsTrueValidatorV3Test extends TestCase
{

    public function testNotEnabledDoesNotValidate(): void
    {
        $reCaptcha = $this->createMock(ReCaptcha::class);
        $requestStack = $this->createMock(RequestStack::class);
        $logger = $this->createMock(LoggerInterface::class);
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects(self::never())
            ->method('addViolation');
        $context->expects(self::never())
            ->method('buildViolation');

        $validator = new IsTrueValidatorV3(false, 0.1, $reCaptcha, $requestStack, $logger);
        $validator->initialize($context);
        $validator->validate('', $this->createMock(Constraint::class));
    }

    public function testRequiresV3(): void
    {
        $reCaptcha = $this->createMock(ReCaptcha::class);
        $requestStack = $this->createMock(RequestStack::class);
        $logger = $this->createMock(LoggerInterface::class);
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects(self::never())
            ->method('addViolation');
        $context->expects(self::never())
            ->method('buildViolation');

        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrueV3",');

        $validator = new IsTrueValidatorV3(true, 0.1, $reCaptcha, $requestStack, $logger);
        $validator->initialize($context);
        $validator->validate('', $this->createMock(IsTrue::class));
    }

    public function testRequiresValueNotNullButNotString(): void
    {
        $reCaptcha = $this->createMock(ReCaptcha::class);
        $requestStack = $this->createMock(RequestStack::class);
        $logger = $this->createMock(LoggerInterface::class);
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects(self::never())
            ->method('addViolation');
        $context->expects(self::never())
            ->method('buildViolation');

        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "string", "stdClass" given');

        $validator = new IsTrueValidatorV3(true, 0.1, $reCaptcha, $requestStack, $logger);
        $validator->initialize($context);
        $validator->validate(new stdClass(), new IsTrueV3());
    }

}
