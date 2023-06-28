<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

interface F12Definition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}