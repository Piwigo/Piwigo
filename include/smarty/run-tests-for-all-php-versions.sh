#!/bin/bash
Help()
{
   # Display Help
   echo "Runs PHPUnit tests for all PHP versions supported by this version of Smarty."
   echo
   echo "Syntax: $0 [-e|h]"
   echo "options:"
   echo "e     Exclude a group of unit tests, e.g. -e 'slow'"
   echo "h     Print this Help."
   echo
}

Exclude=""

# Get the options
while getopts ":he:" option; do
   case $option in
      e) # Exclude
        echo $OPTARG
         Exclude=$OPTARG;;
      h) # display Help
         Help
         exit;;
     \?) # Invalid option
         echo "Error: Invalid option"
         exit;;
   esac
done

if [ -z $Exclude ];
then
  Entrypoint="./run-tests.sh"
else
   Entrypoint="./run-tests.sh $Exclude"
fi

# Runs tests for all supported PHP versions
docker-compose run --entrypoint "$Entrypoint" php71 && \
docker-compose run --entrypoint "$Entrypoint" php72 && \
docker-compose run --entrypoint "$Entrypoint" php73 && \
docker-compose run --entrypoint "$Entrypoint" php74 && \
docker-compose run --entrypoint "$Entrypoint" php80 && \
docker-compose run --entrypoint "$Entrypoint" php81
