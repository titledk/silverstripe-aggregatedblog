# SilverStripe Aggregated Blog

This module helps aggregating or importing a blog from an RSS feed.    


It requires the "external content module", and the "rssconnector" module 
(actually our fork of it, which is upgraded to ss 3.1). See below for installation.

## Installation

_You need to install via Composer at that'll handle all the dependencies. You need to have
the following part of your `composer.json` (apart from the ususals):_

	{
		"repositories": [
			{
				"type": "vcs",
				"url": "https://github.com/anselmdk/silverstripe-rssconnector"
			}
		],
		"require": {
			"titledk/aggregatedblog": "dev-master"
		}
	}



