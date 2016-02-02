[![wercker status](https://app.wercker.com/status/cd6fe8c863a9b457343911a5d4bdba26/s "wercker status")](https://app.wercker.com/project/bykey/cd6fe8c863a9b457343911a5d4bdba26)

CPMS
=============

Channel Bridge CPMS Package used by channel bridge micro service.

Version
---------
0.5.0

Status
-------
Will still be maintenance and develop.

Installation
-------------
 - [Set Up SSH][stupgitssh] so you don't need to be able pull from repo with username and password
 - If you have multiple account at bitbucket and you want to register it on the same computer [Read This][multiacc]

After done add this syntax below to your composer.json file

```javascript
{
	"repositories": [
		{
			"type": "vcs",
			"url": "git@bitbucket.org:acommerceplatform/cm-channel-bridge-cpms-package.git"
		}
	],
	"require": {
        "channelbridge/cmps": "dev-master"
    }
}
```

[stupgitssh]: <https://confluence.atlassian.com/bitbucket/set-up-ssh-for-git-728138079.html>
[multiacc]: <https://confluence.atlassian.com/bitbucket/configure-multiple-ssh-identities-for-gitbash-mac-osx-linux-271943168.html>
