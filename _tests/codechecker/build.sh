#!/bin/sh

javac -d target/classes src/main/java/com/composrfoundation/codequalitychecker/*.java -Xlint:unchecked
jar cfe target/codequalitychecker-11.0.jar com.composrfoundation.codequalitychecker.Main -C target/classes .
