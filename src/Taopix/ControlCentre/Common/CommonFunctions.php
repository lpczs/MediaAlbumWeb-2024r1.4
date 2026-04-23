<?php

namespace Taopix\ControlCentre\Common;

class CommonFunctions
{
    public static function getCurlPEMFilePath(string $folder): string
    {
        return implode(DIRECTORY_SEPARATOR, [
            $folder,
            'libs',
            'internal',
            'curl',
            'curl-ca-bundle.pem'
        ]);
    }

    /**
     * `Like` search
     *
     * @param string $str
     * @param string $searchTerm
     * @return boolean
     */
    public static function like(string $str, string $searchTerm): bool {
        $searchTerm = strtolower($searchTerm);
        $str = strtolower($str);
        $pos = strpos($str, $searchTerm);
        if ($pos === false)
            return false;
        else
            return true;
    }

    /**
     * Create random code 
     *
     * @param integer $length
     * @param boolean $uppercase
     * @return string
     */
    public static function createRandomCode(int $length, $uppercase = false): string
    {
        $result = '';
        $salt = ($uppercase) ? 'ABCDEFGHIJKLMNPQRSTUVWXYZ123456789' : 'abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789';
        $saltLength = strlen($salt) - 1;

        for($i = 0; $i < $length; $i++) {
            $result .= $salt[mt_rand(0, $saltLength)];
        }

        return $result;
    }

    /**
     * Merge two arrays together recursively. 
     *
     * @param array $array1
     * @param array $array2
     * @return Array merged array
     */
    public static function array_merge_recursive_ex(array $array1, array $array2): Array
    {
        $merged = $array1;

        foreach ($array2 as $key => & $value) {
            if (gettype($value)==='object') {
                $value = (array) $value;
            }
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = self::array_merge_recursive_ex($merged[$key], $value);
            } else if (is_numeric($key)) {
                if (!in_array($value, $merged)) {
                    $merged[] = $value;
                }
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

    /**
     * Get differences between two arrays
     *
     * @param Array $array1
     * @param Array $array2
     * @return Array differences only
     */
	public static function recursiveDiff(Array $array1, Array $array2): Array
    {
		$result = array();
		foreach($array1 as $key => $val) {
			if(is_array($val) && isset($array2[$key])) {
				$tmp = self::recursiveDiff($val, $array2[$key]);
				if($tmp) {
					$result[$key] = $tmp;
				}
			}
			elseif(!isset($array2[$key])) {
				$result[$key] = null;
			}
			elseif($val !== $array2[$key]) {
				$result[$key] = $array2[$key];
			}

			if(isset($array2[$key])) {
				unset($array2[$key]);
			}
		}
		return array_merge($result, $array2);
	}
}
