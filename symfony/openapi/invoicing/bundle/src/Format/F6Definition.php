<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

interface F6Definition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}