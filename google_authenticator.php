<?php
/**
* Google Authenticator library
* @author Stefan Fodor (stefan@unserialized.dk)
* Based on the algorithm some PHP snippets I've seen somewhere
*/
class GoogleAuthenticator {
	
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
		
			//var_dump(self::getToken($secret, $tokenSize, $timeSpan + $i));
			
			//If token generated in the timespan is equal witht he one given
            if ( self::getToken($secret, $tokenSize, $timeSpan + $i) == $token ) {
				//everything is OK
				return true;
            }
        }

        return false;
	}
	
	/**
	* Gets a token for a given point in time
	*/
	private static function getToken( $secret, $tokenSize = 6, $timeSpan = null ) {
	
	    if ($timeSpan === null) {
            $timeSpan = floor(time() / 30);
        }

        $secretkey = self::base32Decode($secret);

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
	
        $validChars = self::getBase32LookupTable();
        unset($validChars[32]);

        $secret = '';
        for ($i = 0; $i < $secretLength; $i++) {
            $secret .= $validChars[array_rand($validChars)];
        }
		
        return $secret;
    }
	
	/**
     * Decode base 32
     */
    protected static function base32Decode( $secret ) {
	
        if (empty($secret)) 
			return '';

        $base32chars = self::getBase32LookupTable();
        $base32charsFlipped = array_flip($base32chars);

        $paddingCharCount = substr_count($secret, $base32chars[32]);
        $allowedValues = array(6, 4, 3, 1, 0);
        if (!in_array($paddingCharCount, $allowedValues)) return false;
        for ($i = 0; $i < 4; $i++){
            if ($paddingCharCount == $allowedValues[$i] &&
                substr($secret, -($allowedValues[$i])) != str_repeat($base32chars[32], $allowedValues[$i])) return false;
        }
        $secret = str_replace('=','', $secret);
        $secret = str_split($secret);
        $binaryString = "";
		
        for ($i = 0; $i < count($secret); $i = $i+8) {
            $x = "";
            if (!in_array($secret[$i], $base32chars)) return false;
            for ($j = 0; $j < 8; $j++) {
                $x .= str_pad(base_convert(@$base32charsFlipped[@$secret[$i + $j]], 10, 2), 5, '0', STR_PAD_LEFT);
            }
            $eightBits = str_split($x, 8);
            for ($z = 0; $z < count($eightBits); $z++) {
                $binaryString .= ( ($y = chr(base_convert($eightBits[$z], 2, 10))) || ord($y) == 48 ) ? $y:"";
            }
        }
        return $binaryString;
    }

    /**
     * Encode to base 32
     */
    protected static function base32Encode($secret, $padding = true) {
	
        if (empty($secret)) 
			return '';

        $base32chars = self::getBase32LookupTable();

        $secret = str_split($secret);
        $binaryString = "";
        for ($i = 0; $i < count($secret); $i++) {
            $binaryString .= str_pad(base_convert(ord($secret[$i]), 10, 2), 8, '0', STR_PAD_LEFT);
        }
        $fiveBitBinaryArray = str_split($binaryString, 5);
        $base32 = "";
        $i = 0;
        while ($i < count($fiveBitBinaryArray)) {
            $base32 .= $base32chars[base_convert(str_pad($fiveBitBinaryArray[$i], 5, '0'), 2, 10)];
            $i++;
        }
        if ($padding && ($x = strlen($binaryString) % 40) != 0) {
            if ($x == 8) $base32 .= str_repeat($base32chars[32], 6);
            elseif ($x == 16) $base32 .= str_repeat($base32chars[32], 4);
            elseif ($x == 24) $base32 .= str_repeat($base32chars[32], 3);
            elseif ($x == 32) $base32 .= $base32chars[32];
        }
        return $base32;
    }

    /**
     * Static list of the 32 chars
     */
    protected static function getBase32LookupTable() {
        return array(
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', //  7
            'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', // 15
            'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', // 23
            'Y', 'Z', '2', '3', '4', '5', '6', '7', // 31
            '='  // padding char
        );
    }
	
}
