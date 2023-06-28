<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

interface F25Definition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}