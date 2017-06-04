<?php

if (PHP_VERSION_ID >= 70000) {
	echo '6.2';
} else if (PHP_VERSION_ID >= 50600) {
	echo '5.7';
} else {
	echo '4.8';
}
