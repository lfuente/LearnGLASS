#How to deal with the PLA subrepository

##Cloning everything
You have 3 options:
*	git clone <LearnGLASS.git> <rootdir> --recursive
*	git clone <LearnGLASS.git> <rootdir>
	cd <rootdir>
	*	git submodules update --init
	*	git submodules init
		git submodules update

Note that this will put PLA in (no branch) so any changes you do may get lost since there's no pointer to them.
To change to a branch do:
cd <rootdir>/PLA
git checkout master

Or create other branch.
