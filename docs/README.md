# Documentation Website

The Documentation Website is built using [Docusaurus](https://docusaurus.io/).

If you want to make a contribution to the documentation, please follow these
instructions:

1. Fork the `master` branch of this repository to your GitHub profile. Do not fork the `gh-pages` branch as it only contains the "built" version.
2. Create a branch to work on your changes
3. Test your changes locally (see below for instructions on how to use Docusaurus)
4. Create a Pull Request against the `master` branch to submit your changes


## Install and Develop with Docusaurus

This is not a tutorial on how to use Docusaurus. Please refer to Docusaurus documentation.

### Installation

On your local fork of the Documentation, use `yarn` to install
the Docusaurus dependencies.

```
$ yarn
```

### Local Development

Once the dependencies are installed, you can make changes to the documentation source files.

Typically, only the files located under `/docs`, `/src` and `/blog` should be changed.

Changes to the navigation and footer are made to the `docusauraus.config.js` file.

To test locally your changes, run:

```
$ yarn start
```

This command starts a local development server and opens up a browser window. Most changes are reflected live
without having to restart the server.

## Test builds

A GitHub action is define to test builds. It will run on push to your fork.

You may have to enable workflows on your fork before they are run.

## Submit changes

Once you are satisfied with your changes and the `Build GitHub Pages No Deploy` action has
passed successfully, then create and submit a Pull Request against the `master` branch of
the repository.

