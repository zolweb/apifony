<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

interface F7Definition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}