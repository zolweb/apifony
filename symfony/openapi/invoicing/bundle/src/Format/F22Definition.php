<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

interface F22Definition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}