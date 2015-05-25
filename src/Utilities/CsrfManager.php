<?php
/*
 * File: CsrfManager.php
 * Holds: Wrapper for CSRF stuff
 * Created: 26.05.2015
 * Project: Youkok2
 */

namespace Youkok2\Utilities;

/*
 * Define what classes to use
 */

use \Kunststube\CSRFP\SignatureGenerator as SignatureGenerator;

/*
 * The CsrfManager class
 */

class CsrfManager {
    
    /*
     * Holds the instance
     */
    
    private static $signer;
    
    /*
     * Init method
     */

    public static function init() {
        // New instance
        self::$signer = new SignatureGenerator(CSRF_KEY);
        //self::$signer->setValidityWindow(time() + 3600);
    }
    
    /*
     * Return signature
     */
    
    public static function getSignature() {
        // Checks if we have to init first
        if (self::$signer === null) {
            self::init();
        }
        
        // Return signature
        return self::$signer->getSignature();
    }
    
    /*
     * Validates signature
     */
    
    public static function validateSignature($token) {
        // Checks if we have to init first
        if (self::$signer === null) {
            self::init();
        }
        
        // Return signature
        return self::$signer->validateSignature($token);
    }
}