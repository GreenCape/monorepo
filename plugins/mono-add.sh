#!/usr/bin/env bash

function usage {
  cat <<-EOT
Usage: mono add [--name=<name>] [--branch=<branch>] <directory> <repository>
   or: mono add -h|--help

Create the <directory> subtree by importing its contents from the given
<repository> and remote <branch>. A new commit is created
automatically, joining the imported project’s history with your own.
With --squash, imports only a single commit from the subproject, rather
than its entire history.

Arguments:

    <directory>   The directory to contain the imported subproject.
    <repository>  The repository url or directory name of the subproject.

Options:

  -n|--name=<name>       The name of the subproject. Defaults to the repository
                         name of the imported subproject.
  -b|--branch=<branch>   A valid remote ref
  --squash               Merge subtree changes as a single commit.
  -h|--help              Show this help text.

EOT
}

POSITIONAL=()
while [[ $# -gt 0 ]]; do
  case $1 in
  -n | --name)
    REMOTE_NAME="$2"
    shift # past argument
    shift # past value
    ;;
  -n=* | --name=*)
    REMOTE_NAME="${1#*=}"
    shift # past argument=value
    ;;
  -b | --branch)
    BRANCH="$2"
    shift # past argument
    shift # past value
    ;;
  -b=* | --branch=*)
    BRANCH="${1#*=}"
    shift # past argument=value
    ;;
  --squash)
    OPT_SQUASH=" --squash"
    shift # past argument
    ;;
  -h | --help)
    usage
    exit 0
    ;;
  -*) # unknown option
    echo "Unknown option »$1«"
    usage
    exit 1
    ;;
  *)
    POSITIONAL+=("$1") # save it in an array for later
    shift              # past argument
    ;;
  esac
done

DIRECTORY=${POSITIONAL[0]}
REMOTE_URL=${POSITIONAL[1]}
BRANCH=${BRANCH:-$(git rev-parse --abbrev-ref HEAD)}
if [[ ${#REMOTE_NAME} -eq 0 ]]; then
  REMOTE_NAME=$(basename "${REMOTE_URL/%.git/}")
fi
echo "Remote name: $REMOTE_NAME"
echo "Branch:      $BRANCH"
echo "Directory:   $DIRECTORY"
echo "REMOTE_URL:  $REMOTE_URL"
echo "Squash:      $OPT_SQUASH"

# Check if REMOTE_NAME already exists, then
git remote add "$REMOTE_NAME" "$REMOTE_URL"

# Get the default branch of the remote
DEFAULT_BRANCH=$(git remote show "$REMOTE_NAME" | grep 'HEAD branch' | cut -d' ' -f5)
echo "Default branch: $DEFAULT_BRANCH"

echo "git subtree add$OPT_OPT_SQUASH --prefix=$DIRECTORY $REMOTE_NAME $BRANCH"
