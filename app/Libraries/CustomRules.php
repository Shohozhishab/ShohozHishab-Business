<?php namespace App\Libraries;

class CustomRules{
    /**
     * @description This method provides numeric validation
     * @param int|float $str
     * @param string|null $error
     * @return bool
     */
    public function only_numeric_not_allow($str = null, ?string &$error = null): bool
    {

        if (is_numeric($str) == 1) {
            $error = lang('Only numeric not allow!');
            return false;
        }
        return true;
    }

    /**
     * @description This method provides user name validation
     * @param string $str
     * @param string|null $error
     * @return bool
     */
    public function validusername(string $str, ?string &$error = null): bool
    {

        $usernamePreg = "/^[a-zA-Z0-9 ]+$/";

        if( (preg_match($usernamePreg,$str))) {
            return true;
        }

        $error = "Please enter valid username";

        return false;

    }

}