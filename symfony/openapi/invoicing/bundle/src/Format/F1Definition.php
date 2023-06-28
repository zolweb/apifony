<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

interface F1Definition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}