<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

interface F16Definition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}