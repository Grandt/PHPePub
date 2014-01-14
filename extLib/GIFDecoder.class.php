<?php
/*
:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
::
::	GIFDecoder Version 2.0 by L�szl� Zsidi
::
::	Created at 2007. 02. 01. '07.47.AM'
::
::	Updated at 2009. 06. 23. '06.00.AM'
::
:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
*/

Class GIFDecoder {
	var $GIF_TransparentR =  -1;
	var $GIF_TransparentG =  -1;
	var $GIF_TransparentB =  -1;
	var $GIF_TransparentI =   0;

	var $GIF_buffer = Array ( );
	var $GIF_arrays = Array ( );
	var $GIF_delays = Array ( );
	var $GIF_dispos = Array ( );
	var $GIF_stream = "";
	var $GIF_string = "";
	var $GIF_bfseek =  0;
	var $GIF_anloop =  0;

	var $GIF_screen = Array ( );
	var $GIF_global = Array ( );
	var $GIF_sorted;
	var $GIF_colorS;
	var $GIF_colorC;
	var $GIF_colorF;
	/*
	:::::::::::::::::::::::::::::::::::::::::::::::::::
	::
	::	GIFDecoder ( $GIF_pointer )
	::
	*/
	function GIFDecoder ( $GIF_pointer ) {
		$this->GIF_stream = $GIF_pointer;

		GIFDecoder::GIFGetByte ( 6 );
		GIFDecoder::GIFGetByte ( 7 );

		$this->GIF_screen = $this->GIF_buffer;
		$this->GIF_colorF = $this->GIF_buffer [ 4 ] & 0x80 ? 1 : 0;
		$this->GIF_sorted = $this->GIF_buffer [ 4 ] & 0x08 ? 1 : 0;
		$this->GIF_colorC = $this->GIF_buffer [ 4 ] & 0x07;
		$this->GIF_colorS = 2 << $this->GIF_colorC;

		if ( $this->GIF_colorF == 1 ) {
			GIFDecoder::GIFGetByte ( 3 * $this->GIF_colorS );
			$this->GIF_global = $this->GIF_buffer;
		}
		for ( $cycle = 1; $cycle; ) {
			if ( GIFDecoder::GIFGetByte ( 1 ) ) {
				switch ( $this->GIF_buffer [ 0 ] ) {
					case 0x21:
						GIFDecoder::GIFReadExtensions ( );
						break;
					case 0x2C:
						GIFDecoder::GIFReadDescriptor ( );
						break;
					case 0x3B:
						$cycle = 0;
						break;
				}
			}
			else {
				$cycle = 0;
			}
		}
	}
	/*
	:::::::::::::::::::::::::::::::::::::::::::::::::::
	::
	::	GIFReadExtension ( )
	::
	*/
	function GIFReadExtensions ( ) {
		GIFDecoder::GIFGetByte ( 1 );
		if ( $this->GIF_buffer [ 0 ] == 0xff ) {
			for ( ; ; ) {
				GIFDecoder::GIFGetByte ( 1 );
				if ( ( $u = $this->GIF_buffer [ 0 ] ) == 0x00 ) {
					break;
				}
				GIFDecoder::GIFGetByte ( $u );
				if ( $u == 0x03 ) {
					$this->GIF_anloop = ( $this->GIF_buffer [ 1 ] | $this->GIF_buffer [ 2 ] << 8 );
				}
			}
		}
		else {
			for ( ; ; ) {
				GIFDecoder::GIFGetByte ( 1 );
				if ( ( $u = $this->GIF_buffer [ 0 ] ) == 0x00 ) {
					break;
				}
				GIFDecoder::GIFGetByte ( $u );
				if ( $u == 0x04 ) {
					if ( $this->GIF_buffer [ 1 ] & 0x80 ) {
						$this->GIF_dispos [ ] = ( $this->GIF_buffer [ 0 ] >> 2 ) - 1;
					}
					else {
						$this->GIF_dispos [ ] = ( $this->GIF_buffer [ 0 ] >> 2 ) - 0;
					}
					$this->GIF_delays [ ] = ( $this->GIF_buffer [ 1 ] | $this->GIF_buffer [ 2 ] << 8 );
					if ( $this->GIF_buffer [ 3 ] ) {
						$this->GIF_TransparentI = $this->GIF_buffer [ 3 ];
					}
				}
			}
		}
	}
	/*
	:::::::::::::::::::::::::::::::::::::::::::::::::::
	::
	::	GIFReadExtension ( )
	::
	*/
	function GIFReadDescriptor ( ) {
		$GIF_screen	= Array ( );

		GIFDecoder::GIFGetByte ( 9 );
		$GIF_screen = $this->GIF_buffer;
		$GIF_colorF = $this->GIF_buffer [ 8 ] & 0x80 ? 1 : 0;
		if ( $GIF_colorF ) {
			$GIF_code = $this->GIF_buffer [ 8 ] & 0x07;
			$GIF_sort = $this->GIF_buffer [ 8 ] & 0x20 ? 1 : 0;
		}
		else {
			$GIF_code = $this->GIF_colorC;
			$GIF_sort = $this->GIF_sorted;
		}
		$GIF_size = 2 << $GIF_code;
		$this->GIF_screen [ 4 ] &= 0x70;
		$this->GIF_screen [ 4 ] |= 0x80;
		$this->GIF_screen [ 4 ] |= $GIF_code;
		if ( $GIF_sort ) {
			$this->GIF_screen [ 4 ] |= 0x08;
		}
		/*
		 *
		 * GIF Data Begin
		 *
		 */
		if ( $this->GIF_TransparentI ) {
			$this->GIF_string = "GIF89a";
		}
		else {
			$this->GIF_string = "GIF87a";
		}
		GIFDecoder::GIFPutByte ( $this->GIF_screen );
		if ( $GIF_colorF == 1 ) {
			GIFDecoder::GIFGetByte ( 3 * $GIF_size );
			if ( $this->GIF_TransparentI ) {
				$this->GIF_TransparentR = $this->GIF_buffer [ 3 * $this->GIF_TransparentI + 0 ];
				$this->GIF_TransparentG = $this->GIF_buffer [ 3 * $this->GIF_TransparentI + 1 ];
				$this->GIF_TransparentB = $this->GIF_buffer [ 3 * $this->GIF_TransparentI + 2 ];
			}
			GIFDecoder::GIFPutByte ( $this->GIF_buffer );
		}
		else {
			if ( $this->GIF_TransparentI ) {
				$this->GIF_TransparentR = $this->GIF_global [ 3 * $this->GIF_TransparentI + 0 ];
				$this->GIF_TransparentG = $this->GIF_global [ 3 * $this->GIF_TransparentI + 1 ];
				$this->GIF_TransparentB = $this->GIF_global [ 3 * $this->GIF_TransparentI + 2 ];
			}
			GIFDecoder::GIFPutByte ( $this->GIF_global );
		}
		if ( $this->GIF_TransparentI ) {
			$this->GIF_string .= "!\xF9\x04\x1\x0\x0". chr ( $this->GIF_TransparentI ) . "\x0";
		}
		$this->GIF_string .= chr ( 0x2C );
		$GIF_screen [ 8 ] &= 0x40;
		GIFDecoder::GIFPutByte ( $GIF_screen );
		GIFDecoder::GIFGetByte ( 1 );
		GIFDecoder::GIFPutByte ( $this->GIF_buffer );
		for ( ; ; ) {
			GIFDecoder::GIFGetByte ( 1 );
			GIFDecoder::GIFPutByte ( $this->GIF_buffer );
			if ( ( $u = $this->GIF_buffer [ 0 ] ) == 0x00 ) {
				break;
			}
			GIFDecoder::GIFGetByte ( $u );
			GIFDecoder::GIFPutByte ( $this->GIF_buffer );
		}
		$this->GIF_string .= chr ( 0x3B );
		/*
		 *
		 * GIF Data End
		 *
		 */
		$this->GIF_arrays [ ] = $this->GIF_string;
	}
	/*
	:::::::::::::::::::::::::::::::::::::::::::::::::::
	::
	::	GIFGetByte ( $len )
	::
	*/
	function GIFGetByte ( $len ) {
		$this->GIF_buffer = Array ( );

		for ( $i = 0; $i < $len; $i++ ) {
			if ( $this->GIF_bfseek > strlen ( $this->GIF_stream ) ) {
				return 0;
			}
			$this->GIF_buffer [ ] = ord ( $this->GIF_stream { $this->GIF_bfseek++ } );
		}
		return 1;
	}
	/*
	:::::::::::::::::::::::::::::::::::::::::::::::::::
	::
	::	GIFPutByte ( $bytes )
	::
	*/
	function GIFPutByte ( $bytes ) {
		foreach ( $bytes as $byte ) {
			$this -> GIF_string .= chr ( $byte );
		}
	}
	/*
	:::::::::::::::::::::::::::::::::::::::::::::::::::
	::
	::	PUBLIC FUNCTIONS
	::
	::
	::	GIFGetFrames ( )
	::
	*/
	function GIFGetFrames ( ) {
		return ( $this->GIF_arrays );
	}
	/*
	:::::::::::::::::::::::::::::::::::::::::::::::::::
	::
	::	GIFGetDelays ( )
	::
	*/
	function GIFGetDelays ( ) {
		return ( $this->GIF_delays );
	}
	/*
	:::::::::::::::::::::::::::::::::::::::::::::::::::
	::
	::	GIFGetLoop ( )
	::
	*/
	function GIFGetLoop ( ) {
		return ( $this->GIF_anloop );
	}
	/*
	:::::::::::::::::::::::::::::::::::::::::::::::::::
	::
	::	GIFGetDisposal ( )
	::
	*/
	function GIFGetDisposal ( ) {
		return ( $this->GIF_dispos );
	}
	/*
	:::::::::::::::::::::::::::::::::::::::::::::::::::
	::
	::	GIFGetTransparentR ( )
	::
	*/
	function GIFGetTransparentR ( ) {
		return ( $this->GIF_TransparentR );
	}
	/*
	:::::::::::::::::::::::::::::::::::::::::::::::::::
	::
	::	GIFGetTransparentG ( )
	::
	*/
	function GIFGetTransparentG ( ) {
		return ( $this->GIF_TransparentG );
	}
	/*
	:::::::::::::::::::::::::::::::::::::::::::::::::::
	::
	::	GIFGetTransparentB ( )
	::
	*/
	function GIFGetTransparentB ( ) {
		return ( $this->GIF_TransparentB );
	}
}
?>
