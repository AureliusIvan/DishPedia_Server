1. Server deployment script

Add this bash script to your repository, and name it server_deploy.sh.

This script will be executed on the server to pull and deploy the code from GitHub.

#!/bin/sh
set -e

echo "Deploying application ..."

# Enter maintenance mode
(php artisan down --message 'The app is being (quickly!) updated. Please try again in a minute.') || true
    # Update codebase
    git fetch origin deploy
    git reset --hard origin/deploy

    # Install dependencies based on lock file
    composer install --no-interaction --prefer-dist --optimize-autoloader

    # Migrate database
    php artisan migrate --force

    # Note: If you're using queue workers, this is the place to restart them.
    # ...

    # Clear cache
    php artisan optimize

    # Reload PHP to update opcache
    echo "" | sudo -S service php7.4-fpm reload
# Exit maintenance mode
php artisan up

echo "Application deployed!"

The process explained:

    We’re putting the application into maintenance mode and showing a sensible message to the users.
    We’re fetching the deploy branch and hard resetting the local branch to the fetched version.
    We’re updating composer dependencies based on the lock file. Make sure your composer.lock file is in your repository, and not part of your .gitignore. It makes sure the production environment uses the exact same version of packages as your local environment.
    We’re running database migrations.
    We’re updating Laravel & php-fpm caches. If you’re not using PHP 7.4, change the version in that command.
    We’re putting the server back up.

Note that the server is always on the deploy branch. Also note that we’re putting the server down for the shortest duration possible — only for the composer install, migrations and cache updating. The app needs to go down to avoid requests coming in when the codebase and database are not in sync — it would be irresponsible to simply run those commands without putting the server into maintenance mode first.

And a final note, the reason we’re wrapping the php artisan down command in (...) || true is that deployments sometimes go wrong. And the down command exits with 1 if the application is already down, which would make it impossible to deploy fixes after the previous deployment errored halfway through.
2. Local deployment script

This script is used in your local environment when you want to deploy to production. Ideally, if you work in a team, you’ll also have a CI Action running phpunit as a safeguard for pull requests targeting the production branch. For inspiration, see the link to the Action example in the How it works section above and make it run on pull_request only.

Store the local deployment script as deploy.sh:

#!/bin/sh
set -e

vendor/bin/phpunit

(git push) || true

git checkout production
git merge master

git push origin production

git checkout master

This script is simpler. We’re running tests, pushing changes (if we have not pushed yet; it’s assumed we’re on master), switching to production, merging changes from master and pushing production. Then we switch back to master.
3. The GitHub Action

Store this as .github/workflows/main.yml

name: CD

on:
  push:
    branches: [ production ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v2
      with:
        token: ${{ secrets.PUSH_TOKEN }}
    - name: Set up Node
      uses: actions/setup-node@v1
      with:
        node-version: '12.x'
    - run: npm install
    - run: npm run production
    - name: Commit built assets
      run: |
        git config --local user.email "action@github.com"
        git config --local user.name "GitHub Action"
        git checkout -B deploy
        git add -f public/
        git commit -m "Build front-end assets"
        git push -f origin deploy
    - name: Deploy to production
      uses: appleboy/ssh-action@master
      with:
        username: YOUR USERNAME GOES HERE
        host: YOUR SERVER'S HOSTNAME GOES HERE
        password: ${{ secrets.SSH_PASSWORD }}
        script: 'cd /var/www/html && ./server_deploy.sh'

Explained:

    We set up Node.
    We build the front-end assets.
    We force-checkout to deploy and commit the assets. The deploy branch is temporary and only holds the code deployed on the server. It doesn’t have a linear history — the asset compilation is never part of the production branch history — which is why we always have to force checkout that branch.
    We force-push the deploy branch to origin.
    We connect to the server via SSH and execute server_deploy.sh in the webserver root.

Note that you need to store two secrets in the repository:

    a Personal Access Token for a GitHub account with write access to the repository
    the SSH password

If you want to use SSH keys instead of usernames & passwords, see the documentation for the SSH action.
Usage

With all this set up, install your Laravel application into /var/www/html and checkout the deploy branch. If it doesn’t exist yet, you can do git checkout production && git checkout -b deploy to create it.

For all subsequent deploys all you need to do is run this command from the master branch in your local environment:

./deploy.sh

Or, you can merge into production. But know that it will not run tests unless you configure the action for that, as mentioned above.
Performance and robustness

This approach is robust since it makes it impossible for a request to be processed when the codebase and database are out of sync — thanks to artisan down.

And it’s also very fast, with the least amount of things happening on the server — only the necessary steps — which results in minimal downtime.

See how this action runs:

The Action running on GitHub and successfully deploying an application.

The Deploy to production step took only 13 seconds, and the period when the application was down is actually shorter than that — part of the 13 seconds is GitHub setting up the appleboy/ssh-action action template (before actually touching your server). So usually, the application would be down for less than 10 seconds.