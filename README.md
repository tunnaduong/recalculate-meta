# Recalculate meta

This extension allows to re-calculate the meta information of all discussions and users on demand.

Why you might need this extension:

- You manually imported data into your forum and need to create the meta values
- You manually deleted data from your forum and need to fix the meta values
- A Flarum update changed the way the meta is calculated and you want to fix your existing data

**A word of caution**: if an extension makes changes to the way these meta values are calculated, then this script will probably break it and reset all values to the way Flarum calculates them out of the box!

I strongly recommend you test the command on a backup first before running it on your live data!

## Installation and update

Compatible with both beta 13 and beta 14.

    composer require migratetoflarum/recalculate-meta

## Documentation

The extension is implemented as a command line utility.
Enable the extension in Flarum admin panel, then open a terminal and `cd` into the Flarum folder.

To run the command with the default options:

    php flarum migratetoflarum:recalculate-meta

You can enable or disable various calculations with command options, for example:

    php flarum migratetoflarum:recalculate-meta --skip-users
    php flarum migratetoflarum:recalculate-meta --do-discussion-number-index

To get a list of all available options, run:

    php flarum help migratetoflarum:recalculate-meta

## Removal

When you are done with the update, you can safely remove the extension.
It does not need to stay installed.

    composer remove migratetoflarum/recalculate-meta

## Links

- [Source code on GitHub](https://github.com/migratetoflarum/recalculate-meta)
- [Report an issue](https://github.com/migratetoflarum/recalculate-meta/issues)
- [Download via Packagist](https://packagist.org/packages/migratetoflarum/recalculate-meta)

The initial version of this extension was sponsored by [@Wadera](https://discuss.flarum.org/u/Wadera)
