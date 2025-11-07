
# Frequently Asked Questions for Users 

### Meine Datei kann nicht gespeichert werden. Was nun?

Wenn beim Speichern eine Fehlermeldung passiert, dann ist das in der Regel ein Problem mit der Sperre auf der Datei. Sie sperren immer eine Datei, indem Sie sie öffnen. Die Sperre verhindert, dass mehrere Nutzer eine Datei gleichzeitig bearbeiten. Beim Schließen der Datei wird die Sperre wieder aufgehoben. Wenn jedoch Oxygen nicht richtig geschlossen wird (z. B. wenn Ihr Computer abstürzt, Sie das Beenden von Oxygen erzwingen, usw.), kann der Server die bei der erneuten Öffnung von Oxygen neu entstandene Sperre nicht erkennen. Die Lösung besteht darin die Datei zu entsperren:
- Navigieren Sie in der «Datenquellen Explorer» (Englisch: "Data Source Explorer") zu der problematischen Datei
- Beim Rechtsklick auf die Datei erscheint ein Dropdownmenü. Drucken Sie auf "Unlock/Entsperren": ![Entsperren Screenshot](images/entsperren_screenshot.png)
- Jetzt sollten Sie wieder speichern können.
- Nachdem Sie die jetzt die Datei gespeichert haben, ist es empfehlenswert, falls Sie weiter an der Datei arbeiten möchten, die Datei wieder zu sperren. Das machen Sie, in dem Sie die Datei schließen (natürlich, nachdem Sie die Datei gespeichert haben, damit Ihre Änderungen nicht verloren gehen) und wieder aufmachen.

# Frequently Asked Questions for Developers

### Can I push data transformations or other commits I do locally to ediarum.WEBDAV?

Yes. However, there is, so far, no way to do this through the ediarum.WEBDAV Management Console.

Here is a way to do it manually.

First do your changes locally:
1. Check out the webdav branch locally on your computer.
2. Make your changes and commit them locally.
3. Push your changes to gitlab.

Then, deploy your changes to the server:
 1. ssh into the server php-prod
 2. `cd /opt/projects/ediarum.webdav/{your_project_folder}` 
 3. `sudo -u www-data git pull` Note: git pull on the server (php-prod) MUST always be run as the user www-data (i. e. via `sudo -u www-data …`).
    git pull will ask for a username and a password, the username must have access
    rights to this repository at gitup.uni-potsdam.de, the password must be a valid
    access token generated via Gitlab. After pulling the repository,
    the up-to-date data from the repository is automatically available via
    ediarum.WEBDAV.

There is, however, a big caveat here: NO USERS MAY MAKE A COMMIT (I.E. SAVE A FILE) BETWEEN THE TIME YOU MAKE YOUR CHANGES LOCALLY AND THE TIME YOU PULL THOSE CHANGES ONTO THE SERVER.
If you try to pull your data changes onto the server, and a user has made a commit in the meantime, git, on the server, will complain that the origin (gitlab) and the local server branch have diverged. The server has the newest user commit(s), which gitlab doesn't have, and gitlab has your data transformation commit, which the server doesn't have.  In this case, you'll probably be best served to reset the gitlab head to before your data transformation, then do a git push from the server, then pull those newest changes to your local machine, and redo your data transformation.

For projects with a lot of traffic, it is therefore recommended to disable the webdav connection while you:
 - make your changes locally
 - push them to gitlab
 - pull them to the server.

You can disable the webdav connection by, for example, changing the project slug to {project_slug_disabled}.  During that time, the user will then get a `404 Not Found` when trying to access the webdav.

#### Planned improvements
A feature is planned whereby developers can deploy data transformations over the Management Console.
This feature would have:

- a button to disable the webdav connection. In this time, and user will get a "Maintenance Mode" message when trying to access the webdav.
- a form to enter the name of the gitlab branch where the data transformation commits are located.  When the form is submitted, the server will automagically pull the changes from the data transformation branch onto the webdav branch. If there are merge conflicts, then the developer will receive an error message and be asked to resolve the problem on their branch and try again. We can also enable (or require?) a stricter mode, where the data transformation commits must be on top of the latest user commit (i.e. we would require a 'linear' git history). 



