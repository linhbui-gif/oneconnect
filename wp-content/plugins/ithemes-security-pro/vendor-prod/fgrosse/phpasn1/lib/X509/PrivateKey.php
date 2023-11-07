<?php
/*
 * This file is part of the PHPASN1 library.
 *
 * Copyright © Friedrich Große <friedrich.grosse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace iThemesSecurity\Strauss\FG\X509;

use iThemesSecurity\Strauss\FG\ASN1\OID;
use iThemesSecurity\Strauss\FG\ASN1\Universal\NullObject;
use iThemesSecurity\Strauss\FG\ASN1\Universal\Sequence;
use iThemesSecurity\Strauss\FG\ASN1\Universal\BitString;
use iThemesSecurity\Strauss\FG\ASN1\Universal\ObjectIdentifier;

class PrivateKey extends Sequence
{
    /**
     * @param string $hexKey
     * @param \iThemesSecurity\Strauss\FG\ASN1\ASNObject|string $algorithmIdentifierString
     */
    public function __construct($hexKey, $algorithmIdentifierString = OID::RSA_ENCRYPTION)
    {
        parent::__construct(
            new Sequence(
                new ObjectIdentifier($algorithmIdentifierString),
                new NullObject()
            ),
            new BitString($hexKey)
        );
    }
}
