#How to deal with the PLA subrepository

##Cloning everything
You have several options:

	1)
		git clone [LearnGLASS.git] [rootdir] --recursive

	2)
		git clone [LearnGLASS.git] [rootdir]
		cd [rootdir]
		git submodules update --init
	3)
		git clone <LearnGLASS.git> <rootdir>
		git submodules init
		git submodules update

Note that this will put PLA in (no branch) so any changes you do may get lost since there's no pointer to them.
To change to a branch do:

		cd [rootdir]/PLA
		git checkout master

Or create other branch.

##Pushing changes
The only time when you need to be careful is if you modify PLA. In this case, pushing the main repo first will point to the previous version of PLA, so if you clone the repo later the changes on PLA will not show. You have to push PLA first and then the main repo for it to retain the changes.

#Omitted files
Some files are not tracked online because they contain private information. They are usually scripts with credentials that run other scripts, so no big funcionality is missing, but they are included here anyway. Contact the original developers to get them.

/capture/Sources/get_logfiles.sh
/capture/Metadata/User_data/get_userfiles.sh
/capture/Process/basic.cfg
