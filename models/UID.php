<?php
//An instance of UID is used to generate unique 10-digit alphanumeric
//character strings that will give objects their database identity.

class UID {
	//$check: callback function (string) or method (array) used to
	//        verify availability of each UID.
	private $check;

	public function __construct( $check ) {
		$this->check = $check;
	}

	//generates UIDs until $this->check($uid) returns false (i.e. $uid is available)
	public function GetUniqueID () {
		
		//String containing all characters that may be used in unique ID
		$chars = str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');

		//First character must be alphabetical
		$uid = substr( $chars, mt_rand(0,51), 1 );

		//Shuffle numbers into $chars
		$chars = str_shuffle($chars . '0123456789');

		//Add 9 random alphanumeric characters to $uid
		for ($i = 0; $i < 9; $i++) {
			$x = mt_rand(0,61);
			$uid .= substr( $chars, $x, 1 );
		}

		//if this UniqueID is already in use..
		if ( call_user_func( $this->check, $uid ) ) {

			//generate a new one.
			return $this->GetUniqueID();
		}

		return $uid;
	}
}

?>