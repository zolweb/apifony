<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

interface F19Definition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}