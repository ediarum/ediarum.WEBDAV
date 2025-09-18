
# Usage

## Add an account

* Go to `localhost:8000` or the service URL, and click register to set up an account.
* Once you have an account, use the email and password you entered to log in to the webdav, located at the `webdav` subroot, so for example: `localhost:8000/webdav`.

## Setting up a project with a git repository

In order to set up a project where all webdav changes are saved as git commits, it is necessary that the git repository of the project be saved on the server.

Then, in the settings to the project, the user must enter the absolute path to this repository.

If one sets up a project with GitLab remote url, GitLab username, and Gitlab Personal Access Token (PAT), then all new commits are automatically pushed to gitlab.
