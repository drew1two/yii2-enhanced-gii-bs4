Just trying to bring mootensai's yii2-enhanced-gii up to date.

This is an updated version of mootensai's yii2-enhanced-gii.

Please visit https://github.com/mootensai/yii2-enhanced-gii to pay homage to a great utility.

Here I have updated the templates to utilize bootstrap 4, 
added some outstanding pull requests that made sense, 
and applied some enhancements of my own.

One enhancement is the ability to create a default Yii2 identityInterface as a BaseUser model.
Meaning, we can just create a New User model that will include Yii2's default User model methods if required.

Haven't yet tested this with Kartik's tree-manager.

Installation:- add the following to our composer.json

```
"drew1two/yii2-enhanced-gii-bs4": "dev-master",
"kartik-v/yii2-mpdf": "dev-master"

"repositories": [
    {
        "type": "composer",
        "url": "https://asset-packagist.org"
    },
    {
        "type": "vcs",
        "url": "git@github.com:drew1two/yii2-enhanced-gii-bs4.git",
    }
]
```

