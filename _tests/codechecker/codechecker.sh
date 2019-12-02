#!/bin/sh

if [ ! -f "target/codequalitychecker-11.0.jar" ]; then
	cd "$(dirname "$0")"
fi

java -jar "target/codequalitychecker-11.0.jar"
