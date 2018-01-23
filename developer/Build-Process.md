# Build Process

This page documents the steps that a phpCollab developer/contributor should take for building a phpCollab server locally.

## Development 

## Source Checkout

The following shell commands may be used to grab the source from the repository:

```bash
git clone git@github.com:phpcollab/phpcollab.git phpcollab
```

Or a quicker clone:

```bash
git clone --depth=1 --single-branch --branch=master git@github.com:phpcollab/phpcollab.git phpcollab
# git fetch --unshallow
```

For a successful clone, you will need to have [set up SSH keys](https://help.github.com/articles/working-with-ssh-key-passphrases/) for your account on Github.
If that is not an option, you may clone the phpCollab repository under `https` via `https://github.com/phpcollab/phpcollab.git`.

## Build

The following shell commands may be used to build the source:

```bash
cd phpcollab
git checkout master
```

When done, you may build the codebase via the following command:

```bash
./composer install
```

## IDE Setup

phpCollab development may be carried out using any modern IDE. 

#### Plugins

The following plugins may prove useful during development:

- [Symfony Plugin](https://plugins.jetbrains.com/plugin/7219-symfony-plugin)
- [PHP Annotations](https://plugins.jetbrains.com/plugin/7320-php-annotations)


## Testing Modules

To test the functionality provided by a given phpCollab module, execute the following steps:

```bash
vendor/bin/codecept run
```

## Deploy
Point your web server's root to the phpCollab folder