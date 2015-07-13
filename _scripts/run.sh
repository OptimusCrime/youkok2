#!/bin/bash

# Default port
PORT="1234"

# Check if another port was supplied
if [ $# -ne 0 ]
    then
        PORT="$1"
fi

# Create command to run
CMD="php -S localhost:$PORT -t . cli/route.php $PORT"

# Eval the command and run
eval ${CMD}