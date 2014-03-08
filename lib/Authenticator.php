<?php
/**
* Two Authenticator library
* @author Stefan Fodor (stefan@unserialized.dk)
* Based on the Google's 2 factor algorithm
*/
class Authenticator {
	
	/**
	* Check if the token is valid
	* @param string $secret
	* @param string $token
	* @param int $deltaTime
	*/
	public static function validateToken( $secret, $token, $deltaTime = 1 ) {
        
		$timeSpan = floor(time() / 30);
		$tokenSize = strlen($token);
		
        for ($i = -$deltaTime; $i <= $deltaTime; $i++) {

			//If token generated in the timespan is equal witht he one given
            if ( self::getToken($secret, $tokenSize, $timeSpan + $i) == $token ) {
				return true; //code is OK
            }
        }

        return false; //no match found
	}
	
	/**
	* Gets a token for a given point in time
	*/
	private static function getToken( $secret, $tokenSize = 6, $timeSpan = null ) {
	
	    if ($timeSpan === null) {
            $timeSpan = floor(time() / 30);
        }

        $secretkey = Base32::base32Decode($secret);

        // Binary voodoo
        $time = chr(0).chr(0).chr(0).chr(0).pack('N*', $timeSpan);
        $hm = hash_hmac('SHA1', $time, $secretkey, true);
        $offset = ord(substr($hm, -1)) & 0x0F;
        $hashpart = substr($hm, $offset, 4);
        $value = unpack('N', $hashpart);
        $value = $value[1];
        $value = $value & 0x7FFFFFFF;

		//and voila
        $modulo = pow(10, $tokenSize);
        return str_pad($value % $modulo, $tokenSize, '0', STR_PAD_LEFT);
	}
	
	/**
     * Create new secret.
     */
    public static function createSecret( $secretLength = 16 ) {
	
        $validChars = Base32::getBase32LookupTable();
        unset($validChars[32]);

        $secret = '';
        for ($i = 0; $i < $secretLength; $i++) {
            $secret .= $validChars[array_rand($validChars)];
        }
		
        return $secret;
    }

}
