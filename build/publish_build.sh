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
git config --global user.name "neilime"
git config --global user.email "$COMMIT_AUTHOR_EMAIL"

echo -e "# Publish build #\n"

# Copy generated doc into $HOME
cp -R build/phpdoc $HOME/phpdoc

# Copy generated doc into $HOME
cp -R build/coverage $HOME/coverage

#  Retrieve branch gh-pages
echo -e " * Retrieve branch gh-pages"
cd $HOME
git clone --quiet --branch=gh-pages https://${GH_TOKEN}@github.com/${TRAVIS_REPO_SLUG} gh-pages > /dev/null
cd gh-pages

# Remove old PHPDoc
if [ -d ./phpdoc ]; then
    echo -e " * Remove old PHPDoc"
    git rm -rf ./phpdoc
fi

# Create new PHPDoc directory
echo -e " * Create new PHPDoc directory"
mkdir ./phpdoc

# Copy new PHPDoc version
echo -e " * Copy new PHPDoc version"
cp -Rf $HOME/phpdoc/* ./phpdoc/

# Remove old Coverage
if [ -d ./coverage ]; then
    echo -e " * Remove old Coverage"
    git rm -rf ./coverage
fi

# Create new Coverage directory
echo -e " * Create new Coverage directory"
mkdir ./coverage

# Copy new Coverage version
echo -e " * Copy new Coverage version"
cp -Rf $HOME/coverage/* ./coverage/

# Set new readme file content into index page
NEW=`curl -H 'Cache-Control: no-cache' -s https://github.com/neilime/zf2-assets-bundle/blob/master/README.md | sed -n "/<article class=\"markdown-body entry-content\" itemprop=\"text\">/,/<\/article>/p" | grep -v "<article class=\"markdown-body entry-content\" itemprop=\"text\">" | grep -v "</article>"`;
awk -v var="$NEW" '/<section class="main-content">/{p=1;print "<section class=\"main-content\""var}/<\/section>/{p=0}!p' index.html > index.html.tmp
mv index.html.tmp index.html

# Add, commit & push all files to git
echo -e " * Add, commit & push all files to git"
git add -f .
git commit -m "PHPDocumentor (Publish Travis Build : $TRAVIS_BUILD_NUMBER)"
git push -fq origin gh-pages > /dev/null

# Done
echo -e " * Published PHPDoc & Coverage to gh-pages.\n"