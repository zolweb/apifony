<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

interface F21Definition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}