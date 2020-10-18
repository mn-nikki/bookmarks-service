<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UrlValidator implements UrlValidatorInterface
{
    private ValidatorInterface $validator;

    /**
     * UrlValidator constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function validateUrl(string $url): ?array
    {
        $errors = null;
        $violations = $this->validator->validate($url, new Url());

        if (!empty($violations)) {
            foreach ($violations as $key => $violation) {
                $errors['messageTemplate'] = $violation->getMessageTemplate();
                $errors['invalidValue'] = $violation->getInvalidValue();
            }
        }

        return $errors ?? null;
    }
}
