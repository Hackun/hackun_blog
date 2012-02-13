#!/bin/bash

git_check_changes ()
{
	git status | grep 'Untracked files:\|Changed but not updated:\|Changes to be committed' 
	
	if [ $? -eq 0 ]	; then
		return 1
	else 
		if  git status | grep 'nothing to commit (working directory clean)' ; then
			return 0
		else
			echo "other"	
		fi
	
	fi
}

##############################################################

# check for svn and lftp
type -P git &>/dev/null || { echo "Git is required but does not appear to be installed. Aborting." >&2; exit 1; }
type -P lftp &>/dev/null || { echo "LFTP (lftp), FTP client, is required but does not appear to be installed. Aborting." >&2; exit 1; }

# set up ftp2git defaults
CONFIG_DIR=$HOME"/.ftp2git"
CONFIG_FILE=$CONFIG_DIR"/main.conf"
LOG_FILE=/tmp/ftp2git.log
PARA_NUM=1

## CONFIG CHECK ---------------------------------------------------------------

# check for config director existence - make it if it's not here.
if [ ! -d "$CONFIG_DIR" ]; then
	mkdir $CONFIG_DIR
fi

if [ -f "$CONFIG_FILE" ]; then
	if [ ! -r "$CONFIG_FILE" ]; then
		echo "Config file at "$CONFIG_FILE" is not readable. Please fix and re-run" >&2; exit 1;
	fi
else
	# no config file - dump the default and tell the user to prepare.
	cat > "$CONFIG_FILE" << EOF
## ftp2git config file
## please edit as required

# the name of the repository folder
# REPO_FOLDER_NAME="/home/git/gitweb/hackun_blog.git" (example)
REPO_FOLDER_NAME="xxx" 

# the name of the mirror folder inside your site archives
MIRROR_FOLDER_NAME="mirror"

## END config
EOF
	cat > "$CONFIG_DIR""/myself.site.conf" << EOF
## ftp2git site configuration example
## please edit as required
## create a copy of this file for each site to be archived

# details for where the site is located to be backed up
FTP_ADDRESS="xxx"
FTP_USER="xxx"
FTP_PASSWORD="xxx"

# the folder that should be mirrored and archived
FTP_FOLDER="/public_html"

# local details for the archive - relative to your home
ARCHIVE_LOCATION="ftp2git_bakup"

## END config
EOF
	echo ""
	echo "[INFO] New config files created in '"$CONFIG_DIR"'."
	echo "[HELP] Please edit as required and re-run this script with '--build' to set up the archive folders."
	echo ""
	exit 0;
fi

# might not even need these config settings
source "$CONFIG_FILE"

## GET ALL SITES --------------------------------------------------------------

if [ "$1" = "--build" ]; then
	echo ""
	echo "[INFO] Building and checking ftp2git folders and repositories."
	
	for file in $( find $CONFIG_DIR -type f -name '*.site.conf' )
	do
		echo "[INFO] Loading '"$file"'"

		source "$file"

		# build params
		ARCHIVE=$HOME"/"$ARCHIVE_LOCATION
		ARCHIVE_REPO=$REPO_FOLDER_NAME
		ARCHIVE_MIRROR=$ARCHIVE"/"$MIRROR_FOLDER_NAME



		## ---------------------------------------------- BUILDING CODE

		# build the main folder for this site...
		if [ ! -d "$ARCHIVE" ]; then
			echo "[ACTION] Making '"$ARCHIVE"'... "
			mkdir -p $ARCHIVE
		else
			echo "[INFO] Found archive folder : '"$ARCHIVE"'"
		fi

		# check that the mirror folder is a check-out of a repository
		# if it is, then we can assume the repo exists - this allows you to create
		# your own repo wherever you want.

		if [ ! -d "$ARCHIVE_MIRROR/.git" ]; then

			if [ ! -d "$ARCHIVE_MIRROR" ]; then

				cd $ARCHIVE

				# drop in Git
				if [ ! -d "$ARCHIVE_REPO" ]; then
					echo "[ACTION] Building Git repository at '"$ARCHIVE_REPO"'"
				    	pushd . >> /dev/null
					mkdir $REPO_FOLDER_NAME
					cd $REPO_FOLDER_NAME
					git --bare init
					popd >> /dev/null
				else
					echo "[INFO] Found repository folder : '"$ARCHIVE_REPO"'"
				fi

				echo "[ACTION] Making branches and tags structure."
				pushd . >> /dev/null
				mkdir $MIRROR_FOLDER_NAME
				cd $MIRROR_FOLDER_NAME
				git init
				popd >> /dev/null
			else
				echo "[ERROR] *** "$ARCHIVE_REPO" exists."
				echo "        *** but does not appear to be a valid repository."
				echo "...continuing checks"
			fi

		else
			echo "[INFO] Found mirror already exist in: '"$ARCHIVE_MIRROR"'"
			echo "[HELP] Use --pull to sync from ftp, or --push to sync to ftp."
			echo ""
			exit 1
		fi

		# check ftp
		if lftp -c "open $FTP_ADDRESS & user $FTP_USER $FTP_PASSWORD & cd $FTP_FOLDER"
		then
			echo "[INFO] FTP connection OK."

			# make the lftp file with instructions...
       			echo "[INFO] Writing LFTP command file: '"$file.lftp"'"
			cat > "$file.lftp" << EOF
