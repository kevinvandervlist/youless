<?php

class Hash {

    /**
		 * Return a password hash that's salted.
		 */
    public function generatePasswordHash($private_salt, $password) {
        return sha1(PUBLIC_SALT . $private_salt . $password);
    }
}

?>