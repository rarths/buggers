<?php

namespace Rarths\Sparkles;

/**
 * A testclass that inherits from CSparkles to access protected methods
 *
 */
class CSparklesSession extends \Rarths\Sparkles\CSparkles
{
    /**
     * Set name in session.
     *
     * @return void
     *
     */
    public function setToSession($data) {
        $this->_setSession($data);
    }

    /**
     * Get name from session.
     *
     * @return string
     *
     */
    public function getFromSession() {
        $data = $this->_getSession();
        
        return $data;
    }

    /**
     * Clean session data.
     *
     * @return string
     *
     */
    public function cleanSession() {
        $this->_clean();
    }
}