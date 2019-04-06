<?php

namespace Community\Model\Token;

class ActivationCode extends AbstractToken
{
    const ALPHABET = '23456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnopqrstuvwxyz';
    const LENGTH = 6;
}
