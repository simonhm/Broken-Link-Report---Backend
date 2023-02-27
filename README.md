# IPEDS E Resources Report

## Introduction
This program captures data reported from patron in CSV files and emailed to librarians, who can then perform additional research to see if the article or journal is available and to investigate the cause of the broken link.

## Requirements

PHP Hosting: for running this PHP web-based program 

## Installation

Download and upload all files and folders into your hosting environment. 

## Configuration

### config.ini
Edit this file to provide the list of URL identify and library emails as formatted here:

[Library URL identify and Emails: lib_code = list of URL identify|list of emails]

```
TST = -tst.primo,-test.primo,test.primo|your_library_email1@your_domain.com, your_library_email2@your_domain.com
```

## Instructions for Using the Program

Once Configuration is Complete

Follow this blog to setup the frontend form for Broken Link report in PrimoVE:

https://developers.exlibrisgroup.com/blog/report-a-broken-link-in-primove/

Important note:
```
In custom.js, change this code at the very bottom:
From
xhttp.open("POST", "https://your.domain.edu/path_to_code/", true);
To
xhttp.open("POST", "https://your_hosting_domain.com/broken_link_backend.php", true);
```

Then ... that's it! When the patron sends the report, the data is captured in CSV files and emailed to librarians

## View reports

This program captures data in CSV files and provides a simple view page where library staff can browse and download the broken link reported. 
There are 2 options:

### Option 1: View reports of specific library

https://your.domain.edu/path_to_code/view_reports.php?lib=TST

TST is the lib code you're setting for each libraries in config.ini

### Option 2: View reports of ALL libraries

https://your.domain.edu/path_to_code/view_reports.php?lib=ALL

## Maintainers/Sponsors

Current maintainers:

* [Simon Mai](https://github.com/simonhm)
