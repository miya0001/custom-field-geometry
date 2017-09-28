# miya/custom-field-geometry

[![Build Status](https://travis-ci.org/miya0001/custom-field-geometry.svg?branch=master)](https://travis-ci.org/miya0001/custom-field-geometry)
[![Latest Stable Version](https://poser.pugx.org/miya/custom-field-geometry/v/stable)](https://packagist.org/packages/miya/custom-field-geometry)
[![Total Downloads](https://poser.pugx.org/miya/custom-field-geometry/downloads)](https://packagist.org/packages/miya/custom-field-geometry)
[![Latest Unstable Version](https://poser.pugx.org/miya/custom-field-geometry/v/unstable)](https://packagist.org/packages/miya/custom-field-geometry)
[![License](https://poser.pugx.org/miya/custom-field-geometry/license)](https://packagist.org/packages/miya/custom-field-geometry)

Add a custom field to save latitude and longitude to the edit screen of the specific post type for WordPress.

![](https://www.evernote.com/l/ABWonhEnAJpDvqEwTmDVfIHVcINjPLYqRPAB/image.png)

## Install

```
$ composer require miya/custom-field-geometry
```

## How to use

```
<?php

require_once( dirname( __FILE__ ) . '/vendor/autoload.php' );

$map = new \Miya\WP\Custom_Field\Geometry( 'geometry', 'Latitude and Longitude' );
$map->add( 'post' ); // Set post type to display meta box.
```
