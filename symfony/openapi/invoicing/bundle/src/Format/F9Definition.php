<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

interface F9Definition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}