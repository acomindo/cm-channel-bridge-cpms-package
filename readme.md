CMPS
=============

Channel Bridge CMPS Package used by channel bridge micro service.

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
			"url": "git@bitbucket.org:acommerceplatform/cmp.git"
		}
	],
	"require": {
        "acommerce/cmp": "dev-master"
    }
}
```


if you have question don't hesitate to mail me at <rizha.musthafa@acommerce.asia>


[stupgitssh]: <https://confluence.atlassian.com/bitbucket/set-up-ssh-for-git-728138079.html>
[multiacc]: <https://confluence.atlassian.com/bitbucket/configure-multiple-ssh-identities-for-gitbash-mac-osx-linux-271943168.html>
