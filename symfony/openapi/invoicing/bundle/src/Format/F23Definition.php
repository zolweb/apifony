<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

interface F23Definition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}