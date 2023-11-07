<?php
/**
 * @license MIT
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2021 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace iThemesSecurity\Strauss\Cose\Algorithm\Signature\RSA;

use iThemesSecurity\Strauss\Assert\Assertion;
use iThemesSecurity\Strauss\Cose\Algorithm\Signature\Signature;
use iThemesSecurity\Strauss\Cose\Key\Key;
use iThemesSecurity\Strauss\Cose\Key\RsaKey;
use InvalidArgumentException;

abstract class RSA implements Signature
{
    public function sign(string $data, Key $key): string
    {
        $key = $this->handleKey($key);
        Assertion::true($key->isPrivate(), 'The key is not private');

        if (false === openssl_sign($data, $signature, $key->asPem(), $this->getHashAlgorithm())) {
            throw new InvalidArgumentException('Unable to sign the data');
        }

        return $signature;
    }

    public function verify(string $data, Key $key, string $signature): bool
    {
        $key = $this->handleKey($key);

        return 1 === openssl_verify($data, $signature, $key->asPem(), $this->getHashAlgorithm());
    }

    abstract protected function getHashAlgorithm(): int;

    private function handleKey(Key $key): RsaKey
    {
        return new RsaKey($key->getData());
    }
}
