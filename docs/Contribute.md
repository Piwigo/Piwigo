# Get the Piwigo project :

## Prerequisites
To contribute to project, you will need to install on your computer [Git](https://git-scm.com/) and to have a [Github account](https://github.com/join?source=header-home).

If you don't really know how to use git, I will recommend you to check this [guide](https://git-scm.com/book/en/v2/Getting-Started-Git-Basics). If you are comfortable with git and GitHub, just go to "Create a pull request" section.

### How to configure Git :

To add associate your GitHub account and your Git, you will need use this command on Git Bash :

- Set up your name :
`git config --global user.name "Firstname lastname"`

- Set up your e-mail address :
`git config --global user.email "email@example.com"`

### Get the project on your GitHub account :
You have to click on the fork button of the [Piwigo GitHub](https://github.com/Piwigo/Piwigo).

![Lien](HowToFork.png)

### Get the project on your computer :
Go where you want to work on the project (for example in your **../www/** folder) and enter this command :

Don't forget to replace `username` with your GitHub username.

`git clone https://github.com/username/Piwigo`

Set up the link for git to say where the original Piwigo repositories comes from :

`git remote add upstream https://github.com/Piwigo/Piwigo`

## Create a branch

To fix a problem that you viewed on Issue tab on GitHub, you will need to create a separate space to change the code to not lose the original code.
To do that, you have to switch and create a new branch based on the `master` or somewhere else.

- To switch branch:
`git checkout master`

- To create a new branch:
`git checkout -b BranchName`

To choose name of the new branch, it's better to give a explicit name and give the tag of the issue (example : `#496`)

now, you can work on your new branch.

To affect changes to your project on your GitHub (need to do commit(s) before), just push with this command:

`git push origin master`

If there is some changes on the upstream project, you will need to update your local branch.

`git pull upstream master`

Then go on your branch where you did changes and enter:

`git merge master`

## Create a pull request

On GitHub you should see your commit that you pushed before like this :

![Lien](HowToFork.png)
