<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

interface F13Definition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}