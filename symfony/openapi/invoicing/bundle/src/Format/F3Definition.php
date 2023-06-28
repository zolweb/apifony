<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

interface F3Definition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}