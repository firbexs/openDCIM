<?php

/*
    Copyright (c) 2012, Open Source Solutions Limited, Dublin, Ireland
    All rights reserved.

    Contact: Barry O'Donovan - barry (at) opensolutions (dot) ie
             http://www.opensolutions.ie/

    This file is part of the OSS_SNMP package.

    Redistribution and use in source and binary forms, with or without
    modification, are permitted provided that the following conditions are met:

        * Redistributions of source code must retain the above copyright
          notice, this list of conditions and the following disclaimer.
        * Redistributions in binary form must reproduce the above copyright
          notice, this list of conditions and the following disclaimer in the
          documentation and/or other materials provided with the distribution.
        * Neither the name of Open Source Solutions Limited nor the
          names of its contributors may be used to endorse or promote products
          derived from this software without specific prior written permission.

    THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
    ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
    WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
    DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY
    DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
    (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
    LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
    ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
    (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
    SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

namespace OSS_SNMP\Cache;

/**
 * basic (array) cache implementation
 *
 * @copyright Copyright (c) 2012, Open Source Solutions Limited, Dublin, Ireland
 * @author Barry O'Donovan <barry@opensolutions.ie>
 */
class Basic extends \OSS_SNMP\Cache
{

    /**
     * An array to store results - a temporary cache
     * @var array An array to store results - a temporary cache
     */
    protected $_cache;


    /**
     * Cache constructor.
     *
     * For basic cache, takes no parameters.
     *
     * @return \OSS_SNMP\Cache\Basic An instance of the cache ($this) for  fluent interfaces
     */
    public function __construct()
    {
        $_cache = array();

        return $this;
    }



    /**
     * Load a named value from the cache (or null if not present)
     *
     * @param string $var The name of the value to load
     * @return mixed|null The value from the cache or null
     */
    public function load( $var )
    {
        if( isset( $this->_cache[ $var ] ) )
            return $this->_cache[ $var ];

        return null;
    }


    /**
     * Save a named value to the cache
     *
     * @param string $var The name of the value to save
     * @param mixed  $val The value to save
     * @return mixed The value (as passed)
     */
    public function save( $var, $val )
    {
        return $this->_cache[ $var ] = $val;
    }

    /**
     * Clear a named value from the cache
     *
     * @param string $var The name of the value to clear
     */
    public function clear( $var )
    {
        if( isset( $this->_cache[ $var ] ) )
            unset( $this->_cache[ $var ] );
    }


    /**
     * Clear all values from the cache
     *
     */
    public function clearAll()
    {
        $this->_cache = array();
    }

}

