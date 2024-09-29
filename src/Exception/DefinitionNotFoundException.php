<?php

/**
 * TOBENTO
 *
 * @copyright   Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);
 
namespace Tobento\Service\Picture\Exception;

use Exception;
use Throwable;

/**
 * DefinitionNotFoundException
 */
class DefinitionNotFoundException extends Exception
{
    /**
     * Create a new DefinitionNotFoundException.
     *
     * @param string $definition
     * @param string $message The message
     * @param int $code
     * @param null|Throwable $previous
     */
    public function __construct(
        protected string $definition,
        string $message = '',
        int $code = 0,
        null|Throwable $previous = null
    ) {
        if ($message === '') {            
            $message = sprintf('Picture definition %s not found', $definition);
        }
        
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * Returns the definition name.
     *
     * @return string
     */
    public function definition(): string
    {
        return $this->definition;
    }
}