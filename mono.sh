#!/usr/bin/env bash

MONO_FULL_NAME="MonoRepo Tool"
MONO_VERSION="0.0.1"
MONO_AUTHOR="GreenCape, Niels Braczek"
MONO_COPYRIGHT_YEAR="2021"
MONO_PLUGIN_DIR=${MONO_PLUGIN_DIR:-./plugins}

function hello {
  echo "$MONO_FULL_NAME $MONO_VERSION by $MONO_AUTHOR"
  echo ""
}

function version {
  echo "$MONO_FULL_NAME $MONO_VERSION"
  echo "(C)$MONO_COPYRIGHT_YEAR $MONO_AUTHOR"
}

function usage {
  hello
  cat <<EOT
Usage: mono [-h|--help] [-v|--version]
   or: mono <command> [<arguments>]

'mono help -a' and 'mono help -g' list available commands and some
instructions on mono concepts respectively. Use 'mono help <command>'
or 'mono help <concept>' to learn more about a specific command or concept.
EOT
}

case $1 in
  -h | --help)
    usage
    exit 0
    ;;
  -v | --version)
    version
    exit 0
    ;;
  *)
    MONO_COMMAND=$1
    ;;
esac

MONO_PLUGIN_FILE="$MONO_PLUGIN_DIR/mono-$MONO_COMMAND.sh"

if [ ! -f "$MONO_PLUGIN_FILE" ]; then
  # Plugin does not exist
  echo "Command »$MONO_COMMAND« not found";
  usage
  exit 1
fi

hello
shift
source "$MONO_PLUGIN_FILE"
