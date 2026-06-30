# File Mime Validator Module

## Contents of This File

 - Introduction
 - Requirements
 - Installation
 - Configuration
 - Issues & Feature Requests
 - Maintainers

## Introduction

 - It can perform a more secure and reliable check.
 - It works on the upload type file field used in any entity.
 - This module performs a server-side validation for the extension.
 - It helps in site making malicious type uploads out of the system.
 - Logs the upload if found malicious i.e not the type which is being requested.
 - For a full description of the module, visit
   [project page](https://www.drupal.org/project/file_mime_validator).

## Requirements

 - Drupal 9 or 10.

## Installation

 - Install the module and all its dependencies as you would do with any other
   Drupal module.
   If using [Composer](https://getcomposer.org/) for dependency management,
   you can use
   `composer require "drupal/file_mime_validator"`
 - Enable the module `drush en file_mime_validator`

## Configuration

 - `Admin->Config->System->File Mime Validator`
 - Configure file mime types by adding to default ones.

## Issues & Feature Requests

 - The module is considered feature complete by the maintainers. If you find a
   bug or are missing really important features, please use the module's
   [issue queue](https://www.drupal.org/project/issues/file_mime_validator).

## Maintainers

 - [Akshay Singh](https://www.drupal.org/u/akshay-singh)
