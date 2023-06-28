<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

interface F5Definition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}