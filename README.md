# auto-ffmpeg #
moved from private repository in bitbucket to github.

This is remodeled for old transcoder back in 2015, where we need to transcode 1000 high res clip in just one week.

### What is this repository for? ###

A bunch of PHP script, to automate transcoding process. The main transcoder is ffmpeg, a well-known command line transcoder. The php script just there to pass parameter to ffmpeg process.


### How do I get set up? ###

Clearly, you need a php binnary with mysql (and some msssql) support. The test has been done on windows, since we need ffmpeg with quicksync most, there are already built's ffmpeg binnary that support libmfx (zeranoe's).
In case of *nix, you need to change source and destionation. And windows, use map drive. On linux, use mounted folder.
First setup will be preparing DB.

### Todo : ###

1. Store all parameter (exept db) into db
2. Make parameter editable through web client
3. Make a customized segmen
4. Make a choice for user to choose which segmen to use (invenio,bms,or custom)

### Who do I talk to? ###

eddy.prasetyo@warneter.net
