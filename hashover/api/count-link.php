<?php namespace HashOver;

// Copyright (C) 2018 Jacob Barkdull
// This file is part of HashOver.
//
// HashOver is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// HashOver is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with HashOver.  If not, see <http://www.gnu.org/licenses/>.


// Change to the HashOver directory
chdir (realpath ('../'));

// Setup HashOver for JavaScript
require ('backend/javascript-setup.php');

try {
	// Instantiate general setup class
	$setup = new Setup (array (
		'mode' => 'javascript',
		'context' => 'api'
	));

	// Throw exception if the "Latest Comments" API is disabled
	if ($setup->apiStatus ('count-link') === 'disabled') {
		throw new \Exception ('This API is not enabled.');
	}

	// Instantiate HashOver statistics class
	$statistics = new Statistics ('javascript');

	// Start execution timer
	$statistics->executionStart ();

	// Instantiate JavaScript build class
	$javascript = new JavaScriptBuild ('api/frontends/count-link');

	// Register initial constructor
	$javascript->registerFile ('constructor.js');

	// Register comment count AJAX request getter method
	$javascript->registerFile ('getcommentcount.js');

	// Change to standard frontend directory
	$javascript->changeDirectory ('frontend');

	// Register HashOver script tag getter method
	$javascript->registerFile ('script.js');

	// Register backend path setter
	$javascript->registerFile ('backendpath.js');

	// Register HashOver ready state detection method
	$javascript->registerFile ('onready.js');

	// Register element creation methods
	$javascript->registerFile ('elements.js');

	// Register AJAX-related methods
	$javascript->registerFile ('ajax.js');

	// Change back to count link frontend directory
	$javascript->changeDirectory ('api/frontends/count-link');

	// Register automatic instantiation code
	$javascript->registerFile ('instantiate.js', array (
		'include' => !isset ($_GET['nodefault'])
	));

	// JavaScript build process output
	$output = $javascript->build (
		$setup->minifiesJavascript,
		$setup->minifyLevel
	);

	// Display JavaScript build process output
	echo $output, PHP_EOL;

	// Display statistics
	echo $statistics->executionEnd ();

} catch (\Exception $error) {
	$misc = new Misc ('javascript');
	$message = $error->getMessage ();
	$misc->displayError ($message);
}
