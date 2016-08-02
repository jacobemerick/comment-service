#!/usr/bin/env bash

if [ "${TRAVIS_PULL_REQUEST}" == "false" ] && [ "${TRAVIS_BRANCH}" == "master" ]; then

  echo "Clears git information"
  rm -rf .git

  echo "Rebuilds dependencies"
  rm -rf vendor
  composer install --no-dev --no-interaction --ignore-platform-reqs

  echo "Writing custom gitignore for build"
  echo "# Build Ignores" > .gitignore
  echo "composer.phar" >> .gitignore
  echo "config.json" >> .gitignore
  echo "deploy_key.*" >> .gitignore
  echo "build/" >> .gitignore
  echo "codeclimate.json" >> .gitignore
  echo "coverage.xml" >> .gitignore

  echo "Sets up package for sending"
  git init
  git remote add deploy $DEPLOY_URI
  git config user.name $DEPLOY_USER
  git config user.email $DEPLOY_EMAIL
  git add --all .
  git commit -m "Deply from Travis - build {$TRAVIS_BUILD_NUMBER}"

  echo "Sets up permissions"
  echo -e "Host comments.reynrick.com\n\tStrictHostKeyChecking no" >> ~/.ssh/config
  openssl aes-256-cbc -K $encrypted_a9d53792e855_key -iv $encrypted_a9d53792e855_iv -in deploy_key.pem.enc -out deploy_key.pem -d
  eval "$(ssh-agent -s)"
  chmod 600 deploy_key.pem
  ssh-add deploy_key.pem

  echo "Sends build\n"
  git push -f deploy master

fi
