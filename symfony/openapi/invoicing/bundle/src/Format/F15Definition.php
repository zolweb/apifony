<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

interface F15Definition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}