<?php
namespace Youkok2\Utilities;

use Kunststube\CSRFP\SignatureGenerator;

class CsrfManager
{
    
    private static $signer;

    public static function init() {
        if (self::$signer === null) {
            self::$signer = new SignatureGenerator(CSRF_KEY);
        }
    }
    
    public static function getSignature() {
        self::init();
        
        return self::$signer->getSignature();
    }
    
    public static function validateSignature($token) {
        self::init();
        
        return self::$signer->validateSignature($token);
    }
}
