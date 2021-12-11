#!/usr/bin/env bash

MONO_PLUGIN_DIR=./plugins
MONO_COMMAND=$1
MONO_PLUGIN_FILE="$MONO_PLUGIN_DIR/mono-$MONO_COMMAND.sh"

POSITIONAL=()
while [[ $# -gt 0 ]]; do
  key="$1"

  case $key in
  -e | --extension)
    EXTENSION="$2"
    shift # past argument
    shift # past value
    ;;
  -e=* | --extension=*)
    EXTENSION="${i#*=}"
    shift # past argument=value
    ;;
  -s | --searchpath)
    SEARCHPATH="$2"
    shift # past argument
    shift # past value
    ;;
  -s=* | --searchpath=*)
    SEARCHPATH="${i#*=}"
    shift # past argument=value
    ;;
  -l | --lib)
    LIBPATH="$2"
    shift # past argument
    shift # past value
    ;;
  -l=* | --lib=*)
    LIBPATH="${i#*=}"
    shift # past argument=value
    ;;
  --default)
    DEFAULT=YES
    shift # past argument
    ;;
  *) # unknown option
    POSITIONAL+=("$1") # save it in an array for later
    shift              # past argument
    ;;
  esac
done
