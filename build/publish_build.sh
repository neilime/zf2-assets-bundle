#!/bin/bash

set -e # Exit with nonzero exit code if anything fails

SOURCE_BRANCH="master"
TARGET_BRANCH="gh-pages"

# Pull requests and commits to other branches shouldn't try to deploy, just build to verify
if [ "$TRAVIS_PULL_REQUEST" != "false" -o "$TRAVIS_BRANCH" != "$SOURCE_BRANCH" ]; then
    echo -e "Skipping publish; just doing a build.\n"
    exit 0
fi

## Initialize git
git config user.name "Travis CI"
git config user.email "$COMMIT_AUTHOR_EMAIL"

# Save some useful information
REPO=`git config remote.origin.url`
SSH_REPO=${REPO/https:\/\/github.com\//git@github.com:}
SHA=`git rev-parse --verify HEAD`

echo -e "1. Publishing PHPDoc\n"

# Copy generated doc into $HOME
cp -R build/phpdoc $HOME/phpdoc

#  Retrieve branch gh-pages
echo -e " * Retrieve branch gh-pages"
cd $HOME
git clone --quiet --branch=gh-pages $REPO gh-pages > /dev/null
cd gh-pages

# Remove old PHPDoc
if [ -d ./phpdoc ]; then
    echo -e " * Remove old PHPDoc"
    git rm -rf ./phpdoc
fi

## Create new PHPDoc directory
echo -e " * Create new PHPDoc directory"
mkdir ./phpdoc
cd ./phpdoc

# Copy new PHPDoc version
echo -e " * Copy new PHPDoc version"
cp -Rf $HOME/phpdoc/* ./

# Add, commit & push all files to git
echo -e " * Add, commit & push all files to git"
git add -f .
git commit -m "PHPDocumentor (Publish Travis Build : $TRAVIS_BUILD_NUMBER)"
git push -fq origin gh-pages > /dev/null

# Done
echo -e " * Published PHPDoc to gh-pages.\n"