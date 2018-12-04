## WORKER ##

This is the heart of this project. The script pull job from queue in database, and run it one by one. If there's no job in queue, it's just sit there waiting.
You can add as much as workstation for a massive job. Initially I use 4 workstation to transcode 1000 episode each 1 hour duration. The transcode process finish in a week with ffmpeg + quicksync.

There's a lot of special case for step by step job here that's need to be generalise, because initially it's just private project for company, and I push it to the public repository.
Even though I use php as scripting language, I don't use object oriented style. It's just pure procedural programming

Todo :
-generalise step into function
-push every optional parameters into database
-learn to use OOP style