set ftp:ssl-allow 0
set ftp:list-options -a
set cmd:fail-exit true
open $FTP_ADDRESS -u $FTP_USER,$FTP_PASSWORD
lcd $ARCHIVE_MIRROR
EOF
		echo "[INFO] Create lftp command files"
		# used to first build
		cp $file.lftp $file.lftp.build 
		echo mirror -c -e -x "\.git\/" $FTP_FOLDER $ARCHIVE_MIRROR  --parallel=$PARA_NUM --verbose=3 --log=$LOG_FILE >> $file.lftp.build
		# used to pull update from ftp 2 local 
		cp $file.lftp $file.lftp.pull 
		echo mirror -n -e -x "\.git\/" $FTP_FOLDER $ARCHIVE_MIRROR  --parallel=$PARA_NUM --verbose=3 --log=$LOG_FILE >> $file.lftp.pull
		# used to push update from local 2 ftp
		cp $file.lftp $file.lftp.push 
		echo mirror -R -e -n -x "\.git\/" $ARCHIVE_MIRROR  $FTP_FOLDER --parallel=$PARA_NUM --verbose=3 --log=$LOG_FILE >> $file.lftp.push
		
		
            echo "[INFO] Start first downloading"
            if lftp -f "$file.lftp.build" 
            then
                echo "[INFO] First download OK"
		echo "[INFO] Do first git commit"
                #cat "$LOG_FILE"
                # do the init commit
                pushd . >> /dev/null
                cd $ARCHIVE_MIRROR
                git add -A
                git commit -a -m 'Init commit:first sync from ftp'
                git remote add origin $ARCHIVE_REPO
                git push origin master
                popd >> /dev/null
            else
                echo "[ERROR] First sync Error"
            fi
		else
			echo "[ERROR] *** FTP credentials are not working."
		fi

		echo "[INFO] Checks complete for this site."
		echo ""

	done

	echo "[INFO] Finished first build and check."
	echo ""
	
	exit 0

else 
      if [ "$1" = "--pull" ]; then
	echo ""
	for file in $( find $CONFIG_DIR -type f -name '*.site.conf' )
	do
		echo "[INFO] Loading '"$file"'"

                source "$file"

                # build params
                ARCHIVE=$HOME"/"$ARCHIVE_LOCATION
                ARCHIVE_MIRROR=$ARCHIVE"/"$MIRROR_FOLDER_NAME
		LFTP_COMMS="$file.lftp.pull"

		if [ -f "$LFTP_COMMS" ]
		then
        		if [ ! -r "$LFTP_COMMS" ]
			then
                		echo "[ERROR] LFTP command file at "$LFTP_COMMS" is not readable."
				echo "*** Please fix and re-run."
				exit 1
			fi
			# fall through - file is readable and exists.
		else
			echo "[ERROR] LFTP command file does not exist yet."
			echo "*** Please run again with '--build' to create settings."
			exit 1
		fi

		### ------------- MIRRORING ----------------------###
		echo "[INFO] `date "+%F %T"` : Checking someing new from ftp..."
		if [ -f "$LOG_FILE" ]
		then
			rm "$LOG_FILE"
		fi
		#echo "$LFTP_COMMS"
		lftp -f $LFTP_COMMS
		if [ -s $LOG_FILE ]; then
		 echo "[Actions] lftp actions are below."
		 cat "$LOG_FILE"

		 ### ------------- Git WORK  ----------------------###
		 echo "[INFO] `date "+%F %T"` : Git work..."
		 cd "$ARCHIVE_MIRROR"
		 #git status
		 git add -A
		 git commit -a -m "Auto pull from ftp:`date +%F-%T`"
		 git push	
		else
		 echo "[INFO] Nothing new."
		fi
	
		echo "[INFO] Done FTP."
		echo "[INFO] ftp2git --pull done."
	done
      else 
	if [ "$1" = "--push" ]; 
	then
	   echo ""
	   echo "[INFO] ftp2git push from local to ftp server"
		for file in $( find $CONFIG_DIR -type f -name '*.site.conf' )
	      do
		echo "[INFO] Loading '"$file"'"

                source "$file"

                # build params
                ARCHIVE=$HOME"/"$ARCHIVE_LOCATION
                ARCHIVE_MIRROR=$ARCHIVE"/"$MIRROR_FOLDER_NAME
		LFTP_COMMS="$file.lftp.push"

		if [ -f "$LFTP_COMMS" ]
		then
        		if [ ! -r "$LFTP_COMMS" ]
			then
                		echo "[ERROR] LFTP command file at "$LFTP_COMMS" is not readable."
				echo "*** Please fix and re-run."
				exit 1
			fi
			# fall through - file is readable and exists.
		else
			echo "[ERROR] LFTP command file does not exist yet."
			echo "*** Please run again with '--build' to create settings."
			exit 1
		fi
		
		### ---------- do check first ----------###		
		pushd . >> /dev/null
		cd $ARCHIVE_MIRROR
		git_check_changes

		if [ $? -eq 1 ]; then
			echo "Something changed"
			
			### ------------- git --------------------------###
			git add -A
			git commit -a -m "Auto push to ftp: `date +%F-%T`"
			git push
			
			### ------------- ftp push ----------------------###
			echo "$LFTP_COMMS"
      			lftp -f $LFTP_COMMS
	
			echo "[INFO] Done FTP."
		else
			echo "[INFO] Nothing to be pushed"
		fi
		
		popd >> /dev/null		
	      done
	else #if [ "$1" = "--push" ]; then
		echo ""
		echo "[HELP] Noting to do"
	fi
      fi
	echo "[INFO] ftp2git work complete."
	echo ""
fi
exit 0

