#!/usr/bin/env bash

if [ "${TRAVIS_PULL_REQUEST}" == "false" ] && [ "${TRAVIS_BRANCH}" == "master" ]; then

  echo "Clears git information\n"
  rm -rf .git

  echo "Writing custom gitignore for build\n"
  cat "# Build Ignores\n" > .gitignore
  cat "composer.phar\n" >> .gitignore
  cat "config.json\n" >> .gitignore
  cat "deploy_key.*\n" >> .gitignore
  cat "codeclimate.json\n" >> .gitignore
  cat "coverage.xml\n" >> .gitignore

  echo "Sets up package for sending\n"
  git init
  git remote add deploy $DEPLOY_URI
  git add -f -all .
  git commit -m "Deply from Travis - build {$TRAVIS_BUILD_NUMBER}"

  echo "Sets up permissions\n"
  echo -e "Host comments.reynrick.com\n\tStrictHostKeyChecking no\n" >> ~/.ssh/config
  openssl aes-256-cbc -K $encrypted_a9d53792e855_key -iv $encrypted_a9d53792e855_iv in deploy_key.pem.enc -out deploy_key.pem -d
  eval "$(ssh-agent -s)"
  chmod 600 deploy_key.pem
  ssh-add deploy_key.pem

  echo "Sends build\n"
  git push -f deploy master

fi
