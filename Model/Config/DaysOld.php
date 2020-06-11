<?php
/**
 * @author Gustavo Ulyssea - gustavo.ulyssea@gmail.com
 * @copyright Copyright (c) 2020 GumNet (https://gum.net.br)
 * @package GumNet AME
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY GUM Net (https://gum.net.br). AND CONTRIBUTORS
 * ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED
 * TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL THE FOUNDATION OR CONTRIBUTORS
 * BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace GumNet\AutomaticCancelPendingOrders\Model\Config;

class DaysOld implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => '1'],
            ['value' => 2, 'label' => '2'],
            ['value' => 3, 'label' => '3'],
            ['value' => 4, 'label' => '4'],
            ['value' => 5, 'label' => '5'],
            ['value' => 6, 'label' => '6'],
            ['value' => 7, 'label' => '7'],
            ['value' => 8, 'label' => '8'],
            ['value' => 9, 'label' => '8'],
            ['value' => 10, 'label' => '10'],
            ['value' => 11, 'label' => '11'],
            ['value' => 12, 'label' => '12'],
            ['value' => 13, 'label' => '13'],
            ['value' => 14, 'label' => '14'],
            ['value' => 15, 'label' => '15'],
            ['value' => 16, 'label' => '16'],
            ['value' => 17, 'label' => '17'],
            ['value' => 18, 'label' => '18'],
            ['value' => 19, 'label' => '19'],
            ['value' => 20, 'label' => '20'],
            ['value' => 21, 'label' => '21'],
            ['value' => 22, 'label' => '22'],
            ['value' => 23, 'label' => '23'],
            ['value' => 24, 'label' => '24'],
            ['value' => 25, 'label' => '25'],
            ['value' => 26, 'label' => '26'],
            ['value' => 27, 'label' => '27'],
            ['value' => 28, 'label' => '28'],
            ['value' => 29, 'label' => '29'],
            ['value' => 30, 'label' => '30'],
        ];
    }
}
