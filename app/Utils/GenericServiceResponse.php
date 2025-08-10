<?php
/* -----------------------------------------------------------------------------------
 * Copyright (c) 2024 Princeps Credit Systems Limited
 * -----------------------------------------------------------------------------------
 * This code is the property of Princeps Credit Systems Limited. Unauthorized copying,
 * sharing, or use of this code, via any medium, is strictly prohibited
 * without express permission from Princeps Credit Systems Limited.
 * -----------------------------------------------------------------------------------
 * @package    [USER SERVICE/USER]
 * @author     [COLLINS BENSON]
 * @license    Proprietary
 * @version    1.0.0
 * @link       https://www.princepscreditsystemslimited.com
 */

namespace App\Utils;

use App\Traits\DTOTrait;

class GenericServiceResponse
{
    use DTOTrait;

    /**
     * @param bool $status
     * @param string|null $message
     * @param mixed $data
     */
    public function __construct(
        public bool $status = false,
        public ?string $message = 'Sorry!, something went wrong.',
        public mixed $data = null
    ) { }

}
