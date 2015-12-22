CMBridgeSquid
=============

**Channel Micro Bridge Squid** for integration Channel Management to aCommerce Squid.

Version
-------
0.1.0

Status
------
Will still be maintenance and develop.

Installation
------------

Config your computer to be able pull from bitbucket without username and password
-[Set Up SSH][stupgitssh]
-If you have multiple account at bitbucket and you want to register it on the same computer [Read This][multiacc]

After done add this syntax below to your composer.json file

```javascript
{
	"repositories": [
		{
			"type": "vcs",
			"url": "git@bitbucket.org:pseudecoder/cmbridgesquid.git"
		}
	],
	"require": {
        "acommerce/cmbridgequid": "dev-master"
    }
}
```


if you have question don't hesitate to mail me at <rizha.musthafa@acommerce.asia>


[stupgitssh]: <https://confluence.atlassian.com/bitbucket/set-up-ssh-for-git-728138079.html>
[multiacc]: <https://confluence.atlassian.com/bitbucket/configure-multiple-ssh-identities-for-gitbash-mac-osx-linux-271943168.html>